<?php
/**
 * BuyNiger AI Action Helper - Step-by-Step Wizard Flow
 * 
 * Implements conversational wizard for product creation
 * NO markdown formatting - clean text responses
 */

namespace App\Services\AI;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\VendorPayout;
use App\Models\AIChatSession;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AIActionHelper
{
    protected $user;
    protected $role;
    protected $vendor;
    protected $session;

    public function __construct(User $user, ?AIChatSession $session = null)
    {
        $this->user = $user;
        $this->role = $this->detectRole();
        $this->vendor = $user->vendor ?? null;
        $this->session = $session;
    }

    protected function detectRole(): string
    {
        if (method_exists($this->user, 'isSuperAdmin') && $this->user->isSuperAdmin()) return 'superadmin';
        if (method_exists($this->user, 'isAdmin') && $this->user->isAdmin()) return 'admin';
        if (method_exists($this->user, 'isVendor') && $this->user->isVendor()) return 'vendor';
        return 'customer';
    }

    /**
     * Get current wizard state from session
     */
    protected function getWizardState(): ?array
    {
        if (!$this->session) return null;
        return $this->session->context['wizard'] ?? null;
    }

    /**
     * Set wizard state in session
     */
    protected function setWizardState(array $state): void
    {
        if (!$this->session) return;
        $ctx = $this->session->context ?? [];
        $ctx['wizard'] = $state;
        $this->session->update(['context' => $ctx]);
    }

    /**
     * Clear wizard state
     */
    protected function clearWizardState(): void
    {
        if (!$this->session) return;
        $ctx = $this->session->context ?? [];
        unset($ctx['wizard']);
        $this->session->update(['context' => $ctx]);
    }

    /**
     * Parse user intent and execute action
     */
    public function parseAndExecute(string $message): ?array
    {
        $message = strtolower(trim($message));

        // Check if we're in a wizard flow
        $wizard = $this->getWizardState();
        if ($wizard) {
            return $this->continueWizard($wizard, $message);
        }

        // Vendor Actions
        if ($this->role === 'vendor') {
            // PRODUCT MANAGEMENT
            if ($this->matchesIntent($message, ['add product', 'create product', 'new product', 'add a product'])) {
                return $this->startProductWizard();
            }
            if ($this->matchesIntent($message, ['list product', 'show product', 'my product', 'all product', 'view product'])) {
                return $this->listProducts();
            }
            if ($this->matchesIntent($message, ['update product', 'edit product', 'change product'])) {
                return $this->handleUpdateProduct($message);
            }
            if ($this->matchesIntent($message, ['delete product', 'remove product'])) {
                return $this->handleDeleteProduct($message);
            }
            if ($this->matchesIntent($message, ['duplicate product', 'copy product', 'clone product'])) {
                return $this->duplicateProduct($message);
            }
            if ($this->matchesIntent($message, ['feature product', 'make featured'])) {
                return $this->featureProduct($message);
            }
            if ($this->matchesIntent($message, ['unfeature product', 'remove featured'])) {
                return $this->unfeatureProduct($message);
            }
            if ($this->matchesIntent($message, ['activate product', 'enable product'])) {
                return $this->activateProduct($message);
            }
            if ($this->matchesIntent($message, ['deactivate product', 'disable product', 'hide product'])) {
                return $this->deactivateProduct($message);
            }
            if ($this->matchesIntent($message, ['low stock', 'out of stock', 'stock alert'])) {
                return $this->showLowStock();
            }
            if ($this->matchesIntent($message, ['best seller', 'top product', 'top selling'])) {
                return $this->showBestSellers();
            }
            if ($this->matchesIntent($message, ['search product'])) {
                return $this->searchProducts($message);
            }

            // ORDER MANAGEMENT
            if ($this->matchesIntent($message, ['list order', 'show order', 'my order', 'all order', 'view order'])) {
                return $this->listOrders();
            }
            if ($this->matchesIntent($message, ['pending order'])) {
                return $this->listOrdersByStatus('pending');
            }
            if ($this->matchesIntent($message, ['processing order'])) {
                return $this->listOrdersByStatus('processing');
            }
            if ($this->matchesIntent($message, ['shipped order'])) {
                return $this->listOrdersByStatus('shipped');
            }
            if ($this->matchesIntent($message, ['delivered order', 'completed order'])) {
                return $this->listOrdersByStatus('delivered');
            }
            if ($this->matchesIntent($message, ['update order', 'ship order', 'mark as shipped', 'process order', 'mark order', 'deliver order'])) {
                return $this->handleUpdateOrderStatus($message);
            }
            if ($this->matchesIntent($message, ['order detail', 'view order #'])) {
                return $this->viewOrderDetails($message);
            }
            if ($this->matchesIntent($message, ['today order', 'order today'])) {
                return $this->showTodayOrders();
            }

            // FINANCE & PAYOUTS
            if ($this->matchesIntent($message, ['my balance', 'check balance', 'available balance', 'wallet'])) {
                return $this->checkBalance();
            }
            if ($this->matchesIntent($message, ['request payout', 'withdraw', 'cash out'])) {
                return $this->handlePayoutRequest($message);
            }
            if ($this->matchesIntent($message, ['payout history', 'my payout', 'withdrawal history'])) {
                return $this->showPayoutHistory();
            }
            if ($this->matchesIntent($message, ['total earning', 'total revenue', 'how much earned'])) {
                return $this->showTotalEarnings();
            }
            if ($this->matchesIntent($message, ['latest sale', 'recent sale', 'what did i sell', 'my sales', 'show sales'])) {
                return $this->getRecentSales();
            }

            // ANALYTICS & STATS
            if ($this->matchesIntent($message, ['store stat', 'my stat', 'dashboard', 'overview', 'summary'])) {
                return $this->showStoreStats();
            }
            if ($this->matchesIntent($message, ['sales today', 'today sale', 'revenue today'])) {
                return $this->showTodaySales();
            }
            if ($this->matchesIntent($message, ['this week', 'weekly sale', 'week revenue'])) {
                return $this->showWeeklySales();
            }
            if ($this->matchesIntent($message, ['this month', 'monthly sale', 'month revenue'])) {
                return $this->showMonthlySales();
            }

            // COUPONS
            if ($this->matchesIntent($message, ['list coupon', 'my coupon', 'show coupon', 'all coupon'])) {
                return $this->listCoupons();
            }
            if ($this->matchesIntent($message, ['create coupon', 'add coupon', 'new coupon'])) {
                return $this->startCouponWizard();
            }
            if ($this->matchesIntent($message, ['delete coupon', 'remove coupon'])) {
                return $this->deleteCoupon($message);
            }

            // STORE INFO
            if ($this->matchesIntent($message, ['store info', 'my store', 'store detail', 'shop info'])) {
                return $this->showStoreInfo();
            }
            if ($this->matchesIntent($message, ['bank detail', 'my bank', 'payment detail'])) {
                return $this->showBankDetails();
            }
        }

        // Admin Actions
        if (in_array($this->role, ['admin', 'superadmin'])) {
            // VENDOR MANAGEMENT
            if ($this->matchesIntent($message, ['list vendor', 'all vendor', 'show vendor'])) {
                return $this->listVendors();
            }
            if ($this->matchesIntent($message, ['pending vendor', 'vendor pending'])) {
                return $this->listPendingVendors();
            }
            if ($this->matchesIntent($message, ['approve vendor', 'accept vendor'])) {
                return $this->handleApproveVendor($message);
            }
            if ($this->matchesIntent($message, ['reject vendor', 'decline vendor'])) {
                return $this->handleRejectVendor($message);
            }
            if ($this->matchesIntent($message, ['disable vendor', 'suspend vendor'])) {
                return $this->disableVendor($message);
            }
            if ($this->matchesIntent($message, ['enable vendor', 'activate vendor'])) {
                return $this->enableVendor($message);
            }

            // USER MANAGEMENT
            if ($this->matchesIntent($message, ['list user', 'all user', 'show user'])) {
                return $this->listUsers();
            }
            if ($this->matchesIntent($message, ['search user'])) {
                return $this->searchUsers($message);
            }
            if ($this->matchesIntent($message, ['disable user', 'suspend user', 'ban user'])) {
                return $this->disableUser($message);
            }
            if ($this->matchesIntent($message, ['enable user', 'activate user', 'unban user'])) {
                return $this->enableUser($message);
            }
            if ($this->matchesIntent($message, ['new user today', 'user today', 'today registration'])) {
                return $this->showNewUsersToday();
            }

            // PAYOUT MANAGEMENT
            if ($this->matchesIntent($message, ['pending payout', 'payout pending'])) {
                return $this->listPendingPayouts();
            }
            if ($this->matchesIntent($message, ['process payout', 'approve payout', 'complete payout'])) {
                return $this->handleProcessPayout($message);
            }
            if ($this->matchesIntent($message, ['reject payout', 'decline payout'])) {
                return $this->rejectPayout($message);
            }
            if ($this->matchesIntent($message, ['payout history', 'all payout'])) {
                return $this->showAllPayouts();
            }

            // PRODUCT MODERATION
            if ($this->matchesIntent($message, ['pending product', 'product pending', 'product approval'])) {
                return $this->listPendingProducts();
            }
            if ($this->matchesIntent($message, ['approve product'])) {
                return $this->approveProduct($message);
            }
            if ($this->matchesIntent($message, ['reject product'])) {
                return $this->rejectProduct($message);
            }
            if ($this->matchesIntent($message, ['feature product'])) {
                return $this->adminFeatureProduct($message);
            }

            // PLATFORM STATS
            if ($this->matchesIntent($message, ['platform stat', 'dashboard', 'overview', 'summary'])) {
                return $this->showPlatformStats();
            }
            if ($this->matchesIntent($message, ['user count', 'total user', 'how many user'])) {
                return $this->showUserCount();
            }
            if ($this->matchesIntent($message, ['vendor count', 'total vendor', 'how many vendor'])) {
                return $this->showVendorCount();
            }
            if ($this->matchesIntent($message, ['product count', 'total product', 'how many product'])) {
                return $this->showProductCount();
            }
            if ($this->matchesIntent($message, ['order count', 'total order', 'how many order'])) {
                return $this->showOrderCount();
            }
            if ($this->matchesIntent($message, ['total revenue', 'platform revenue', 'all revenue'])) {
                return $this->showTotalRevenue();
            }
            if ($this->matchesIntent($message, ['today revenue', 'revenue today', 'today sale'])) {
                return $this->showTodayRevenue();
            }

            // CATEGORIES
            if ($this->matchesIntent($message, ['list categor', 'all categor', 'show categor'])) {
                return $this->listCategories();
            }
            if ($this->matchesIntent($message, ['add categor', 'create categor', 'new categor'])) {
                return $this->addCategory($message);
            }

            // SYSTEM
            if ($this->matchesIntent($message, ['clear cache', 'refresh cache'])) {
                return $this->clearCache();
            }
        }

        // Customer Actions
        if ($this->role === 'customer') {
            // ORDERS
            if ($this->matchesIntent($message, ['my order', 'list order', 'show order', 'order history'])) {
                return $this->listCustomerOrders();
            }
            if ($this->matchesIntent($message, ['track order', 'where is my order', 'order status'])) {
                return $this->trackOrder($message);
            }
            if ($this->matchesIntent($message, ['cancel order', 'cancel my order'])) {
                return $this->handleCancelOrder($message);
            }
            if ($this->matchesIntent($message, ['reorder', 'order again', 'buy again'])) {
                return $this->reorderGuidance($message);
            }

            // SHOPPING
            if ($this->matchesIntent($message, ['search product', 'find product', 'looking for'])) {
                return $this->searchProductsCustomer($message);
            }
            if ($this->matchesIntent($message, ['featured product', 'popular product', 'best product'])) {
                return $this->showFeaturedProducts();
            }
            if ($this->matchesIntent($message, ['new arrival', 'latest product', 'new product'])) {
                return $this->showNewArrivals();
            }
            if ($this->matchesIntent($message, ['cheap', 'affordable', 'budget', 'under'])) {
                return $this->showCheapProducts($message);
            }

            // WISHLIST
            if ($this->matchesIntent($message, ['my wishlist', 'show wishlist', 'wishlist'])) {
                return $this->showWishlist();
            }

            // PROFILE
            if ($this->matchesIntent($message, ['my profile', 'my account', 'account info'])) {
                return $this->showCustomerProfile();
            }
            if ($this->matchesIntent($message, ['my address', 'address', 'delivery address'])) {
                return $this->showAddresses();
            }

            // SUPPORT
            if ($this->matchesIntent($message, ['help', 'support', 'contact'])) {
                return $this->showHelp();
            }
        }

        return null;
    }

    protected function matchesIntent(string $message, array $phrases): bool
    {
        foreach ($phrases as $phrase) {
            if (str_contains($message, $phrase)) return true;
        }
        return false;
    }

    // ==================== PRODUCT WIZARD - STEP BY STEP ====================

    /**
     * Start the product creation wizard
     */
    protected function startProductWizard(): array
    {
        if (!$this->vendor) {
            return ['success' => false, 'message' => 'You need a vendor profile to add products.'];
        }

        $this->setWizardState([
            'type' => 'add_product',
            'step' => 1,
            'data' => []
        ]);

        return [
            'success' => true,
            'action' => 'wizard',
            'message' => "Alright, let's add a new product to your store!\n\nStep 1 of 5: What is the name of your product?"
        ];
    }

    /**
     * Continue the wizard based on current step
     */
    protected function continueWizard(array $wizard, string $input): array
    {
        if ($wizard['type'] === 'add_product') {
            return $this->continueProductWizard($wizard, $input);
        }

        // Unknown wizard type
        $this->clearWizardState();
        return null;
    }

    /**
     * Handle product wizard steps
     */
    protected function continueProductWizard(array $wizard, string $input): array
    {
        $step = $wizard['step'];
        $data = $wizard['data'];

        // Handle cancel
        if (str_contains($input, 'cancel') || str_contains($input, 'stop') || str_contains($input, 'nevermind')) {
            $this->clearWizardState();
            return [
                'success' => true,
                'message' => "No problem, I've cancelled the product creation. Let me know if you need anything else!"
            ];
        }

        switch ($step) {
            case 1: // Name
                $name = ucwords(trim($input));
                if (strlen($name) < 2) {
                    return [
                        'success' => true,
                        'message' => "That name is too short. Please enter a proper product name (at least 2 characters)."
                    ];
                }
                $data['name'] = $name;
                $wizard['data'] = $data;
                $wizard['step'] = 2;
                $this->setWizardState($wizard);
                
                return [
                    'success' => true,
                    'action' => 'wizard',
                    'message' => "Great! The product is called \"{$name}\".\n\nStep 2 of 5: What is the price in Naira? (just enter the number, e.g., 25000)"
                ];

            case 2: // Price
                $price = (float) preg_replace('/[^0-9.]/', '', $input);
                if ($price <= 0) {
                    return [
                        'success' => true,
                        'message' => "Please enter a valid price greater than 0. Just type the number, like 15000 or 250000."
                    ];
                }
                $data['price'] = $price;
                $wizard['data'] = $data;
                $wizard['step'] = 3;
                $this->setWizardState($wizard);
                
                return [
                    'success' => true,
                    'action' => 'wizard',
                    'message' => "Price set to N" . number_format($price) . ".\n\nStep 3 of 5: How many do you have in stock? (enter a number)"
                ];

            case 3: // Quantity
                $qty = (int) preg_replace('/[^0-9]/', '', $input);
                if ($qty < 1) $qty = 1;
                $data['quantity'] = $qty;
                $wizard['data'] = $data;
                $wizard['step'] = 4;
                $this->setWizardState($wizard);
                
                // Get categories for selection
                $categories = Category::take(10)->pluck('name')->toArray();
                $catList = implode(', ', $categories);
                
                return [
                    'success' => true,
                    'action' => 'wizard',
                    'message' => "Got it, {$qty} in stock.\n\nStep 4 of 5: What category does this product belong to?\n\nAvailable categories: {$catList}\n\n(Just type the category name)"
                ];

            case 4: // Category
                $catName = ucwords(trim($input));
                $category = Category::where('name', 'like', '%' . $catName . '%')->first();
                
                if (!$category) {
                    $category = Category::first();
                    $data['category_id'] = $category?->id ?? 1;
                    $data['category_name'] = $category?->name ?? 'General';
                } else {
                    $data['category_id'] = $category->id;
                    $data['category_name'] = $category->name;
                }
                
                $wizard['data'] = $data;
                $wizard['step'] = 5;
                $this->setWizardState($wizard);
                
                return [
                    'success' => true,
                    'action' => 'wizard',
                    'message' => "Category set to \"{$data['category_name']}\".\n\nStep 5 of 5 (Optional): Enter a short description for this product, or type \"skip\" to use default."
                ];

            case 5: // Description
                $desc = trim($input);
                if (str_contains(strtolower($desc), 'skip') || strlen($desc) < 3) {
                    $data['description'] = "Quality product from " . ($this->vendor->store_name ?? 'our store');
                } else {
                    $data['description'] = ucfirst($desc);
                }
                
                // Create the product
                try {
                    $product = Product::create([
                        'vendor_id' => $this->vendor->id,
                        'category_id' => $data['category_id'] ?? 1,
                        'name' => $data['name'],
                        'slug' => Str::slug($data['name']) . '-' . Str::random(5),
                        'description' => $data['description'],
                        'price' => $data['price'],
                        'quantity' => $data['quantity'],
                        'status' => 'active',
                    ]);

                    // Store for reference
                    $ctx = $this->session->context ?? [];
                    $ctx['last_product_id'] = $product->id;
                    $ctx['last_product_name'] = $product->name;
                    unset($ctx['wizard']);
                    $this->session->update(['context' => $ctx]);

                    return [
                        'success' => true,
                        'action' => 'product_created',
                        'message' => "Done! Your product has been created successfully!\n\n" .
                            "Product: {$product->name}\n" .
                            "Price: N" . number_format($product->price) . "\n" .
                            "Quantity: {$product->quantity}\n" .
                            "Category: {$data['category_name']}\n" .
                            "Status: Active\n\n" .
                            "Product ID: #{$product->id}\n\n" .
                            "You can view and edit it at: /vendor/products/{$product->id}/edit\n\n" .
                            "What else can I help you with?"
                    ];

                } catch (\Exception $e) {
                    $this->clearWizardState();
                    Log::error('Product creation error: ' . $e->getMessage());
                    return [
                        'success' => false,
                        'message' => "Sorry, there was an error creating the product: " . $e->getMessage()
                    ];
                }
        }

        // Unknown step
        $this->clearWizardState();
        return null;
    }

    // ==================== LIST & VIEW ACTIONS ====================

    /**
     * List all products for vendor
     */
    protected function listProducts(): array
    {
        if (!$this->vendor) {
            return ['success' => false, 'message' => 'Vendor profile not found.'];
        }

        $products = Product::where('vendor_id', $this->vendor->id)
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get(['id', 'name', 'price', 'quantity', 'status']);

        if ($products->isEmpty()) {
            return [
                'success' => true,
                'message' => "You don't have any products yet. Say 'add a new product' to create your first one!"
            ];
        }

        $totalCount = Product::where('vendor_id', $this->vendor->id)->count();
        
        $list = "";
        foreach ($products as $p) {
            $stockStatus = $p->quantity < 5 ? " (LOW STOCK)" : "";
            $list .= "- #{$p->id}: {$p->name} - N" . number_format($p->price) . " ({$p->quantity} in stock){$stockStatus}\n";
        }

        $showing = min(15, $totalCount);
        
        return [
            'success' => true,
            'message' => "Here are your products ({$showing} of {$totalCount}):\n\n{$list}\n" .
                "To update a product: say 'update product #ID price 50000'\n" .
                "To delete a product: say 'delete product #ID'"
        ];
    }

    /**
     * List orders for vendor
     */
    protected function listOrders(): array
    {
        if (!$this->vendor) {
            return ['success' => false, 'message' => 'Vendor profile not found.'];
        }

        $orders = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
            ->latest()
            ->take(10)
            ->with(['items' => fn($q) => $q->where('vendor_id', $this->vendor->id)->with('product')])
            ->get();

        if ($orders->isEmpty()) {
            return [
                'success' => true,
                'message' => "You don't have any orders yet. Once customers purchase your products, they'll appear here."
            ];
        }

        $pendingCount = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
            ->where('status', 'pending')->count();
        $processingCount = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
            ->where('status', 'processing')->count();

        $list = "";
        foreach ($orders as $o) {
            $items = $o->items->pluck('product.name')->filter()->implode(', ') ?: 'Items';
            $total = $o->items->sum('subtotal');
            $date = $o->created_at->format('M d');
            $statusIcon = match($o->status) {
                'pending' => '[PENDING]',
                'processing' => '[PROCESSING]',
                'shipped' => '[SHIPPED]',
                'delivered' => '[DELIVERED]',
                default => "[{$o->status}]"
            };
            $list .= "- {$o->order_number} {$statusIcon} - N" . number_format($total) . " ({$items}) - {$date}\n";
        }

        return [
            'success' => true,
            'message' => "Your orders (Pending: {$pendingCount}, Processing: {$processingCount}):\n\n{$list}\n" .
                "To ship an order: say 'ship order ORDER_NUMBER'\n" .
                "To mark as delivered: say 'mark order ORDER_NUMBER as delivered'"
        ];
    }

    /**
     * Check vendor balance
     */
    protected function checkBalance(): array
    {
        if (!$this->vendor) {
            return ['success' => false, 'message' => 'Vendor profile not found.'];
        }

        $balance = $this->vendor->balance ?? 0;
        $pendingPayouts = VendorPayout::where('vendor_id', $this->vendor->id)
            ->where('status', 'pending')
            ->sum('amount');

        $totalEarned = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
            ->where('payment_status', 'paid')
            ->sum('total');

        $message = "Your Financial Summary:\n\n" .
            "Available Balance: N" . number_format($balance) . "\n" .
            "Pending Payouts: N" . number_format($pendingPayouts) . "\n" .
            "Total Earned (All Time): N" . number_format($totalEarned) . "\n\n";

        if ($balance >= 1000) {
            $message .= "You can withdraw up to N" . number_format($balance) . ". Say 'withdraw 50000' to request a payout.";
        } else {
            $message .= "Minimum withdrawal is N1,000. Keep selling to increase your balance!";
        }

        return ['success' => true, 'message' => $message];
    }

    // ==================== OTHER ACTIONS ====================

    protected function getRecentSales(): array
    {
        if (!$this->vendor) {
            return ['success' => false, 'message' => 'Vendor profile not found.'];
        }

        $recentOrders = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
            ->where('payment_status', 'paid')
            ->latest()
            ->take(5)
            ->with(['items' => fn($q) => $q->where('vendor_id', $this->vendor->id)->with('product')])
            ->get();

        if ($recentOrders->isEmpty()) {
            return [
                'success' => true,
                'message' => "You haven't made any sales yet. Keep marketing your products!"
            ];
        }

        $salesList = "";
        $totalSales = 0;
        
        foreach ($recentOrders as $order) {
            $orderTotal = $order->items->sum('subtotal');
            $totalSales += $orderTotal;
            $itemNames = $order->items->pluck('product.name')->filter()->implode(', ') ?: 'Items';
            $date = $order->created_at->format('M d, Y');
            $salesList .= "- Order {$order->order_number}: N" . number_format($orderTotal) . " ({$itemNames}) on {$date}\n";
        }

        return [
            'success' => true,
            'message' => "Here are your recent sales:\n\n{$salesList}\n" .
                "Total from these orders: N" . number_format($totalSales) . "\n\n" .
                "Your current balance: N" . number_format($this->vendor->balance ?? 0)
        ];
    }

    protected function handleUpdateProduct(string $message): array
    {
        // Check for last product reference
        $lastId = $this->session->context['last_product_id'] ?? null;
        $lastName = $this->session->context['last_product_name'] ?? null;

        if (preg_match('/(?:product\s+)?#?(\d+)/i', $message, $matches)) {
            $productId = $matches[1];
        } elseif ($lastId && (str_contains($message, 'last') || str_contains($message, 'just'))) {
            $productId = $lastId;
        } else {
            $productId = null;
        }

        if ($productId) {
            $product = Product::where('id', $productId)->where('vendor_id', $this->vendor->id)->first();
            if (!$product) {
                return ['success' => false, 'message' => "Product #{$productId} not found in your store."];
            }

            $updates = [];
            if (preg_match('/price[:\s]*[₦N]?(\d+)/i', $message, $m)) $updates['price'] = (float)$m[1];
            if (preg_match('/(?:quantity|qty|stock)[:\s]*(\d+)/i', $message, $m)) $updates['quantity'] = (int)$m[1];

            if (empty($updates)) {
                return [
                    'success' => true,
                    'message' => "What would you like to update for {$product->name} (#{$product->id})?\n\n" .
                        "Current price: N" . number_format($product->price) . "\n" .
                        "Current quantity: {$product->quantity}\n\n" .
                        "Just say something like: update product #{$product->id} price 50000"
                ];
            }

            $product->update($updates);
            $changes = [];
            if (isset($updates['price'])) $changes[] = "Price: N" . number_format($updates['price']);
            if (isset($updates['quantity'])) $changes[] = "Quantity: " . $updates['quantity'];
            
            return [
                'success' => true,
                'message' => "Product updated!\n\n{$product->name} (#{$product->id})\nChanged: " . implode(', ', $changes)
            ];
        }

        $products = Product::where('vendor_id', $this->vendor->id)->orderBy('created_at', 'desc')->take(5)->get(['id', 'name', 'price']);
        $list = $products->map(fn($p) => "- #{$p->id}: {$p->name} (N" . number_format($p->price) . ")")->implode("\n");
        
        $extra = $lastId ? "\n\nLast product you added: #{$lastId} - {$lastName}" : "";
        
        return [
            'success' => true,
            'message' => "Which product do you want to update?\n\n{$list}{$extra}\n\nSay: update product #ID price 50000"
        ];
    }

    protected function handleDeleteProduct(string $message): array
    {
        $lastId = $this->session->context['last_product_id'] ?? null;
        $lastName = $this->session->context['last_product_name'] ?? null;

        if (preg_match('/(?:product\s+)?#?(\d+)/i', $message, $matches)) {
            $productId = $matches[1];
        } elseif ($lastId && (str_contains($message, 'just') || str_contains($message, 'last') || str_contains($message, 'you added') || str_contains($message, 'u added'))) {
            $productId = $lastId;
        } else {
            $productId = null;
        }

        if ($productId) {
            $product = Product::where('id', $productId)->where('vendor_id', $this->vendor->id)->first();
            if (!$product) {
                return ['success' => false, 'message' => "Product #{$productId} not found."];
            }

            $name = $product->name;
            $product->delete();
            
            // Clear from context
            $ctx = $this->session->context ?? [];
            if (isset($ctx['last_product_id']) && $ctx['last_product_id'] == $productId) {
                unset($ctx['last_product_id']);
                unset($ctx['last_product_name']);
                $this->session->update(['context' => $ctx]);
            }
            
            return [
                'success' => true,
                'message' => "Done! Product \"{$name}\" (#{$productId}) has been deleted from your store."
            ];
        }

        $products = Product::where('vendor_id', $this->vendor->id)->orderBy('created_at', 'desc')->take(5)->get(['id', 'name']);
        $list = $products->map(fn($p) => "- #{$p->id}: {$p->name}")->implode("\n");
        
        $extra = $lastId ? "\n\nLast product added: #{$lastId} - {$lastName}" : "";
        
        return [
            'success' => true,
            'message' => "Which product do you want to delete?\n\n{$list}{$extra}\n\nSay: delete product #ID"
        ];
    }

    protected function handleUpdateOrderStatus(string $message): array
    {
        if (preg_match('/(?:order\s+)?#?(ORD-\w+|\d+)/i', $message, $matches)) {
            $order = Order::where('order_number', 'like', "%{$matches[1]}%")
                ->whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
                ->first();
            
            if (!$order) {
                return ['success' => false, 'message' => "Order not found."];
            }

            $newStatus = 'processing';
            if (str_contains($message, 'ship')) $newStatus = 'shipped';
            if (str_contains($message, 'deliver')) $newStatus = 'delivered';

            $order->update(['status' => $newStatus]);

            return [
                'success' => true,
                'message' => "Order {$order->order_number} updated to: " . ucfirst($newStatus)
            ];
        }

        $orders = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
            ->whereIn('status', ['pending', 'processing'])
            ->take(5)
            ->get(['order_number', 'status', 'total']);

        if ($orders->isEmpty()) {
            return ['success' => true, 'message' => "No pending orders to process!"];
        }

        $list = $orders->map(fn($o) => "- {$o->order_number} (" . ucfirst($o->status) . ") - N" . number_format($o->total))->implode("\n");
        
        return [
            'success' => true,
            'message' => "Your pending orders:\n\n{$list}\n\nSay: ship order ORDER_NUMBER"
        ];
    }

    protected function handlePayoutRequest(string $message): array
    {
        $balance = $this->vendor->balance ?? 0;
        
        if ($balance < 1000) {
            return [
                'success' => false,
                'message' => "Your balance is N" . number_format($balance) . ". You need at least N1,000 to withdraw."
            ];
        }

        if (preg_match('/(\d+(?:,\d{3})*)/i', $message, $matches)) {
            $amount = (float) str_replace(',', '', $matches[1]);
            
            if ($amount > $balance) {
                return ['success' => false, 'message' => "That's more than your balance of N" . number_format($balance)];
            }
            if ($amount < 1000) {
                return ['success' => false, 'message' => "Minimum withdrawal is N1,000"];
            }

            VendorPayout::create([
                'vendor_id' => $this->vendor->id,
                'amount' => $amount,
                'reference' => 'PAY-' . strtoupper(Str::random(10)),
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
            ]);

            $this->vendor->decrement('balance', $amount);

            return [
                'success' => true,
                'message' => "Payout requested!\n\nAmount: N" . number_format($amount) . "\nStatus: Pending\n\nIt will be processed in 24-48 hours.\n\nNew balance: N" . number_format($this->vendor->balance)
            ];
        }

        return [
            'success' => true,
            'message' => "Your available balance: N" . number_format($balance) . "\n\nHow much do you want to withdraw? Just type the amount, like: withdraw 50000"
        ];
    }

    // Admin actions
    protected function handleApproveVendor(string $message): array
    {
        if (preg_match('/vendor\s+#?(\d+)/i', $message, $matches)) {
            $vendor = Vendor::find($matches[1]);
            if (!$vendor) return ['success' => false, 'message' => 'Vendor not found.'];
            $vendor->update(['status' => 'approved']);
            return ['success' => true, 'message' => "{$vendor->store_name} has been approved!"];
        }

        $pending = Vendor::where('status', 'pending')->take(5)->get(['id', 'store_name']);
        if ($pending->isEmpty()) return ['success' => true, 'message' => 'No pending vendors!'];

        $list = $pending->map(fn($v) => "- #{$v->id}: {$v->store_name}")->implode("\n");
        return ['success' => true, 'message' => "Pending vendors:\n\n{$list}\n\nSay: approve vendor #ID"];
    }

    protected function handleRejectVendor(string $message): array
    {
        if (preg_match('/vendor\s+#?(\d+)/i', $message, $matches)) {
            $vendor = Vendor::find($matches[1]);
            if (!$vendor) return ['success' => false, 'message' => 'Vendor not found.'];
            $vendor->update(['status' => 'rejected']);
            return ['success' => true, 'message' => "{$vendor->store_name} has been rejected."];
        }
        return ['success' => false, 'message' => 'Please specify: reject vendor #ID'];
    }

    protected function handleProcessPayout(string $message): array
    {
        if (preg_match('/payout\s+#?(\d+)/i', $message, $matches)) {
            $payout = VendorPayout::find($matches[1]);
            if (!$payout) return ['success' => false, 'message' => 'Payout not found.'];
            $payout->update(['status' => 'completed', 'processed_at' => now()]);
            return ['success' => true, 'message' => "Payout #{$payout->id} (N" . number_format($payout->amount) . ") completed!"];
        }

        $pending = VendorPayout::where('status', 'pending')->with('vendor')->take(5)->get();
        if ($pending->isEmpty()) return ['success' => true, 'message' => 'No pending payouts!'];

        $list = $pending->map(fn($p) => "- #{$p->id}: N" . number_format($p->amount) . " ({$p->vendor->store_name})")->implode("\n");
        return ['success' => true, 'message' => "Pending payouts:\n\n{$list}\n\nSay: process payout #ID"];
    }

    protected function handleCancelOrder(string $message): array
    {
        if (preg_match('/(?:order\s+)?#?(ORD-\w+|\d+)/i', $message, $matches)) {
            $order = Order::where('user_id', $this->user->id)
                ->where('order_number', 'like', "%{$matches[1]}%")
                ->where('status', 'pending')
                ->first();
            
            if (!$order) {
                return ['success' => false, 'message' => "Order not found or can't be cancelled."];
            }

            $order->update(['status' => 'cancelled']);
            return ['success' => true, 'message' => "Order {$order->order_number} has been cancelled."];
        }

        $orders = Order::where('user_id', $this->user->id)->where('status', 'pending')->take(5)->get(['order_number', 'total']);
        if ($orders->isEmpty()) return ['success' => true, 'message' => 'No orders to cancel.'];

        $list = $orders->map(fn($o) => "- {$o->order_number}: N" . number_format($o->total))->implode("\n");
        return ['success' => true, 'message' => "Your cancellable orders:\n\n{$list}\n\nSay: cancel order ORDER_NUMBER"];
    }

    public function getAvailableActions(): array
    {
        switch ($this->role) {
            case 'vendor':
                return [
                    'Product: add, list, update, delete, duplicate, feature, deactivate',
                    'Orders: list, filter by status, ship, deliver, view details',
                    'Finance: balance, withdraw, payout history, earnings',
                    'Analytics: stats, today/weekly/monthly sales, top products',
                    'Coupons: list, create, delete',
                    'Store: info, bank details'
                ];
            case 'admin':
            case 'superadmin':
                return [
                    'Vendors: list, pending, approve, reject, enable, disable',
                    'Users: list, search, enable, disable',
                    'Payouts: pending, process, reject, history',
                    'Products: pending, approve, reject, feature',
                    'Stats: users, vendors, products, orders, revenue',
                    'Categories: list, add'
                ];
            case 'customer':
                return [
                    'Orders: list, track, cancel, reorder',
                    'Shopping: search, featured, new arrivals, deals',
                    'Account: wishlist, profile, addresses'
                ];
            default:
                return [];
        }
    }

    // ==================== VENDOR: PRODUCT METHODS ====================

    protected function duplicateProduct(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $product = Product::where('id', $m[1])->where('vendor_id', $this->vendor->id)->first();
            if (!$product) return ['success' => false, 'message' => "Product #{$m[1]} not found."];
            
            $new = $product->replicate();
            $new->name = $product->name . ' (Copy)';
            $new->slug = Str::slug($new->name) . '-' . Str::random(5);
            $new->save();
            
            return ['success' => true, 'message' => "Product duplicated!\n\nNew product: {$new->name}\nID: #{$new->id}"];
        }
        return ['success' => false, 'message' => "Specify product ID: duplicate product #123"];
    }

    protected function featureProduct(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $p = Product::where('id', $m[1])->where('vendor_id', $this->vendor->id)->first();
            if (!$p) return ['success' => false, 'message' => "Product not found."];
            $p->update(['is_featured' => true]);
            return ['success' => true, 'message' => "{$p->name} is now featured!"];
        }
        return ['success' => false, 'message' => "Specify product: feature product #123"];
    }

    protected function unfeatureProduct(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $p = Product::where('id', $m[1])->where('vendor_id', $this->vendor->id)->first();
            if (!$p) return ['success' => false, 'message' => "Product not found."];
            $p->update(['is_featured' => false]);
            return ['success' => true, 'message' => "{$p->name} is no longer featured."];
        }
        return ['success' => false, 'message' => "Specify product: unfeature product #123"];
    }

    protected function activateProduct(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $p = Product::where('id', $m[1])->where('vendor_id', $this->vendor->id)->first();
            if (!$p) return ['success' => false, 'message' => "Product not found."];
            $p->update(['status' => 'active']);
            return ['success' => true, 'message' => "{$p->name} is now active and visible to customers."];
        }
        return ['success' => false, 'message' => "Specify product: activate product #123"];
    }

    protected function deactivateProduct(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $p = Product::where('id', $m[1])->where('vendor_id', $this->vendor->id)->first();
            if (!$p) return ['success' => false, 'message' => "Product not found."];
            $p->update(['status' => 'inactive']);
            return ['success' => true, 'message' => "{$p->name} is now hidden from customers."];
        }
        return ['success' => false, 'message' => "Specify product: deactivate product #123"];
    }

    protected function showLowStock(): array
    {
        $products = Product::where('vendor_id', $this->vendor->id)->where('quantity', '<', 10)->get(['id', 'name', 'quantity']);
        if ($products->isEmpty()) return ['success' => true, 'message' => "All products have sufficient stock!"];
        $list = $products->map(fn($p) => "- #{$p->id}: {$p->name} ({$p->quantity} left)")->implode("\n");
        return ['success' => true, 'message' => "Low stock products:\n\n{$list}\n\nSay: update product #ID quantity 50"];
    }

    protected function showBestSellers(): array
    {
        $products = Product::where('vendor_id', $this->vendor->id)->orderBy('order_count', 'desc')->take(10)->get(['id', 'name', 'order_count', 'price']);
        if ($products->isEmpty()) return ['success' => true, 'message' => "No sales data yet."];
        $list = $products->map(fn($p) => "- #{$p->id}: {$p->name} - {$p->order_count} sold - N" . number_format($p->price))->implode("\n");
        return ['success' => true, 'message' => "Your best selling products:\n\n{$list}"];
    }

    protected function searchProducts(string $message): array
    {
        if (preg_match('/search product[s]?\s+(.+)/i', $message, $m)) {
            $q = trim($m[1]);
            $products = Product::where('vendor_id', $this->vendor->id)->where('name', 'like', "%{$q}%")->take(10)->get(['id', 'name', 'price']);
            if ($products->isEmpty()) return ['success' => true, 'message' => "No products found matching '{$q}'."];
            $list = $products->map(fn($p) => "- #{$p->id}: {$p->name} - N" . number_format($p->price))->implode("\n");
            return ['success' => true, 'message' => "Search results for '{$q}':\n\n{$list}"];
        }
        return ['success' => false, 'message' => "Say: search product iphone"];
    }

    // ==================== VENDOR: ORDER METHODS ====================

    protected function listOrdersByStatus(string $status): array
    {
        $orders = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
            ->where('status', $status)->latest()->take(10)->get(['order_number', 'total', 'created_at']);
        if ($orders->isEmpty()) return ['success' => true, 'message' => "No {$status} orders."];
        $list = $orders->map(fn($o) => "- {$o->order_number} - N" . number_format($o->total) . " - " . $o->created_at->format('M d'))->implode("\n");
        return ['success' => true, 'message' => ucfirst($status) . " orders:\n\n{$list}"];
    }

    protected function viewOrderDetails(string $message): array
    {
        if (preg_match('/(ORD-\w+|\d+)/i', $message, $m)) {
            $order = Order::where('order_number', 'like', "%{$m[1]}%")
                ->whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
                ->with(['items' => fn($q) => $q->where('vendor_id', $this->vendor->id)->with('product'), 'user'])
                ->first();
            if (!$order) return ['success' => false, 'message' => "Order not found."];
            
            $items = $order->items->map(fn($i) => "- {$i->product->name} x{$i->quantity} = N" . number_format($i->subtotal))->implode("\n");
            return ['success' => true, 'message' => "Order: {$order->order_number}\nStatus: " . ucfirst($order->status) . "\nCustomer: {$order->user->name}\nDate: " . $order->created_at->format('M d, Y') . "\n\nItems:\n{$items}\n\nTotal: N" . number_format($order->total)];
        }
        return ['success' => false, 'message' => "Say: view order ORD-ABC123"];
    }

    protected function showTodayOrders(): array
    {
        $orders = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))
            ->whereDate('created_at', today())->get();
        $count = $orders->count();
        $total = $orders->sum('total');
        return ['success' => true, 'message' => "Today's orders: {$count}\nTotal value: N" . number_format($total)];
    }

    // ==================== VENDOR: FINANCE METHODS ====================

    protected function showPayoutHistory(): array
    {
        $payouts = VendorPayout::where('vendor_id', $this->vendor->id)->latest()->take(10)->get(['id', 'amount', 'status', 'created_at']);
        if ($payouts->isEmpty()) return ['success' => true, 'message' => "No payout history yet."];
        $list = $payouts->map(fn($p) => "- #{$p->id}: N" . number_format($p->amount) . " - " . ucfirst($p->status) . " - " . $p->created_at->format('M d'))->implode("\n");
        return ['success' => true, 'message' => "Your payout history:\n\n{$list}"];
    }

    protected function showTotalEarnings(): array
    {
        $total = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))->where('payment_status', 'paid')->sum('total');
        $thisMonth = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))->where('payment_status', 'paid')->whereMonth('created_at', now()->month)->sum('total');
        return ['success' => true, 'message' => "Total earnings (all time): N" . number_format($total) . "\nThis month: N" . number_format($thisMonth) . "\nCurrent balance: N" . number_format($this->vendor->balance ?? 0)];
    }

    // ==================== VENDOR: ANALYTICS METHODS ====================

    protected function showStoreStats(): array
    {
        $products = Product::where('vendor_id', $this->vendor->id)->count();
        $orders = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))->count();
        $pending = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))->where('status', 'pending')->count();
        $revenue = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))->where('payment_status', 'paid')->sum('total');
        $balance = $this->vendor->balance ?? 0;
        return ['success' => true, 'message' => "Store Overview:\n\nProducts: {$products}\nTotal Orders: {$orders}\nPending Orders: {$pending}\nTotal Revenue: N" . number_format($revenue) . "\nAvailable Balance: N" . number_format($balance)];
    }

    protected function showTodaySales(): array
    {
        $orders = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))->whereDate('created_at', today())->where('payment_status', 'paid');
        return ['success' => true, 'message' => "Today's sales:\nOrders: " . $orders->count() . "\nRevenue: N" . number_format($orders->sum('total'))];
    }

    protected function showWeeklySales(): array
    {
        $orders = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))->where('created_at', '>=', now()->subWeek())->where('payment_status', 'paid');
        return ['success' => true, 'message' => "This week's sales:\nOrders: " . $orders->count() . "\nRevenue: N" . number_format($orders->sum('total'))];
    }

    protected function showMonthlySales(): array
    {
        $orders = Order::whereHas('items', fn($q) => $q->where('vendor_id', $this->vendor->id))->whereMonth('created_at', now()->month)->where('payment_status', 'paid');
        return ['success' => true, 'message' => "This month's sales:\nOrders: " . $orders->count() . "\nRevenue: N" . number_format($orders->sum('total'))];
    }

    // ==================== VENDOR: COUPON METHODS ====================

    protected function listCoupons(): array
    {
        if (!class_exists(\App\Models\Coupon::class)) {
            return ['success' => true, 'message' => "Coupons feature is coming soon!"];
        }
        $coupons = \App\Models\Coupon::where('vendor_id', $this->vendor->id)->take(10)->get();
        if ($coupons->isEmpty()) return ['success' => true, 'message' => "No coupons yet. Say: create coupon"];
        $list = $coupons->map(fn($c) => "- {$c->code}: {$c->discount_value}" . ($c->discount_type == 'percentage' ? '%' : ' Naira') . " off")->implode("\n");
        return ['success' => true, 'message' => "Your coupons:\n\n{$list}"];
    }

    protected function startCouponWizard(): array
    {
        return ['success' => true, 'message' => "To create a coupon, go to your vendor dashboard:\n/vendor/coupons/create\n\nOr tell me the details:\n- Code (e.g., SAVE20)\n- Discount type (percentage or fixed)\n- Discount amount"];
    }

    protected function deleteCoupon(string $message): array
    {
        return ['success' => true, 'message' => "To delete a coupon, go to:\n/vendor/coupons\n\nAnd click the delete button next to the coupon."];
    }

    // ==================== VENDOR: STORE INFO ====================

    protected function showStoreInfo(): array
    {
        $v = $this->vendor;
        return ['success' => true, 'message' => "Your Store Info:\n\nStore Name: {$v->store_name}\nStatus: " . ucfirst($v->status) . "\nRating: " . ($v->rating ?? 'N/A') . "\nTotal Products: " . Product::where('vendor_id', $v->id)->count() . "\nBalance: N" . number_format($v->balance ?? 0)];
    }

    protected function showBankDetails(): array
    {
        $v = $this->vendor;
        $bank = $v->bank_name ?? 'Not set';
        $acc = $v->account_number ? substr($v->account_number, 0, 3) . '****' . substr($v->account_number, -3) : 'Not set';
        return ['success' => true, 'message' => "Your Bank Details:\n\nBank: {$bank}\nAccount: {$acc}\n\nTo update, go to: /vendor/settings"];
    }

    // ==================== ADMIN: VENDOR METHODS ====================

    protected function listVendors(): array
    {
        $vendors = Vendor::latest()->take(10)->get(['id', 'store_name', 'status']);
        $list = $vendors->map(fn($v) => "- #{$v->id}: {$v->store_name} (" . ucfirst($v->status) . ")")->implode("\n");
        $total = Vendor::count();
        return ['success' => true, 'message' => "Vendors ({$total} total):\n\n{$list}"];
    }

    protected function listPendingVendors(): array
    {
        $vendors = Vendor::where('status', 'pending')->take(10)->get(['id', 'store_name', 'created_at']);
        if ($vendors->isEmpty()) return ['success' => true, 'message' => "No pending vendor applications!"];
        $list = $vendors->map(fn($v) => "- #{$v->id}: {$v->store_name} - Applied " . $v->created_at->diffForHumans())->implode("\n");
        return ['success' => true, 'message' => "Pending vendors:\n\n{$list}\n\nSay: approve vendor #ID"];
    }

    protected function disableVendor(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $v = Vendor::find($m[1]);
            if (!$v) return ['success' => false, 'message' => "Vendor not found."];
            $v->update(['status' => 'suspended']);
            return ['success' => true, 'message' => "{$v->store_name} has been suspended."];
        }
        return ['success' => false, 'message' => "Say: disable vendor #123"];
    }

    protected function enableVendor(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $v = Vendor::find($m[1]);
            if (!$v) return ['success' => false, 'message' => "Vendor not found."];
            $v->update(['status' => 'approved']);
            return ['success' => true, 'message' => "{$v->store_name} has been reactivated."];
        }
        return ['success' => false, 'message' => "Say: enable vendor #123"];
    }

    // ==================== ADMIN: USER METHODS ====================

    protected function listUsers(): array
    {
        $total = User::count();
        $today = User::whereDate('created_at', today())->count();
        return ['success' => true, 'message' => "User Statistics:\n\nTotal Users: {$total}\nNew Today: {$today}\n\nTo search: search user john@email.com"];
    }

    protected function searchUsers(string $message): array
    {
        if (preg_match('/search user[s]?\s+(.+)/i', $message, $m)) {
            $q = trim($m[1]);
            $users = User::where('email', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%")->take(5)->get(['id', 'name', 'email']);
            if ($users->isEmpty()) return ['success' => true, 'message' => "No users found for '{$q}'."];
            $list = $users->map(fn($u) => "- #{$u->id}: {$u->name} ({$u->email})")->implode("\n");
            return ['success' => true, 'message' => "Users matching '{$q}':\n\n{$list}"];
        }
        return ['success' => false, 'message' => "Say: search user john"];
    }

    protected function disableUser(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $u = User::find($m[1]);
            if (!$u) return ['success' => false, 'message' => "User not found."];
            $u->update(['is_active' => false]);
            return ['success' => true, 'message' => "{$u->name} has been disabled."];
        }
        return ['success' => false, 'message' => "Say: disable user #123"];
    }

    protected function enableUser(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $u = User::find($m[1]);
            if (!$u) return ['success' => false, 'message' => "User not found."];
            $u->update(['is_active' => true]);
            return ['success' => true, 'message' => "{$u->name} has been enabled."];
        }
        return ['success' => false, 'message' => "Say: enable user #123"];
    }

    protected function showNewUsersToday(): array
    {
        $users = User::whereDate('created_at', today())->take(10)->get(['name', 'email', 'created_at']);
        if ($users->isEmpty()) return ['success' => true, 'message' => "No new users today."];
        $list = $users->map(fn($u) => "- {$u->name} ({$u->email})")->implode("\n");
        return ['success' => true, 'message' => "New users today:\n\n{$list}"];
    }

    // ==================== ADMIN: PAYOUT METHODS ====================

    protected function listPendingPayouts(): array
    {
        $payouts = VendorPayout::where('status', 'pending')->with('vendor')->take(10)->get();
        if ($payouts->isEmpty()) return ['success' => true, 'message' => "No pending payouts!"];
        $list = $payouts->map(fn($p) => "- #{$p->id}: N" . number_format($p->amount) . " - {$p->vendor->store_name}")->implode("\n");
        $total = VendorPayout::where('status', 'pending')->sum('amount');
        return ['success' => true, 'message' => "Pending payouts (Total: N" . number_format($total) . "):\n\n{$list}\n\nSay: process payout #ID"];
    }

    protected function rejectPayout(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $p = VendorPayout::find($m[1]);
            if (!$p) return ['success' => false, 'message' => "Payout not found."];
            $p->vendor->increment('balance', $p->amount);
            $p->update(['status' => 'rejected']);
            return ['success' => true, 'message' => "Payout #{$p->id} rejected. Amount returned to vendor balance."];
        }
        return ['success' => false, 'message' => "Say: reject payout #123"];
    }

    protected function showAllPayouts(): array
    {
        $total = VendorPayout::where('status', 'completed')->sum('amount');
        $pending = VendorPayout::where('status', 'pending')->sum('amount');
        $count = VendorPayout::count();
        return ['success' => true, 'message' => "Payout Summary:\n\nTotal Payouts: {$count}\nCompleted: N" . number_format($total) . "\nPending: N" . number_format($pending)];
    }

    // ==================== ADMIN: PRODUCT MODERATION ====================

    protected function listPendingProducts(): array
    {
        $products = Product::where('status', 'pending')->with('vendor')->take(10)->get(['id', 'name', 'vendor_id']);
        if ($products->isEmpty()) return ['success' => true, 'message' => "No products pending approval!"];
        $list = $products->map(fn($p) => "- #{$p->id}: {$p->name} (by {$p->vendor->store_name})")->implode("\n");
        return ['success' => true, 'message' => "Products pending approval:\n\n{$list}\n\nSay: approve product #ID"];
    }

    protected function approveProduct(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $p = Product::find($m[1]);
            if (!$p) return ['success' => false, 'message' => "Product not found."];
            $p->update(['status' => 'active']);
            return ['success' => true, 'message' => "{$p->name} has been approved and is now live!"];
        }
        return ['success' => false, 'message' => "Say: approve product #123"];
    }

    protected function rejectProduct(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $p = Product::find($m[1]);
            if (!$p) return ['success' => false, 'message' => "Product not found."];
            $p->update(['status' => 'rejected']);
            return ['success' => true, 'message' => "{$p->name} has been rejected."];
        }
        return ['success' => false, 'message' => "Say: reject product #123"];
    }

    protected function adminFeatureProduct(string $message): array
    {
        if (preg_match('/#?(\d+)/i', $message, $m)) {
            $p = Product::find($m[1]);
            if (!$p) return ['success' => false, 'message' => "Product not found."];
            $p->update(['is_featured' => true]);
            return ['success' => true, 'message' => "{$p->name} is now featured on the homepage!"];
        }
        return ['success' => false, 'message' => "Say: feature product #123"];
    }

    // ==================== ADMIN: PLATFORM STATS ====================

    protected function showPlatformStats(): array
    {
        $users = User::count();
        $vendors = Vendor::where('status', 'approved')->count();
        $products = Product::where('status', 'active')->count();
        $orders = Order::count();
        $revenue = Order::where('payment_status', 'paid')->sum('total');
        $todayRev = Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total');
        return ['success' => true, 'message' => "Platform Overview:\n\nUsers: {$users}\nVendors: {$vendors}\nProducts: {$products}\nOrders: {$orders}\n\nTotal Revenue: N" . number_format($revenue) . "\nToday: N" . number_format($todayRev)];
    }

    protected function showUserCount(): array { return ['success' => true, 'message' => "Total Users: " . User::count()]; }
    protected function showVendorCount(): array { return ['success' => true, 'message' => "Total Vendors: " . Vendor::count() . "\nApproved: " . Vendor::where('status', 'approved')->count() . "\nPending: " . Vendor::where('status', 'pending')->count()]; }
    protected function showProductCount(): array { return ['success' => true, 'message' => "Total Products: " . Product::count() . "\nActive: " . Product::where('status', 'active')->count()]; }
    protected function showOrderCount(): array { return ['success' => true, 'message' => "Total Orders: " . Order::count() . "\nToday: " . Order::whereDate('created_at', today())->count()]; }
    protected function showTotalRevenue(): array { return ['success' => true, 'message' => "Total Platform Revenue: N" . number_format(Order::where('payment_status', 'paid')->sum('total'))]; }
    protected function showTodayRevenue(): array { return ['success' => true, 'message' => "Today's Revenue: N" . number_format(Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total'))]; }

    // ==================== ADMIN: CATEGORIES ====================

    protected function listCategories(): array
    {
        $cats = Category::withCount('products')->take(20)->get();
        $list = $cats->map(fn($c) => "- #{$c->id}: {$c->name} ({$c->products_count} products)")->implode("\n");
        return ['success' => true, 'message' => "Categories:\n\n{$list}"];
    }

    protected function addCategory(string $message): array
    {
        if (preg_match('/(?:add|create|new)\s+categor[y]?\s+(.+)/i', $message, $m)) {
            $name = ucwords(trim($m[1]));
            $cat = Category::create(['name' => $name, 'slug' => Str::slug($name)]);
            return ['success' => true, 'message' => "Category '{$name}' created! ID: #{$cat->id}"];
        }
        return ['success' => false, 'message' => "Say: add category Electronics"];
    }

    protected function clearCache(): array
    {
        \Artisan::call('optimize:clear');
        return ['success' => true, 'message' => "Cache cleared successfully!"];
    }

    // ==================== CUSTOMER METHODS ====================

    protected function listCustomerOrders(): array
    {
        $orders = Order::where('user_id', $this->user->id)->latest()->take(10)->get(['order_number', 'total', 'status', 'created_at']);
        if ($orders->isEmpty()) return ['success' => true, 'message' => "You haven't placed any orders yet. Start shopping!"];
        $list = $orders->map(fn($o) => "- {$o->order_number}: N" . number_format($o->total) . " - " . ucfirst($o->status) . " - " . $o->created_at->format('M d'))->implode("\n");
        return ['success' => true, 'message' => "Your Orders:\n\n{$list}"];
    }

    protected function trackOrder(string $message): array
    {
        if (preg_match('/(ORD-\w+|\d+)/i', $message, $m)) {
            $order = Order::where('user_id', $this->user->id)->where('order_number', 'like', "%{$m[1]}%")->first();
            if (!$order) return ['success' => false, 'message' => "Order not found."];
            return ['success' => true, 'message' => "Order: {$order->order_number}\nStatus: " . ucfirst($order->status) . "\nPlaced: " . $order->created_at->format('M d, Y') . "\nTotal: N" . number_format($order->total)];
        }
        $orders = Order::where('user_id', $this->user->id)->latest()->take(3)->get(['order_number']);
        $list = $orders->map(fn($o) => $o->order_number)->implode(', ');
        return ['success' => true, 'message' => "Which order? Your recent orders: {$list}\n\nSay: track order ORD-ABC123"];
    }

    protected function reorderGuidance(string $message): array
    {
        return ['success' => true, 'message' => "To reorder a previous purchase:\n1. Go to your order history: /customer/orders\n2. Find the order you want to repeat\n3. Click 'Reorder' button\n\nOr browse products at: /shop"];
    }

    protected function searchProductsCustomer(string $message): array
    {
        if (preg_match('/(?:search|find|looking for)\s+(?:product[s]?\s+)?(.+)/i', $message, $m)) {
            $q = trim($m[1]);
            $products = Product::where('status', 'active')->where('name', 'like', "%{$q}%")->take(5)->get(['name', 'price', 'slug']);
            if ($products->isEmpty()) return ['success' => true, 'message' => "No products found for '{$q}'. Try a different search term."];
            $list = $products->map(fn($p) => "- {$p->name} - N" . number_format($p->price) . " (/shop/{$p->slug})")->implode("\n");
            return ['success' => true, 'message' => "Products matching '{$q}':\n\n{$list}"];
        }
        return ['success' => true, 'message' => "What are you looking for? Say: search product iphone"];
    }

    protected function showFeaturedProducts(): array
    {
        $products = Product::where('status', 'active')->where('is_featured', true)->take(5)->get(['name', 'price', 'slug']);
        if ($products->isEmpty()) return ['success' => true, 'message' => "Check out our featured products at: /shop"];
        $list = $products->map(fn($p) => "- {$p->name} - N" . number_format($p->price))->implode("\n");
        return ['success' => true, 'message' => "Featured Products:\n\n{$list}\n\nView all at: /shop"];
    }

    protected function showNewArrivals(): array
    {
        $products = Product::where('status', 'active')->latest()->take(5)->get(['name', 'price', 'slug']);
        $list = $products->map(fn($p) => "- {$p->name} - N" . number_format($p->price))->implode("\n");
        return ['success' => true, 'message' => "New Arrivals:\n\n{$list}\n\nView all at: /shop"];
    }

    protected function showCheapProducts(string $message): array
    {
        $maxPrice = 10000;
        if (preg_match('/under\s*[N]?(\d+)/i', $message, $m)) $maxPrice = (int)$m[1];
        $products = Product::where('status', 'active')->where('price', '<=', $maxPrice)->orderBy('price')->take(5)->get(['name', 'price']);
        if ($products->isEmpty()) return ['success' => true, 'message' => "No products under N" . number_format($maxPrice) . "."];
        $list = $products->map(fn($p) => "- {$p->name} - N" . number_format($p->price))->implode("\n");
        return ['success' => true, 'message' => "Products under N" . number_format($maxPrice) . ":\n\n{$list}"];
    }

    protected function showWishlist(): array
    {
        return ['success' => true, 'message' => "View your wishlist at: /customer/wishlist\n\nTo add items to wishlist, click the heart icon on any product."];
    }

    protected function showCustomerProfile(): array
    {
        $u = $this->user;
        return ['success' => true, 'message' => "Your Profile:\n\nName: {$u->name}\nEmail: {$u->email}\nMember since: " . $u->created_at->format('M Y') . "\n\nEdit at: /customer/profile"];
    }

    protected function showAddresses(): array
    {
        return ['success' => true, 'message' => "Manage your delivery addresses at: /customer/addresses\n\nYou can add, edit, or remove addresses from there."];
    }

    protected function showHelp(): array
    {
        return ['success' => true, 'message' => "How can I help you?\n\n- Track an order: say 'track order ORD-ABC123'\n- Find products: say 'search product iphone'\n- Cancel order: say 'cancel order ORD-ABC123'\n- View profile: say 'my profile'\n\nFor more help, contact support at: /contact"];
    }
}

