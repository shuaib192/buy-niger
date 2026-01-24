<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderItem;
use App\Models\Product;

class AnalyticsController extends Controller
{
    /**
     * Display vendor analytics.
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;
        $vendorId = $vendor->id;

        // Total orders and revenue
        $totalOrders = OrderItem::where('vendor_id', $vendorId)->count();
        $totalRevenue = OrderItem::where('vendor_id', $vendorId)->where('status', 'delivered')->sum('subtotal');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Top 5 Products by Revenue
        $topProducts = OrderItem::where('vendor_id', $vendorId)
            ->where('status', 'delivered')
            ->selectRaw('product_id, SUM(subtotal) as revenue, SUM(quantity) as quantity_sold')
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->with('product')
            ->take(5)
            ->get();

        // Customer Stats (Total active customers served)
        $totalCustomers = OrderItem::where('vendor_id', $vendorId)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->distinct('orders.user_id')
            ->count('orders.user_id');

        // Revenue Chart Data (Last 30 Days)
        $revenueData = OrderItem::where('vendor_id', $vendorId)
            ->where('status', 'delivered')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(subtotal) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $chartLabels = $revenueData->pluck('date');
        $chartValues = $revenueData->pluck('total');

        return view('vendor.analytics.index', compact('totalOrders', 'totalRevenue', 'averageOrderValue', 'topProducts', 'totalCustomers', 'chartLabels', 'chartValues'));
    }
}
