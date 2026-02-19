<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: MessageController
 */

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Start a new conversation or open existing one.
     */
    public function startConversation(Request $request, $vendorId)
    {
        $user = Auth::user();
        $vendor = Vendor::findOrFail($vendorId);
        
        // Product context (optional)
        $productId = $request->product_id ?? null;
        $subject = $request->subject ?? 'General Inquiry';

        // Check if conversation exists (same user, vendor, and potentially same product)
        $query = Conversation::where('user_id', $user->id)
            ->where('vendor_id', $vendor->id);
            
        if ($productId) {
            $query->where('product_id', $productId);
        }

        $conversation = $query->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_id' => $user->id,
                'vendor_id' => $vendor->id,
                'product_id' => $productId,
                'subject' => $subject,
                'last_message_at' => now(),
            ]);
        }
        
        // If coming from product page, maybe auto-send an initial message? 
        // For now, just redirect to show.

        return redirect()->route('customer.messages.show', $conversation->id);
    }

    /**
     * Vendor: Start or open conversation with a customer.
     */
    public function startConversationFromVendor(Request $request, $userId)
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) abort(403);

        $user = \App\Models\User::findOrFail($userId);
        $subject = $request->subject ?? 'Vendor Inquiry';

        $conversation = Conversation::where('user_id', $user->id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_id' => $user->id,
                'vendor_id' => $vendor->id,
                'subject' => $subject,
                'last_message_at' => now(),
            ]);
        }

        return redirect()->route('vendor.messages.show', $conversation->id);
    }

    /**
     * Customer: List all conversations.
     */
    public function indexCustomer()
    {
        $conversations = Conversation::where('user_id', Auth::id())
            ->with(['vendor', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('customer.messages.index', compact('conversations'));
    }

    /**
     * Vendor: List all conversations.
     */
    public function indexVendor()
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) abort(403);

        $conversations = Conversation::where('vendor_id', $vendor->id)
            ->with(['user', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('vendor.messages.index', compact('conversations'));
    }

    /**
     * Show conversation (Unified view for both).
     */
    public function show($id)
    {
        $user = Auth::user();
        $conversation = Conversation::with(['messages.sender', 'user', 'vendor'])->findOrFail($id);

        // Authorization check
        if ($user->role_id == 3) { // Vendor
             if ($conversation->vendor_id != $user->vendor->id) abort(403);
             // Mark header as read
             if ($conversation->messages()->where('sender_type', 'customer')->where('is_read', false)->exists()) {
                 $conversation->messages()->where('sender_type', 'customer')->update(['is_read' => true]);
                 $conversation->update(['vendor_read_at' => now()]);
             }
             $view = 'vendor.messages.show';
        } else { // Customer
             if ($conversation->user_id != $user->id) abort(403);
             // Mark header as read
             if ($conversation->messages()->where('sender_type', 'vendor')->where('is_read', false)->exists()) {
                 $conversation->messages()->where('sender_type', 'vendor')->update(['is_read' => true]);
                 $conversation->update(['user_read_at' => now()]);
             }
             $view = 'customer.messages.show';
        }

        return view($view, compact('conversation'));
    }

    /**
     * Send a message.
     */
    public function send(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $conversation = Conversation::findOrFail($id);
        $user = Auth::user();

        // Identify sender type
        $senderType = $user->role_id == 3 ? 'vendor' : 'customer'; // 3 is vendor

        // Security check
        if ($senderType == 'vendor' && $conversation->vendor_id != $user->vendor->id) abort(403);
        if ($senderType == 'customer' && $conversation->user_id != $user->id) abort(403);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => $senderType,
            'body' => $request->body,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return back()->with('success', 'Message sent!');
    }
}
