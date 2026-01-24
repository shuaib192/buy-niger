<?php

namespace App\Http\Controllers;

use App\Models\AIChatSession;
use App\Models\AIChatMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Notification;
use App\Services\AI\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    /**
     * Open or resume a chat session.
     */
    public function open()
    {
        $user = Auth::user();
        $role = $this->getUserRole($user);
        
        $session = AIChatSession::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            $session = AIChatSession::create([
                'user_id' => $user->id,
                'session_type' => 'customer_support',
                'status' => 'active',
                'context' => [
                    'platform' => 'BuyNiger',
                    'user_role' => $role,
                    'user_name' => $user->name
                ]
            ]);
        }

        $messages = AIChatMessage::where('session_id', $session->id)
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get();

        return response()->json([
            'session_id' => $session->id,
            'messages' => $messages
        ]);
    }

    /**
     * Send a message - Role-Aware & Platform-Intelligent
     */
    public function send(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:ai_chat_sessions,id',
            'message' => 'required|string|max:1000'
        ]);

        $session = AIChatSession::findOrFail($request->session_id);
        $user = Auth::user();

        if ($session->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        AIChatMessage::create([
            'session_id' => $session->id,
            'role' => 'user',
            'content' => $request->message
        ]);

        // Check for actionable intents
        $actionResult = $this->detectAndExecuteAction($request->message, $user);
        
        if ($actionResult) {
            $aiMessage = AIChatMessage::create([
                'session_id' => $session->id,
                'role' => 'assistant',
                'content' => $actionResult['response'],
                'metadata' => ['action' => $actionResult['action']]
            ]);

            $session->increment('message_count');

            return response()->json([
                'success' => true,
                'message' => $aiMessage,
                'action' => $actionResult['action']
            ]);
        }

        // AI Response with full platform context
        try {
            $aiService = new AIService();
            $role = $this->getUserRole($user);
            $context = $this->buildFullContext($user, $role);
            
            $systemPrompt = "You are BuyNiger AI, the intelligent assistant for BuyNiger e-commerce platform.

PLATFORM KNOWLEDGE:
- BuyNiger is a multi-vendor e-commerce platform in Nigeria
- Supports multiple user roles: Customers, Vendors, Admins, Super Admins
- Features: Product listings, Order management, Vendor stores, Payments (Paystack/Flutterwave), Disputes

CURRENT USER:
{$context}

CAPABILITIES BY ROLE:
- **Customers**: Browse products, place orders, track orders, manage wishlist, message vendors
- **Vendors**: Manage products, process orders, view analytics, request payouts, handle messages
- **Admins**: Moderate products, manage users, handle disputes, view platform stats
- **Super Admins**: Full platform control, AI settings, system configuration, payment gateways

Be helpful, concise, and guide users on how to accomplish tasks. Keep responses under 100 words.";

            $response = $aiService->generateText(
                $systemPrompt . "\n\nUser: " . $request->message,
                'CRO',
                'chatbot_response'
            );

            $aiMessage = AIChatMessage::create([
                'session_id' => $session->id,
                'role' => 'assistant',
                'content' => $response
            ]);

            $session->increment('message_count');

            return response()->json([
                'success' => true,
                'message' => $aiMessage
            ]);

        } catch (\Exception $e) {
            $fallback = AIChatMessage::create([
                'session_id' => $session->id,
                'role' => 'assistant',
                'content' => $this->getRoleFallback($user)
            ]);

            return response()->json([
                'success' => true,
                'message' => $fallback
            ]);
        }
    }

    /**
     * Detect and execute platform actions based on user role.
     */
    private function detectAndExecuteAction(string $message, $user): ?array
    {
        $message = strtolower($message);
        $role = $this->getUserRole($user);

        // Universal: Orders
        if (preg_match('/(my orders?|order status|check.*(order|delivery)|track|pending orders)/i', $message)) {
            if ($role === 'vendor') {
                return $this->getVendorOrders($user);
            }
            return $this->getCustomerOrders($user);
        }

        // Universal: Notifications
        if (preg_match('/(notification|alert|what.*new)/i', $message)) {
            return $this->getNotifications($user);
        }

        // Vendor: Store Stats
        if ($role === 'vendor' && preg_match('/(stats|analytics|revenue|sales|store stats|my store)/i', $message)) {
            return $this->getVendorStats($user);
        }

        // Admin/SuperAdmin: Platform Overview
        if (in_array($role, ['admin', 'superadmin']) && preg_match('/(overview|platform|dashboard|stats)/i', $message)) {
            return $this->getPlatformOverview();
        }

        // Admin/SuperAdmin: Pending Vendors
        if (in_array($role, ['admin', 'superadmin']) && preg_match('/(pending.*vendor|vendor.*pending|approve.*vendor)/i', $message)) {
            return $this->getPendingVendors();
        }

        // Customer: Search Products
        if ($role === 'customer' && preg_match('/(?:find|search|looking for|want to buy|show me)\s+(.+)/i', $message, $matches)) {
            return $this->searchProducts($matches[1]);
        }

        // Help
        if (preg_match('/(help|support|what can you do)/i', $message)) {
            return $this->getRoleHelp($role);
        }

        return null;
    }

    private function getUserRole($user): string
    {
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) return 'superadmin';
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) return 'admin';
        if (method_exists($user, 'isVendor') && $user->isVendor()) return 'vendor';
        return 'customer';
    }

    private function buildFullContext($user, $role): string
    {
        $context = "Name: {$user->name}\nRole: " . ucfirst($role) . "\n";
        
        if ($role === 'vendor' && $user->vendor) {
            $context .= "Store: {$user->vendor->store_name}\n";
            $context .= "Pending Orders: " . Order::whereHas('items', fn($q) => $q->where('vendor_id', $user->vendor->id))->where('status', 'pending')->count() . "\n";
        } elseif ($role === 'customer') {
            $context .= "Total Orders: " . Order::where('user_id', $user->id)->count() . "\n";
        } elseif (in_array($role, ['admin', 'superadmin'])) {
            $context .= "Platform Users: " . User::count() . "\n";
            $context .= "Pending Vendors: " . Vendor::where('status', 'pending')->count() . "\n";
        }
        
        return $context;
    }

    private function getCustomerOrders($user): array
    {
        $orders = Order::where('user_id', $user->id)->latest()->take(3)->get();
        
        if ($orders->isEmpty()) {
            return ['action' => 'orders', 'response' => "You don't have any orders yet. Start shopping at /shop!"];
        }

        $response = "📦 **Your Recent Orders:**\n\n";
        foreach ($orders as $order) {
            $emoji = ['pending' => '🕐', 'processing' => '⚙️', 'shipped' => '🚚', 'delivered' => '✅'][$order->status] ?? '📋';
            $response .= "{$emoji} **#{$order->order_number}** - ₦" . number_format($order->total) . " ({$order->status})\n";
        }
        
        return ['action' => 'orders', 'response' => $response];
    }

    private function getVendorOrders($user): array
    {
        $vendorId = $user->vendor->id ?? null;
        if (!$vendorId) return ['action' => 'orders', 'response' => 'Vendor profile not found.'];
        
        $pending = Order::whereHas('items', fn($q) => $q->where('vendor_id', $vendorId))
            ->where('status', 'pending')
            ->count();
        
        $processing = Order::whereHas('items', fn($q) => $q->where('vendor_id', $vendorId))
            ->where('status', 'processing')
            ->count();

        return [
            'action' => 'orders',
            'response' => "📦 **Your Store Orders:**\n\n🕐 Pending: **{$pending}**\n⚙️ Processing: **{$processing}**\n\nManage orders at /vendor/orders"
        ];
    }

    private function getVendorStats($user): array
    {
        $vendor = $user->vendor;
        if (!$vendor) return ['action' => 'stats', 'response' => 'Vendor profile not found.'];
        
        $products = Product::where('vendor_id', $vendor->id)->count();
        $revenue = Order::whereHas('items', fn($q) => $q->where('vendor_id', $vendor->id))
            ->where('payment_status', 'paid')
            ->sum('total');

        return [
            'action' => 'stats',
            'response' => "📊 **{$vendor->store_name} Stats:**\n\n🛍️ Products: **{$products}**\n💰 Revenue: **₦" . number_format($revenue) . "**\n⭐ Rating: **" . number_format($vendor->rating ?? 0, 1) . "/5**\n\nFull analytics at /vendor/analytics"
        ];
    }

    private function getPlatformOverview(): array
    {
        $users = User::count();
        $vendors = Vendor::where('status', 'approved')->count();
        $orders = Order::count();
        $revenue = Order::where('payment_status', 'paid')->sum('total');

        return [
            'action' => 'overview',
            'response' => "📊 **Platform Overview:**\n\n👥 Users: **{$users}**\n🏪 Vendors: **{$vendors}**\n📦 Orders: **{$orders}**\n💰 Revenue: **₦" . number_format($revenue) . "**"
        ];
    }

    private function getPendingVendors(): array
    {
        $pending = Vendor::where('status', 'pending')->with('user')->take(5)->get();
        
        if ($pending->isEmpty()) {
            return ['action' => 'vendors', 'response' => '✅ No pending vendor applications!'];
        }

        $response = "👥 **Pending Vendors ({$pending->count()}):**\n\n";
        foreach ($pending as $v) {
            $response .= "• **{$v->store_name}** ({$v->user->name})\n";
        }
        $response .= "\nReview at /superadmin/vendors";
        
        return ['action' => 'vendors', 'response' => $response];
    }

    private function getNotifications($user): array
    {
        $notifications = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->take(5)
            ->get();

        if ($notifications->isEmpty()) {
            return ['action' => 'notifications', 'response' => '🔔 No new notifications!'];
        }

        $response = "🔔 **Notifications:**\n\n";
        foreach ($notifications as $n) {
            $response .= "• {$n->title}\n";
        }
        
        return ['action' => 'notifications', 'response' => $response];
    }

    private function searchProducts($query): array
    {
        $products = Product::where('status', 'active')
            ->where('name', 'like', "%{$query}%")
            ->take(5)
            ->get();

        if ($products->isEmpty()) {
            return ['action' => 'search', 'response' => "No products found for \"{$query}\". Try /shop?search={$query}"];
        }

        $response = "🛍️ **Products matching \"{$query}\":**\n\n";
        foreach ($products as $p) {
            $response .= "• **{$p->name}** - ₦" . number_format($p->price) . "\n";
        }
        
        return ['action' => 'search', 'response' => $response];
    }

    private function getRoleHelp($role): array
    {
        $help = [
            'customer' => "🛒 **Customer Help:**\n• \"Check my orders\"\n• \"Find [product]\"\n• \"Track delivery\"\n• \"View notifications\"",
            'vendor' => "🏪 **Vendor Help:**\n• \"Store stats\"\n• \"Pending orders\"\n• \"My products\"\n• \"Request payout\"",
            'admin' => "👤 **Admin Help:**\n• \"Platform overview\"\n• \"Pending vendors\"\n• \"Recent disputes\"\n• \"User stats\"",
            'superadmin' => "⚙️ **Super Admin Help:**\n• \"Platform overview\"\n• \"Pending vendors\"\n• \"AI settings\"\n• \"System health\""
        ];
        
        return ['action' => 'help', 'response' => $help[$role] ?? $help['customer']];
    }

    private function getRoleFallback($user): string
    {
        $role = $this->getUserRole($user);
        $messages = [
            'customer' => "I can help you check orders, find products, or get support. What do you need?",
            'vendor' => "I can show you store stats, pending orders, or help with products. What would you like?",
            'admin' => "I can show platform overview, pending vendors, or recent activity. How can I help?",
            'superadmin' => "I can help with platform stats, vendor approvals, or system settings. What do you need?"
        ];
        return $messages[$role] ?? $messages['customer'];
    }

    public function history($sessionId)
    {
        $session = AIChatSession::findOrFail($sessionId);
        if ($session->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json(['messages' => AIChatMessage::where('session_id', $sessionId)->orderBy('created_at')->get()]);
    }
}
