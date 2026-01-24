<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\StockHistory;

class InventoryController extends Controller
{
    /**
     * Display inventory log and stock management.
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;
        
        $products = Product::where('vendor_id', $vendor->id)
            ->with(['category'])
            ->orderBy('quantity', 'asc') // Show low stock first
            ->paginate(10);
            
        $logs = StockHistory::where('vendor_id', $vendor->id)
            ->with(['product', 'user'])
            ->latest()
            ->paginate(15);
            
        return view('vendor.inventory.index', compact('products', 'logs'));
    }

    /**
     * Update stock for a product manually.
     */
    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string'
        ]);

        $vendor = Auth::user()->vendor;
        $product = Product::where('vendor_id', $vendor->id)->findOrFail($productId);
        
        $oldQuantity = $product->quantity;
        $newQuantity = $request->quantity;
        
        if ($oldQuantity == $newQuantity) {
            return back()->with('info', 'No changes made to stock.');
        }
        
        // Update product
        $product->update(['quantity' => $newQuantity]);
        
        // Log history
        StockHistory::create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $newQuantity,
            'type' => $newQuantity > $oldQuantity ? 'restock' : 'correction',
            'reason' => $request->reason ?? 'Manual update',
            'user_id' => Auth::id()
        ]);
        
        return back()->with('success', 'Stock updated successfully!');
    }
}
