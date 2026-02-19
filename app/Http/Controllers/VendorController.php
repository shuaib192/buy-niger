<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Controller: VendorController
 */

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\VendorBankDetail;
use App\Models\VendorPayout;
use App\Models\Category;
use App\Models\Tag;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    /**
     * Display vendor dashboard with stats.
     */
    public function dashboard(Request $request)
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) return redirect()->route('home')->with('error', 'Vendor profile not found.');

        // Get real order stats
        $orderItems = OrderItem::where('vendor_id', $vendor->id);
        
        $stats = [
            'total_products' => Product::where('vendor_id', $vendor->id)->count(),
            'active_products' => Product::where('vendor_id', $vendor->id)->where('status', 'active')->count(),
            'total_orders' => $orderItems->clone()->count(),
            'pending_orders' => $orderItems->clone()->whereIn('status', ['pending', 'processing'])->count(),
            'paid_orders' => $orderItems->clone()->whereHas('order', fn($q) => $q->where('payment_status', 'paid'))->count(),
            'pending_earnings' => $orderItems->clone()->whereIn('status', ['processing', 'shipped'])->sum('subtotal'),
            'total_earnings' => $orderItems->clone()->where('status', 'delivered')->sum('subtotal'),
            'store_rating' => $vendor->rating ?? 0,
        ];

        // Get recent orders
        $recentOrders = OrderItem::where('vendor_id', $vendor->id)
            ->with(['order.user', 'product'])
            ->latest()
            ->take(5)
            ->get();

        // Chart Data Filtering
        $period = $request->get('period', 'monthly');
        $chartData = $this->getChartData($vendor->id, $period);

        // Top Products by sales count
        $topProducts = OrderItem::where('vendor_id', $vendor->id)
            ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(subtotal) as total_revenue')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Get low stock products
        $lowStockProducts = Product::where('vendor_id', $vendor->id)
            ->where('quantity', '<=', 5)
            ->where('status', 'active')
            ->take(5)
            ->get();

        return view('vendor.dashboard', compact('vendor', 'stats', 'recentOrders', 'chartData', 'lowStockProducts', 'topProducts', 'period'));
    }

    /**
     * Helper to get sales chart data based on period.
     */
    private function getChartData($vendorId, $period)
    {
        $data = [];
        $labels = [];
        $query = OrderItem::where('vendor_id', $vendorId)->where('status', 'delivered');

        if ($period === 'daily') {
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $labels[] = now()->subDays($i)->format('d M');
                $data[] = $query->clone()->whereDate('created_at', $date)->sum('subtotal');
            }
        } elseif ($period === 'weekly') {
            for ($i = 11; $i >= 0; $i--) {
                $start = now()->subWeeks($i)->startOfWeek();
                $end = now()->subWeeks($i)->endOfWeek();
                $labels[] = 'Week ' . $start->format('W');
                $data[] = $query->clone()->whereBetween('created_at', [$start, $end])->sum('subtotal');
            }
        } else { // monthly
            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $labels[] = $month->format('M Y');
                $data[] = $query->clone()->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('subtotal');
            }
        }

        return ['labels' => $labels, 'datasets' => [['label' => 'Sales (₦)', 'data' => $data]]];
    }

    /**
     * List all orders for vendor.
     */
    public function orders(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        $query = OrderItem::where('vendor_id', $vendor->id)
            ->with('order.user', 'product');

        // Status counts for tabs
        $counts = [
            'all' => OrderItem::where('vendor_id', $vendor->id)->count(),
            'pending' => OrderItem::where('vendor_id', $vendor->id)->where('status', 'pending')->count(),
            'processing' => OrderItem::where('vendor_id', $vendor->id)->where('status', 'processing')->count(),
            'shipped' => OrderItem::where('vendor_id', $vendor->id)->where('status', 'shipped')->count(),
            'delivered' => OrderItem::where('vendor_id', $vendor->id)->where('status', 'delivered')->count(),
            'cancelled' => OrderItem::where('vendor_id', $vendor->id)->where('status', 'cancelled')->count(),
        ];

        // Filter by status
        $status = $request->get('status', 'all');
        if ($status != 'all') {
            $query->where('status', $status);
        }

        $orderItems = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('vendor.orders', compact('orderItems', 'counts', 'status'));
    }

    /**
     * View single order item.
     */
    public function orderDetail($id)
    {
        $vendor = Auth::user()->vendor;
        
        $orderItem = OrderItem::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->with('order.user', 'order.address', 'product')
            ->firstOrFail();

        return view('vendor.order-detail', compact('orderItem'));
    }

    /**
     * Display inventory management screen.
     */
    public function inventory(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $query = Product::where('vendor_id', $vendor->id)->with(['variants', 'category']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(15)->withQueryString();
        
        // Get recent stock history
        $history = StockHistory::whereHas('product', fn($q) => $q->where('vendor_id', $vendor->id))
            ->with(['product', 'variant'])
            ->latest()
            ->take(10)
            ->get();

        return view('vendor.inventory', compact('products', 'history'));
    }

    /**
 * Display vendor analytics dashboard.
 */
public function analytics(Request $request)
{
    $vendor = Auth::user()->vendor;

    // --- Period Setup ---
    $period = $request->get('period', '30');
    $periodMap = [
        '7'   => ['days' => 7,   'label' => 'Last 7 Days'],
        '30'  => ['days' => 30,  'label' => 'Last 30 Days'],
        '90'  => ['days' => 90,  'label' => 'Last 90 Days'],
        '365' => ['days' => 365, 'label' => 'Last 12 Months'],
        'all' => ['days' => null, 'label' => 'All Time'],
    ];
    if (!array_key_exists($period, $periodMap)) $period = '30';
    $days = $periodMap[$period]['days'];
    $periodLabel = $periodMap[$period]['label'];

    $now = now();
    $startDate = $days ? $now->copy()->subDays($days) : null;
    $prevStartDate = $days ? $now->copy()->subDays($days * 2) : null;
    $prevEndDate = $startDate;

    // Helper: base query for current period
    $q = fn() => OrderItem::where('order_items.vendor_id', $vendor->id)
        ->when($startDate, fn($q) => $q->where('order_items.created_at', '>=', $startDate));

    // Helper: base query for previous period (for comparison)
    $prevQ = fn() => OrderItem::where('order_items.vendor_id', $vendor->id)
        ->when($prevStartDate, fn($q) => $q
            ->where('order_items.created_at', '>=', $prevStartDate)
            ->where('order_items.created_at', '<', $prevEndDate)
        );

    // --- Current Period Metrics ---
    $totalRevenue    = (clone $q())->where('order_items.status', 'delivered')->sum('subtotal');
    $totalOrders     = (clone $q())->count();
    $totalProducts   = Product::where('vendor_id', $vendor->id)->count();
    $pendingOrders   = (clone $q())->where('order_items.status', 'pending')->count();
    $deliveredOrders = (clone $q())->where('order_items.status', 'delivered')->count();
    $cancelledOrders = (clone $q())->where('order_items.status', 'cancelled')->count();
    $avgOrderValue   = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
    $totalCustomers  = (clone $q())
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->distinct('orders.user_id')
        ->count('orders.user_id');
    $conversionRate  = $totalProducts > 0 ? round(($totalOrders / max($totalProducts, 1)) * 10, 1) : 0;

    // --- Previous Period for Growth % ---
    $prevRevenue = (clone $prevQ())->where('order_items.status', 'delivered')->sum('subtotal');
    $prevOrders  = (clone $prevQ())->count();
    $prevCustomers = (clone $prevQ())
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->distinct('orders.user_id')
        ->count('orders.user_id');

    $revenueGrowth   = $prevRevenue > 0 ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : null;
    $ordersGrowth    = $prevOrders > 0 ? round((($totalOrders - $prevOrders) / $prevOrders) * 100, 1) : null;
    $customersGrowth = $prevCustomers > 0 ? round((($totalCustomers - $prevCustomers) / $prevCustomers) * 100, 1) : null;

    // --- Sales Chart (group by day or month depending on period) ---
    $groupFormat = $days && $days <= 90 ? 'DATE(order_items.created_at)' : 'DATE_FORMAT(order_items.created_at, "%Y-%m-01")';
    $salesData = (clone $q())
        ->where('order_items.status', 'delivered')
        ->selectRaw("$groupFormat as date, SUM(subtotal) as total, COUNT(*) as count")
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    $chartLabels = $salesData->pluck('date')->map(function($d) use ($days) {
        return $days && $days <= 90
            ? \Carbon\Carbon::parse($d)->format('M d')
            : \Carbon\Carbon::parse($d)->format('M Y');
    })->toArray();
    $chartValues  = $salesData->pluck('total')->toArray();
    $chartOrders  = $salesData->pluck('count')->toArray();

    // --- Orders by Status ---
    $ordersByStatus = (clone $q())
        ->selectRaw('order_items.status, count(*) as count')
        ->groupBy('order_items.status')
        ->pluck('count', 'order_items.status')
        ->toArray();

    // --- Top Products ---
    $topProducts = (clone $q())
        ->selectRaw('product_id, SUM(quantity) as quantity_sold, SUM(subtotal) as revenue')
        ->with('product')
        ->groupBy('product_id')
        ->orderByDesc('revenue')
        ->take(8)
        ->get();

    // --- Revenue by Category ---
    $revenueByCategory = (clone $q())
        ->where('order_items.status', 'delivered')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->selectRaw('categories.name as category, SUM(order_items.subtotal) as revenue')
        ->groupBy('categories.name')
        ->orderByDesc('revenue')
        ->take(6)
        ->get();

    // --- Recent Orders ---
    $recentOrders = (clone $q())
        ->with(['product', 'order.user'])
        ->latest('order_items.created_at')
        ->take(8)
        ->get();

    // --- AI Business Insights (logic-based) ---
    $insights = $this->generateVendorInsights([
        'revenue'        => $totalRevenue,
        'prevRevenue'    => $prevRevenue,
        'revenueGrowth'  => $revenueGrowth,
        'orders'         => $totalOrders,
        'ordersGrowth'   => $ordersGrowth,
        'delivered'      => $deliveredOrders,
        'cancelled'      => $cancelledOrders,
        'customers'      => $totalCustomers,
        'customersGrowth'=> $customersGrowth,
        'avgOrderValue'  => $avgOrderValue,
        'topProducts'    => $topProducts,
        'products'       => $totalProducts,
        'periodLabel'    => $periodLabel,
    ]);

    return view('vendor.analytics', compact(
        'vendor', 'period', 'periodLabel', 'periodMap',
        'totalRevenue', 'totalOrders', 'totalProducts', 'totalCustomers',
        'pendingOrders', 'deliveredOrders', 'cancelledOrders',
        'avgOrderValue', 'conversionRate',
        'revenueGrowth', 'ordersGrowth', 'customersGrowth',
        'chartLabels', 'chartValues', 'chartOrders',
        'topProducts', 'revenueByCategory', 'ordersByStatus', 'recentOrders',
        'insights'
    ));
}

/**
 * Generate AI-style business insights from analytics data.
 */
protected function generateVendorInsights(array $data): array
{
    $insights = [];

    // Revenue insight
    if ($data['revenue'] == 0) {
        $insights[] = ['type' => 'warning', 'icon' => 'fa-exclamation-triangle', 'title' => 'No Revenue Yet',
            'text' => "You haven't recorded any delivered orders in the {$data['periodLabel']}. Focus on promoting your products and fulfilling pending orders to start generating revenue."];
    } elseif ($data['revenueGrowth'] !== null && $data['revenueGrowth'] > 20) {
        $insights[] = ['type' => 'success', 'icon' => 'fa-chart-line', 'title' => 'Strong Revenue Growth',
            'text' => "Your revenue grew by {$data['revenueGrowth']}% compared to the previous period. This is excellent momentum — consider investing in more inventory for your best-selling items."];
    } elseif ($data['revenueGrowth'] !== null && $data['revenueGrowth'] < -10) {
        $insights[] = ['type' => 'danger', 'icon' => 'fa-arrow-down', 'title' => 'Revenue Decline',
            'text' => "Revenue dropped by " . abs($data['revenueGrowth']) . "% this period. Review your pricing, product availability, and consider running a promotion to re-engage customers."];
    } else {
        $insights[] = ['type' => 'info', 'icon' => 'fa-wallet', 'title' => 'Revenue Summary',
            'text' => "You earned ₦" . number_format($data['revenue']) . " in the {$data['periodLabel']}. " .
                ($data['revenueGrowth'] !== null ? "That&apos;s a " . abs($data['revenueGrowth']) . "% " . ($data['revenueGrowth'] >= 0 ? 'increase' : 'decrease') . " from the previous period." : '')];
    }

    // Cancellations insight
    if ($data['orders'] > 0) {
        $cancelRate = round(($data['cancelled'] / $data['orders']) * 100, 1);
        if ($cancelRate > 15) {
            $insights[] = ['type' => 'danger', 'icon' => 'fa-times-circle', 'title' => 'High Cancellation Rate',
                'text' => "{$cancelRate}% of your orders were cancelled. This is above the healthy threshold of 5-10%. Consider improving product descriptions, shipping speed, or customer communication to reduce cancellations."];
        } elseif ($cancelRate > 0 && $cancelRate <= 10) {
            $insights[] = ['type' => 'success', 'icon' => 'fa-check-circle', 'title' => 'Good Fulfillment Rate',
                'text' => "Your cancellation rate is {$cancelRate}%, which is within a healthy range. Keep maintaining consistent stock levels and clear product descriptions."];
        }
    }

    // Customer growth insight
    if ($data['customersGrowth'] !== null) {
        if ($data['customersGrowth'] > 0) {
            $insights[] = ['type' => 'success', 'icon' => 'fa-users', 'title' => 'Growing Customer Base',
                'text' => "You gained " . abs(round($data['customersGrowth'])) . "% more unique customers this period. This growth suggests your store visibility is improving — keep it up!"];
        } else {
            $insights[] = ['type' => 'warning', 'icon' => 'fa-user-minus', 'title' => 'Customer Acquisition Slowing',
                'text' => "Customer acquisition dropped by " . abs(round($data['customersGrowth'])) . "%. Consider sharing your store link on social media or offering first-order discounts."];
        }
    }

    // Average order value insight
    if ($data['avgOrderValue'] > 0) {
        if ($data['avgOrderValue'] > 10000) {
            $insights[] = ['type' => 'success', 'icon' => 'fa-gem', 'title' => 'High Average Order Value',
                'text' => "Your average order value is ₦" . number_format($data['avgOrderValue']) . " — you&apos;re attracting high-value customers. Consider upselling related products to increase it further."];
        } else {
            $insights[] = ['type' => 'info', 'icon' => 'fa-shopping-cart', 'title' => 'Average Order Value',
                'text' => "Your average order value is ₦" . number_format($data['avgOrderValue']) . ". You can increase this by bundling products, offering free shipping above a threshold, or suggesting related items."];
        }
    }

    // Top product highlight
    if ($data['topProducts']->isNotEmpty()) {
        $top = $data['topProducts']->first();
        if ($top && $top->product) {
            $insights[] = ['type' => 'info', 'icon' => 'fa-star', 'title' => 'Your Best Seller',
                'text' => "\"{$top->product->name}\" is your top-performing product with ₦" . number_format($top->revenue) . " in revenue. Make sure it&apos;s always in stock and prominently featured in your store."];
        }
    }

    // Low product count warning
    if ($data['products'] < 5) {
        $insights[] = ['type' => 'warning', 'icon' => 'fa-box-open', 'title' => 'Add More Products',
            'text' => "You only have {$data['products']} product(s) listed. Stores with 10+ products typically see 3x more customer engagement. Upload more products to maximize your visibility."];
    }

    return $insights;
}

/**
 * Export detailed analytics report as multi-sheet Excel.
 */
public function exportAnalytics(Request $request)
{
    $vendor = Auth::user()->vendor;
    $period = $request->get('period', '30');
    $periodMap = [
        '7' => ['days' => 7, 'label' => 'Last 7 Days'],
        '30' => ['days' => 30, 'label' => 'Last 30 Days'],
        '90' => ['days' => 90, 'label' => 'Last 90 Days'],
        '365' => ['days' => 365, 'label' => 'Last 12 Months'],
        'all' => ['days' => null, 'label' => 'All Time'],
    ];
    if (!array_key_exists($period, $periodMap)) $period = '30';
    $days = $periodMap[$period]['days'];
    $periodLabel = $periodMap[$period]['label'];
    $startDate = $days ? now()->subDays($days) : null;

    // Base query
    $q = fn() => OrderItem::where('order_items.vendor_id', $vendor->id)
        ->when($startDate, fn($qr) => $qr->where('order_items.created_at', '>=', $startDate));

    // Gather data
    $totalRevenue = (clone $q())->where('order_items.status', 'delivered')->sum('subtotal');
    $totalOrders = (clone $q())->count();
    $deliveredOrders = (clone $q())->where('order_items.status', 'delivered')->count();
    $pendingOrders = (clone $q())->where('order_items.status', 'pending')->count();
    $cancelledOrders = (clone $q())->where('order_items.status', 'cancelled')->count();
    $totalProducts = Product::where('vendor_id', $vendor->id)->count();
    $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
    $totalCustomers = (clone $q())
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->distinct('orders.user_id')
        ->count('orders.user_id');

    // Daily/monthly sales
    $groupFormat = $days && $days <= 90 ? 'DATE(order_items.created_at)' : 'DATE_FORMAT(order_items.created_at, "%Y-%m-01")';
    $salesData = (clone $q())
        ->selectRaw("$groupFormat as date, SUM(subtotal) as revenue, COUNT(*) as orders, SUM(quantity) as units")
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get();

    // Top products
    $topProducts = (clone $q())
        ->selectRaw('product_id, SUM(quantity) as quantity_sold, SUM(subtotal) as revenue, COUNT(*) as order_count')
        ->with('product')
        ->groupBy('product_id')
        ->orderByDesc('revenue')
        ->get();

    // All orders for period
    $allOrders = (clone $q())
        ->with(['product', 'order.user'])
        ->latest('order_items.created_at')
        ->get();

    // Product performance
    $productPerformance = Product::where('vendor_id', $vendor->id)
        ->withCount(['orderItems as total_sold' => function($q) { $q->select(\DB::raw('COALESCE(SUM(quantity),0)')); }])
        ->withCount(['orderItems as total_revenue' => function($q) { $q->select(\DB::raw('COALESCE(SUM(subtotal),0)')); }])
        ->get();

    // ── Build Excel ──
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $spreadsheet->getProperties()
        ->setCreator('BuyNiger')
        ->setTitle("Analytics Report - {$vendor->store_name}")
        ->setDescription("Detailed analytics report for {$periodLabel}");

    $headerFill = ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']]];
    $headerFont = ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11]];
    $borderAll = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]]];
    $nairaFormat = '#,##0.00';

    // Helper to style a header row
    $styleHeader = function($sheet, $cols, $row = 1) use ($headerFill, $headerFont, $borderAll) {
        $lastCol = chr(64 + $cols); // A=1, B=2, ...
        $range = "A{$row}:{$lastCol}{$row}";
        $sheet->getStyle($range)->applyFromArray(array_merge($headerFill, $headerFont));
        // Auto-width
        for ($i = 1; $i <= $cols; $i++) {
            $sheet->getColumnDimension(chr(64 + $i))->setAutoSize(true);
        }
    };

    $applyBorders = function($sheet, $cols, $startRow, $endRow) use ($borderAll) {
        $lastCol = chr(64 + $cols);
        $sheet->getStyle("A{$startRow}:{$lastCol}{$endRow}")->applyFromArray($borderAll);
    };

    // ═══════════════════════════════════
    // SHEET 1: Overview
    // ═══════════════════════════════════
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('Overview');

    // Store header
    $sheet1->setCellValue('A1', 'BUYNIGER ANALYTICS REPORT');
    $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('1E293B');
    $sheet1->mergeCells('A1:D1');

    $sheet1->setCellValue('A3', 'Store Name');
    $sheet1->setCellValue('B3', $vendor->store_name);
    $sheet1->setCellValue('A4', 'Report Period');
    $sheet1->setCellValue('B4', $periodLabel);
    $sheet1->setCellValue('A5', 'Generated On');
    $sheet1->setCellValue('B5', now()->format('F j, Y g:i A'));
    $sheet1->setCellValue('A6', 'Vendor Email');
    $sheet1->setCellValue('B6', Auth::user()->email);
    $sheet1->getStyle('A3:A6')->getFont()->setBold(true);
    $applyBorders($sheet1, 2, 3, 6);

    // KPI Section
    $sheet1->setCellValue('A8', 'KEY PERFORMANCE INDICATORS');
    $sheet1->getStyle('A8')->getFont()->setBold(true)->setSize(13)->getColor()->setRGB('6366F1');
    $sheet1->mergeCells('A8:D8');

    $kpiHeaders = ['Metric', 'Value', 'Description', 'Status'];
    foreach ($kpiHeaders as $i => $h) {
        $sheet1->setCellValue(chr(65 + $i) . '9', $h);
    }
    $styleHeader($sheet1, 4, 9);

    $kpis = [
        ['Total Revenue', '₦' . number_format($totalRevenue, 2), 'Revenue from delivered orders', $totalRevenue > 0 ? '✅ Active' : '⚠️ No Revenue'],
        ['Total Orders', number_format($totalOrders), 'All orders in the period', $totalOrders > 0 ? '✅ Active' : '⚠️ None'],
        ['Delivered Orders', number_format($deliveredOrders), 'Successfully delivered', $deliveredOrders > 0 ? '✅ Good' : '—'],
        ['Pending Orders', number_format($pendingOrders), 'Awaiting processing', $pendingOrders > 5 ? '⚠️ High' : '✅ OK'],
        ['Cancelled Orders', number_format($cancelledOrders), 'Orders cancelled', $cancelledOrders > $totalOrders * 0.1 ? '⚠️ High' : '✅ Low'],
        ['Average Order Value', '₦' . number_format($avgOrderValue, 2), 'Revenue per order', $avgOrderValue > 5000 ? '✅ Good' : 'ℹ️ Low'],
        ['Unique Customers', number_format($totalCustomers), 'Distinct buyers', $totalCustomers > 10 ? '✅ Growing' : 'ℹ️ Build audience'],
        ['Active Products', number_format($totalProducts), 'Products in your store', $totalProducts >= 10 ? '✅ Good' : '⚠️ Add more'],
        ['Fulfillment Rate', $totalOrders > 0 ? round(($deliveredOrders / $totalOrders) * 100, 1) . '%' : 'N/A', 'Delivered / Total orders', ''],
        ['Cancellation Rate', $totalOrders > 0 ? round(($cancelledOrders / $totalOrders) * 100, 1) . '%' : 'N/A', 'Cancelled / Total orders', ''],
    ];
    $row = 10;
    foreach ($kpis as $kpi) {
        $sheet1->setCellValue("A{$row}", $kpi[0]);
        $sheet1->setCellValue("B{$row}", $kpi[1]);
        $sheet1->setCellValue("C{$row}", $kpi[2]);
        $sheet1->setCellValue("D{$row}", $kpi[3]);
        $row++;
    }
    $applyBorders($sheet1, 4, 9, $row - 1);
    // Zebra stripes
    for ($r = 10; $r < $row; $r += 2) {
        $sheet1->getStyle("A{$r}:D{$r}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
    }

    // AI Insights section
    $row += 1;
    $sheet1->setCellValue("A{$row}", 'AI BUSINESS INSIGHTS');
    $sheet1->getStyle("A{$row}")->getFont()->setBold(true)->setSize(13)->getColor()->setRGB('6366F1');
    $sheet1->mergeCells("A{$row}:D{$row}");
    $row++;

    $insights = $this->generateVendorInsights([
        'revenue' => $totalRevenue, 'prevRevenue' => 0, 'revenueGrowth' => null,
        'orders' => $totalOrders, 'ordersGrowth' => null,
        'delivered' => $deliveredOrders, 'cancelled' => $cancelledOrders,
        'customers' => $totalCustomers, 'customersGrowth' => null,
        'avgOrderValue' => $avgOrderValue,
        'topProducts' => $topProducts,
        'products' => $totalProducts,
        'periodLabel' => $periodLabel,
    ]);

    $sheet1->setCellValue("A{$row}", 'Type');
    $sheet1->setCellValue("B{$row}", 'Title');
    $sheet1->setCellValue("C{$row}", 'Insight');
    $sheet1->setCellValue("D{$row}", 'Action');
    $styleHeader($sheet1, 4, $row);
    $row++;
    foreach ($insights as $ins) {
        $sheet1->setCellValue("A{$row}", ucfirst($ins['type']));
        $sheet1->setCellValue("B{$row}", $ins['title']);
        $sheet1->setCellValue("C{$row}", strip_tags(html_entity_decode($ins['text'])));
        $sheet1->setCellValue("D{$row}", $ins['type'] === 'success' ? 'Keep it up!' : ($ins['type'] === 'danger' ? 'Needs attention!' : 'Review'));
        $sheet1->getStyle("C{$row}")->getAlignment()->setWrapText(true);
        $sheet1->getColumnDimension('C')->setWidth(60);
        $row++;
    }
    $applyBorders($sheet1, 4, $row - count($insights) - 1, $row - 1);

    // ═══════════════════════════════════
    // SHEET 2: Sales Timeline
    // ═══════════════════════════════════
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('Sales Timeline');

    $s2Headers = ['Date', 'Revenue (₦)', 'Orders', 'Units Sold', 'Avg Revenue/Order'];
    foreach ($s2Headers as $i => $h) {
        $sheet2->setCellValue(chr(65 + $i) . '1', $h);
    }
    $styleHeader($sheet2, 5, 1);

    $row = 2;
    foreach ($salesData as $s) {
        $avgPerOrder = $s->orders > 0 ? $s->revenue / $s->orders : 0;
        $sheet2->setCellValue("A{$row}", $s->date);
        $sheet2->setCellValue("B{$row}", $s->revenue);
        $sheet2->getStyle("B{$row}")->getNumberFormat()->setFormatCode($nairaFormat);
        $sheet2->setCellValue("C{$row}", $s->orders);
        $sheet2->setCellValue("D{$row}", $s->units);
        $sheet2->setCellValue("E{$row}", $avgPerOrder);
        $sheet2->getStyle("E{$row}")->getNumberFormat()->setFormatCode($nairaFormat);
        $row++;
    }
    // Totals row
    $sheet2->setCellValue("A{$row}", 'TOTAL');
    $sheet2->setCellValue("B{$row}", $salesData->sum('revenue'));
    $sheet2->getStyle("B{$row}")->getNumberFormat()->setFormatCode($nairaFormat);
    $sheet2->setCellValue("C{$row}", $salesData->sum('orders'));
    $sheet2->setCellValue("D{$row}", $salesData->sum('units'));
    $sheet2->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
    $sheet2->getStyle("A{$row}:E{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('EEF2FF');
    $applyBorders($sheet2, 5, 1, $row);

    // ═══════════════════════════════════
    // SHEET 3: Top Products
    // ═══════════════════════════════════
    $sheet3 = $spreadsheet->createSheet();
    $sheet3->setTitle('Top Products');

    $s3Headers = ['Rank', 'Product Name', 'Category', 'Price (₦)', 'Units Sold', 'Revenue (₦)', 'Order Count', '% of Total Revenue'];
    foreach ($s3Headers as $i => $h) {
        $sheet3->setCellValue(chr(65 + $i) . '1', $h);
    }
    $styleHeader($sheet3, 8, 1);

    $row = 2;
    $totalProductRevenue = $topProducts->sum('revenue') ?: 1;
    foreach ($topProducts as $i => $tp) {
        $pct = round(($tp->revenue / $totalProductRevenue) * 100, 1);
        $sheet3->setCellValue("A{$row}", $i + 1);
        $sheet3->setCellValue("B{$row}", $tp->product->name ?? 'Deleted Product');
        $sheet3->setCellValue("C{$row}", $tp->product->category->name ?? 'N/A');
        $sheet3->setCellValue("D{$row}", $tp->product->price ?? 0);
        $sheet3->getStyle("D{$row}")->getNumberFormat()->setFormatCode($nairaFormat);
        $sheet3->setCellValue("E{$row}", $tp->quantity_sold);
        $sheet3->setCellValue("F{$row}", $tp->revenue);
        $sheet3->getStyle("F{$row}")->getNumberFormat()->setFormatCode($nairaFormat);
        $sheet3->setCellValue("G{$row}", $tp->order_count);
        $sheet3->setCellValue("H{$row}", $pct . '%');
        $row++;
    }
    $applyBorders($sheet3, 8, 1, $row - 1);

    // ═══════════════════════════════════
    // SHEET 4: All Orders
    // ═══════════════════════════════════
    $sheet4 = $spreadsheet->createSheet();
    $sheet4->setTitle('All Orders');

    $s4Headers = ['Order ID', 'Date', 'Customer', 'Customer Email', 'Product', 'Quantity', 'Unit Price (₦)', 'Subtotal (₦)', 'Status'];
    foreach ($s4Headers as $i => $h) {
        $sheet4->setCellValue(chr(65 + $i) . '1', $h);
    }
    $lastColIdx = count($s4Headers);
    $styleHeader($sheet4, $lastColIdx, 1);

    $row = 2;
    foreach ($allOrders as $oi) {
        $sheet4->setCellValue("A{$row}", '#' . $oi->order_id);
        $sheet4->setCellValue("B{$row}", $oi->created_at->format('Y-m-d H:i'));
        $sheet4->setCellValue("C{$row}", $oi->order->user->name ?? 'Guest');
        $sheet4->setCellValue("D{$row}", $oi->order->user->email ?? '—');
        $sheet4->setCellValue("E{$row}", $oi->product->name ?? '—');
        $sheet4->setCellValue("F{$row}", $oi->quantity);
        $sheet4->setCellValue("G{$row}", $oi->product->price ?? 0);
        $sheet4->getStyle("G{$row}")->getNumberFormat()->setFormatCode($nairaFormat);
        $sheet4->setCellValue("H{$row}", $oi->subtotal);
        $sheet4->getStyle("H{$row}")->getNumberFormat()->setFormatCode($nairaFormat);
        $sheet4->setCellValue("I{$row}", ucfirst($oi->status));
        $row++;
    }
    // Summary row
    $sheet4->setCellValue("A{$row}", 'TOTAL');
    $sheet4->setCellValue("F{$row}", $allOrders->sum('quantity'));
    $sheet4->setCellValue("H{$row}", $allOrders->sum('subtotal'));
    $sheet4->getStyle("H{$row}")->getNumberFormat()->setFormatCode($nairaFormat);
    $sheet4->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
    $sheet4->getStyle("A{$row}:I{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('EEF2FF');
    $applyBorders($sheet4, $lastColIdx, 1, $row);
    for ($i = 1; $i <= $lastColIdx; $i++) {
        $sheet4->getColumnDimension(chr(64 + $i))->setAutoSize(true);
    }

    // ═══════════════════════════════════
    // SHEET 5: Product Performance
    // ═══════════════════════════════════
    $sheet5 = $spreadsheet->createSheet();
    $sheet5->setTitle('Product Performance');

    $s5Headers = ['Product Name', 'Category', 'Price (₦)', 'Stock', 'Status', 'Total Units Sold', 'Total Revenue (₦)', 'Created Date'];
    foreach ($s5Headers as $i => $h) {
        $sheet5->setCellValue(chr(65 + $i) . '1', $h);
    }
    $styleHeader($sheet5, 8, 1);

    $row = 2;
    foreach ($productPerformance as $pp) {
        $sheet5->setCellValue("A{$row}", $pp->name);
        $sheet5->setCellValue("B{$row}", $pp->category->name ?? 'N/A');
        $sheet5->setCellValue("C{$row}", $pp->price);
        $sheet5->getStyle("C{$row}")->getNumberFormat()->setFormatCode($nairaFormat);
        $sheet5->setCellValue("D{$row}", $pp->stock ?? 0);
        $sheet5->setCellValue("E{$row}", ucfirst($pp->status ?? 'active'));
        $sheet5->setCellValue("F{$row}", $pp->total_sold);
        $sheet5->setCellValue("G{$row}", $pp->total_revenue);
        $sheet5->getStyle("G{$row}")->getNumberFormat()->setFormatCode($nairaFormat);
        $sheet5->setCellValue("H{$row}", $pp->created_at ? $pp->created_at->format('Y-m-d') : '—');
        $row++;
    }
    $applyBorders($sheet5, 8, 1, $row - 1);

    // Set first sheet active
    $spreadsheet->setActiveSheetIndex(0);

    // Write to response
    $filename = "BuyNiger_Analytics_{$vendor->store_name}_" . date('Y-m-d') . ".xlsx";
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    return response()->streamDownload(function() use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}    

    /**
     * Update stock level via AJAX or form.
     */
    public function updateStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'change' => 'required|integer',
            'type' => 'required|string|in:restock,adjustment,return',
            'reason' => 'nullable|string|max:255',
        ]);

        $vendor = Auth::user()->vendor;
        $product = Product::where('vendor_id', $vendor->id)->findOrFail($request->product_id);

        if ($request->variant_id) {
            $variant = $product->variants()->findOrFail($request->variant_id);
            $oldStock = $variant->stock_quantity;
            $newStock = $oldStock + $request->change;
            $variant->update(['stock_quantity' => $newStock]);
            
            // Log history
            StockHistory::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'change_amount' => $request->change,
                'new_stock_level' => $newStock,
                'type' => $request->type,
                'reason' => $request->reason,
                'user_id' => Auth::id()
            ]);
        } else {
            $oldStock = $product->quantity;
            $newStock = $oldStock + $request->change;
            $product->update(['quantity' => $newStock]);
            
            // Log history
            StockHistory::create([
                'product_id' => $product->id,
                'change_amount' => $request->change,
                'new_stock_level' => $newStock,
                'type' => $request->type,
                'reason' => $request->reason,
                'user_id' => Auth::id()
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'new_stock' => $newStock]);
        }

        return back()->with('success', 'Stock updated successfully!');
    }

    /**
     * Update order item status.
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $vendor = Auth::user()->vendor;
        
        $orderItem = OrderItem::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->firstOrFail();

        $oldStatus = $orderItem->status;
        $orderItem->update(['status' => $request->status]);

        // If shipped, add tracking number
        if ($request->status == 'shipped' && $request->tracking_number) {
            $orderItem->update(['tracking_number' => $request->tracking_number]);
        }

        // Auto-payout: Credit vendor balance when order is delivered
        if ($request->status == 'delivered' && $oldStatus != 'delivered') {
            $amount = $orderItem->subtotal;

            // Credit vendor balance
            $vendor->increment('balance', $amount);

            // Create a completed payout record
            VendorPayout::create([
                'vendor_id' => $vendor->id,
                'amount' => $amount,
                'reference' => 'AUTO-' . strtoupper(\Str::random(10)),
                'status' => 'completed',
                'payment_method' => 'auto_credit',
                'payment_details' => [
                    'order_item_id' => $orderItem->id,
                    'order_id' => $orderItem->order_id,
                    'product_name' => $orderItem->product_name,
                    'note' => 'Automatically credited on delivery confirmation',
                ]
            ]);
        }

        return back()->with('success', 'Order status updated to ' . ucfirst($request->status));
    }

    /**
     * Export orders to CSV.
     */
    public function exportOrders()
    {
        $vendor = Auth::user()->vendor;
        $orderItems = OrderItem::where('vendor_id', $vendor->id)
            ->with(['order.user', 'product'])
            ->latest()
            ->get();

        $filename = "orders_export_" . date('Ymd_His') . ".csv";
        $handle = fopen('php://output', 'w');
        
        // Headers for download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'. $filename .'"');
        
        fputcsv($handle, ['Order ID', 'Product', 'Customer', 'Quantity', 'Price', 'Status', 'Date']);
        
        foreach ($orderItems as $item) {
            fputcsv($handle, [
                $item->order->order_number,
                $item->product->name,
                $item->order->user->name,
                $item->quantity,
                $item->price,
                $item->status,
                $item->created_at->format('Y-m-d H:i')
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Display vendor store settings.
     */
    public function settings()
    {
        $vendor = Auth::user()->vendor;
        return view('vendor.settings', compact('vendor'));
    }

    /**
     * Update vendor store settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:4096',
            'bank_name' => 'nullable|string|max:100',
            'account_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:20',
            'delivery_fee' => 'nullable|numeric|min:0',
            'id_type' => 'nullable|in:national_id,drivers_license,international_passport,voters_card',
            'id_number' => 'nullable|string|max:50',
            'id_document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'nin' => 'nullable|string|max:11',
            'bvn' => 'nullable|string|max:11',
            'cac_number' => 'nullable|string|max:50',
            'cac_document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $vendor = Auth::user()->vendor;

        $data = $request->only([
            'store_name', 'description', 'address', 'city', 'state',
            'meta_title', 'meta_description', 'facebook', 'twitter', 'instagram',
            'delivery_fee',
            'id_type', 'id_number', 'nin', 'bvn', 'cac_number',
        ]);

        if ($request->hasFile('logo')) {
            if ($vendor->logo) {
                Storage::disk('public')->delete($vendor->logo);
            }
            $data['logo'] = $request->file('logo')->store('vendors/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($vendor->banner) {
                Storage::disk('public')->delete($vendor->banner);
            }
            $data['banner'] = $request->file('banner')->store('vendors/banners', 'public');
        }

        // Handle KYC document uploads
        if ($request->hasFile('id_document')) {
            if ($vendor->id_document_path) {
                Storage::disk('public')->delete($vendor->id_document_path);
            }
            $data['id_document_path'] = $request->file('id_document')->store('vendors/kyc', 'public');
        }

        if ($request->hasFile('cac_document')) {
            if ($vendor->cac_document_path) {
                Storage::disk('public')->delete($vendor->cac_document_path);
            }
            $data['cac_document_path'] = $request->file('cac_document')->store('vendors/kyc', 'public');
        }

        // If KYC data is being submitted, set status to pending for review
        $kycFields = ['id_type', 'id_number', 'nin', 'bvn'];
        $hasKycData = collect($kycFields)->contains(fn($f) => !empty($data[$f]));
        if ($hasKycData && ($vendor->kyc_status ?? 'not_submitted') !== 'verified') {
            $data['kyc_status'] = 'pending';
        }

        $vendor->update($data);

        // Update or create primary bank detail
        if ($request->bank_name && $request->account_number) {
            VendorBankDetail::updateOrCreate(
                ['vendor_id' => $vendor->id, 'is_primary' => true],
                [
                    'bank_name' => $request->bank_name,
                    'account_name' => $request->account_name,
                    'account_number' => $request->account_number,
                ]
            );
        }

        return back()->with('success', 'Store settings updated successfully!');
    }
    /**
     * Display vendor finance dashboard.
     */
    public function finances()
    {
        $vendor = Auth::user()->vendor;
        $orderItems = OrderItem::where('vendor_id', $vendor->id);
        
        $payouts = VendorPayout::where('vendor_id', $vendor->id)->latest()->paginate(10);
        $bankDetails = VendorBankDetail::where('vendor_id', $vendor->id)->get();

        $stats = [
            'total_earned' => $orderItems->clone()->where('status', 'delivered')->sum('subtotal'),
            'pending_payout' => $orderItems->clone()->whereIn('status', ['processing', 'shipped'])->sum('subtotal'),
            'available_balance' => $vendor->balance,
        ];

        return view('vendor.finances', compact('vendor', 'payouts', 'bankDetails', 'stats'));
    }

    /**
     * Handle payout request.
     */
    public function requestPayout(Request $request)
    {
        $vendor = Auth::user()->vendor;
        if (($vendor->kyc_status ?? 'not_submitted') !== 'verified') {
            return back()->with('error', 'You must complete KYC verification before requesting payouts.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'bank_detail_id' => 'required|exists:vendor_bank_details,id'
        ]);

        $vendor = Auth::user()->vendor;

        if ($request->amount > $vendor->balance) {
            return back()->with('error', 'Insufficient balance for this payout request.');
        }

        // Create payout record
        VendorPayout::create([
            'vendor_id' => $vendor->id,
            'amount' => $request->amount,
            'reference' => 'PAY-' . strtoupper(Str::random(10)),
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
            'payment_details' => [
                'bank_detail_id' => $request->bank_detail_id
            ]
        ]);

        // Deduct from balance
        $vendor->decrement('balance', $request->amount);

        return back()->with('success', 'Payout request submitted successfully! It will be processed within 24-48 hours.');
    }

    /**
     * Display vendor profile (personal settings).
     */
    public function profile()
    {
        $user = Auth::user();
        return view('vendor.profile', compact('user'));
    }

    /**
     * Update vendor profile (personal settings).
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Bulk actions for products.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);

        $vendor = Auth::user()->vendor;
        $products = Product::where('vendor_id', $vendor->id)->whereIn('id', $request->ids)->get();

        foreach ($products as $product) {
            if ($request->action === 'delete') {
                foreach ($product->images as $image) {
                     Storage::disk('public')->delete($image->image_path);
                }
                $product->delete();
            } elseif ($request->action === 'activate') {
                $product->update(['status' => 'active']);
            } elseif ($request->action === 'deactivate') {
                $product->update(['status' => 'inactive']);
            }
        }

        return back()->with('success', 'Bulk action completed successfully!');
    }

    /**
     * List vendor products.
     */
    public function products(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $query = Product::where('vendor_id', $vendor->id)->with(['category', 'images']);

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by Status
        if ($request->filled('status')) {
            if ($request->status === 'out_of_stock') {
                $query->where('quantity', '<=', 0);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'stock_low':
                $query->orderBy('quantity', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(15)->withQueryString();
            
        return view('vendor.products.index', compact('products', 'categories', 'sort'));
    }

    /**
     * Show create product form.
     */
    public function createProduct()
    {
        $vendor = Auth::user()->vendor;
        if (($vendor->kyc_status ?? 'not_submitted') !== 'verified') {
            return redirect()->route('vendor.dashboard')->with('error', 'You must complete KYC verification before adding products.');
        }

        $categories = Category::where('is_active', true)->get();
        return view('vendor.products.create', compact('categories'));
    }

    /**
     * Store new product.
     */
    public function storeProduct(Request $request)
    {
        $vendor = Auth::user()->vendor;
        if (($vendor->kyc_status ?? 'not_submitted') !== 'verified') {
            return redirect()->route('vendor.dashboard')->with('error', 'You must complete KYC verification before adding products.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'tags' => 'nullable|string',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
        ]);

        $vendor = Auth::user()->vendor;

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name . '-' . Str::random(6)),
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->compare_price ?? null,
            'quantity' => $request->quantity,
            'sku' => $request->sku ?? Str::upper(Str::random(8)),
            'status' => 'active',
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        // Handle Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index
                ]);
            }
        }

        // Handle Variants
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                if (!empty($variant['size']) || !empty($variant['color'])) {
                    $product->variants()->create([
                        'size' => $variant['size'],
                        'color' => $variant['color'],
                        'price' => !empty($variant['price']) ? $variant['price'] : null,
                        'stock_quantity' => $variant['stock'] ?? 0,
                        'sku' => !empty($variant['sku']) ? $variant['sku'] : $product->sku . '-' . Str::upper(Str::random(4))
                    ]);
                }
            }
        }

        // Handle Tags
        if ($request->tags) {
            $tags = array_map('trim', explode(',', $request->tags));
            foreach ($tags as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(['name' => $tagName, 'slug' => Str::slug($tagName)]);
                    $product->tags()->attach($tag->id);
                }
            }
        }

        return redirect()->route('vendor.products')->with('success', 'Product published successfully!');
    }

    /**
     * Show edit product form.
     */
    public function editProduct($id)
    {
        $vendor = Auth::user()->vendor;
        if (($vendor->kyc_status ?? 'not_submitted') !== 'verified') {
            return redirect()->route('vendor.dashboard')->with('error', 'KYC verification required to edit products.');
        }

        $product = $vendor->products()->findOrFail($id);
        $categories = \App\Models\Category::where('is_active', true)->get();
        
        return view('vendor.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product.
     */
    public function updateProduct(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;
        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
        ]);

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->compare_price ?? null,
            'quantity' => $request->quantity,
            'sku' => $request->sku ?? $product->sku,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        // Handle New Images
        if ($request->hasFile('images')) {
            $nextOrder = $product->images()->max('sort_order') + 1;
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $nextOrder + $index
                ]);
            }
        }

        // Handle Reordering and Primary Image
        if ($request->has('image_order')) {
            foreach ($request->image_order as $index => $imageId) {
                $product->images()->where('id', $imageId)->update([
                    'sort_order' => $index,
                    'is_primary' => $index === 0
                ]);
            }
        }
        
        // Handle Image Deletion
        if ($request->delete_images) {
            foreach ($request->delete_images as $imgId) {
                $img = $product->images()->find($imgId);
                if ($img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }
        }

        // Handle Variants
        // For simplicity, we'll replace variants if they are sent. 
        // A more complex system would track IDs, but usually vendors re-add.
        if ($request->has('variants')) {
            $product->variants()->delete();
            foreach ($request->variants as $variant) {
                if (!empty($variant['size']) || !empty($variant['color'])) {
                    $product->variants()->create([
                        'size' => $variant['size'],
                        'color' => $variant['color'],
                        'price' => !empty($variant['price']) ? $variant['price'] : null,
                        'stock_quantity' => $variant['stock'] ?? 0,
                        'sku' => !empty($variant['sku']) ? $variant['sku'] : $product->sku . '-' . Str::upper(Str::random(4))
                    ]);
                }
            }
        }
        
        // Handle Tags
        if ($request->tags) {
            $product->tags()->detach();
            $tags = array_map('trim', explode(',', $request->tags));
            foreach ($tags as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(['name' => $tagName, 'slug' => Str::slug($tagName)]);
                    $product->tags()->attach($tag->id);
                }
            }
        }

        return redirect()->route('vendor.products')->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product.
     */
    public function destroyProduct($id)
    {
        $vendor = Auth::user()->vendor;
        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);
        
        // Delete images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $product->images()->delete();
        $product->delete();

        return back()->with('success', 'Product deleted successfully!');
    }

    /**
     * List vendor coupons.
     */
    public function coupons()
    {
        $vendor = Auth::user()->vendor;
        $coupons = \App\Models\Coupon::where('vendor_id', $vendor->id)->latest()->get();
        return view('vendor.coupons.index', compact('coupons'));
    }

    /**
     * Store new coupon.
     */
    public function storeCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:20',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_spend' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $vendor = Auth::user()->vendor;

        \App\Models\Coupon::create([
            'vendor_id' => $vendor->id,
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_spend' => $request->min_spend,
            'expires_at' => $request->expires_at,
            'usage_limit' => $request->usage_limit,
            'is_active' => true,
        ]);

        return back()->with('success', 'Coupon created successfully!');
    }

    /**
     * Delete coupon.
     */
    public function destroyCoupon($id)
    {
        $vendor = Auth::user()->vendor;
        $coupon = \App\Models\Coupon::where('vendor_id', $vendor->id)->findOrFail($id);
        $coupon->delete();

        return back()->with('success', 'Coupon deleted successfully!');
    }

    /**
     * Toggle coupon active status.
     */
    public function toggleCouponStatus(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;
        $coupon = \App\Models\Coupon::where('vendor_id', $vendor->id)->findOrFail($id);
        
        $coupon->is_active = $request->active ? true : false;
        $coupon->save();

        return response()->json(['success' => true]);
    }
}
