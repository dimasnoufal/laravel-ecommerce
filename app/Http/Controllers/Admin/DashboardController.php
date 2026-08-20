<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\User;
use App\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard overview with real database metrics.
     */
    public function index()
    {
        // Date range: Current 7 days window
        $now = Carbon::now();
        $startDate = $now->copy()->subDays(6)->startOfDay();
        $endDate = $now->copy()->endOfDay();

        // Previous 7 days window for % growth calculation
        $prevStartDate = $startDate->copy()->subDays(7)->startOfDay();
        $prevEndDate = $startDate->copy()->subSecond();

        // 1. KPI Metrik: Net Revenue (Confirmed, Processing, Shipped, Delivered orders)
        $validStatuses = [
            OrderStatus::CONFIRMED->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::DELIVERED->value,
        ];

        $currentRevenue = (float) Order::whereIn('status', $validStatuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $allTimeRevenue = (float) Order::whereIn('status', $validStatuses)
            ->sum('total_amount');

        $prevRevenue = (float) Order::whereIn('status', $validStatuses)
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->sum('total_amount');

        $revenueGrowth = $prevRevenue > 0 
            ? round((($currentRevenue - $prevRevenue) / $prevRevenue) * 100, 1) 
            : ($currentRevenue > 0 ? 100 : 0);

        // 2. KPI Metrik: Total Orders & Pending Alerts
        $totalOrdersCount = Order::count();
        $recentOrdersCount = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $prevOrdersCount = Order::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $ordersGrowth = $prevOrdersCount > 0
            ? round((($recentOrdersCount - $prevOrdersCount) / $prevOrdersCount) * 100, 1)
            : ($recentOrdersCount > 0 ? 100 : 0);

        $pendingOrdersCount = Order::whereIn('status', [OrderStatus::PENDING->value, OrderStatus::PROCESSING->value])->count();

        // 3. KPI Metrik: Products & Low Stock Alert
        $totalProductsCount = Product::count();
        $lowStockVariantsCount = ProductVariant::where('stock', '<=', 5)->count();

        // 4. KPI Metrik: Total Customers
        $totalCustomersCount = User::whereHas('roles', function ($query) {
            $query->where('slug', 'customer');
        })->count();

        // Fallback: If roles are not yet seeded, count all non-admin users
        if ($totalCustomersCount === 0) {
            $totalCustomersCount = User::count();
        }

        // 5. Trend Chart Aggregation: Last 7 Days Daily Revenue & Order Count
        $dailyOrders = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN status IN (\'' . implode("','", $validStatuses) . '\') THEN total_amount ELSE 0 END) as total_revenue')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartRevenueData = [];
        $chartOrderData = [];

        for ($i = 6; $i >= 0; $i--) {
            $dateKey = $now->copy()->subDays($i)->format('Y-m-d');
            $displayLabel = $now->copy()->subDays($i)->format('D, d M');

            $chartLabels[] = $displayLabel;
            $chartRevenueData[] = isset($dailyOrders[$dateKey]) ? (float) $dailyOrders[$dateKey]->total_revenue : 0;
            $chartOrderData[] = isset($dailyOrders[$dateKey]) ? (int) $dailyOrders[$dateKey]->total_orders : 0;
        }

        $chartData = [
            'labels' => $chartLabels,
            'revenue' => $chartRevenueData,
            'orders' => $chartOrderData,
        ];

        // 6. Category Breakdown
        $categories = Category::withCount('children')
            ->latest()
            ->take(5)
            ->get();

        $categoryColors = ['#2563EB', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
        $topCategories = [];
        $totalCatCount = max(1, $categories->count());

        foreach ($categories as $index => $category) {
            // Count products in this category
            $productCount = Product::where('category_id', $category->id)->count();
            $percentage = $totalProductsCount > 0 ? round(($productCount / $totalProductsCount) * 100) : round(100 / $totalCatCount);

            $topCategories[] = [
                'name' => $category->name,
                'slug' => $category->slug,
                'products_count' => $productCount,
                'percentage' => $percentage,
                'color' => $categoryColors[$index % count($categoryColors)],
            ];
        }

        // 7. Recent Transactions / Orders
        $recentOrders = Order::with(['user', 'orderItems.productVariant.product'])
            ->latest()
            ->take(6)
            ->get();

        $metrics = [
            'all_time_revenue' => $allTimeRevenue,
            'current_revenue' => $currentRevenue,
            'revenue_growth' => $revenueGrowth,
            'total_orders' => $totalOrdersCount,
            'orders_growth' => $ordersGrowth,
            'pending_orders' => $pendingOrdersCount,
            'total_products' => $totalProductsCount,
            'low_stock_count' => $lowStockVariantsCount,
            'total_customers' => $totalCustomersCount,
            'date_range_label' => $startDate->format('d M') . ' - ' . $endDate->format('d M Y'),
        ];

        return view('admin.dashboard', compact('metrics', 'chartData', 'topCategories', 'recentOrders'));
    }
}
