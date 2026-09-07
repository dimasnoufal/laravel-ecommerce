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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard overview with cached high-performance metrics.
     */
    public function index()
    {
        // Date range: Current 7 days window
        $now = Carbon::now();
        $startDate = $now->copy()->subDays(6)->startOfDay();
        $endDate = $now->copy()->endOfDay();
        $dateKey = $startDate->format('Y-m-d');

        // Valid statuses for revenue calculation
        $validStatuses = [
            OrderStatus::CONFIRMED->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::DELIVERED->value,
        ];

        // 1. KPI Metrics (Cached for 60 seconds)
        $metrics = Cache::remember("admin_dashboard_metrics_{$dateKey}", 60, function () use ($startDate, $endDate, $validStatuses) {
            $prevStartDate = $startDate->copy()->subDays(7)->startOfDay();
            $prevEndDate = $startDate->copy()->subSecond();

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

            $totalOrdersCount = Order::count();
            $recentOrdersCount = Order::whereBetween('created_at', [$startDate, $endDate])->count();
            $prevOrdersCount = Order::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
            $ordersGrowth = $prevOrdersCount > 0
                ? round((($recentOrdersCount - $prevOrdersCount) / $prevOrdersCount) * 100, 1)
                : ($recentOrdersCount > 0 ? 100 : 0);

            $pendingOrdersCount = Order::whereIn('status', [OrderStatus::PENDING->value, OrderStatus::PROCESSING->value])->count();

            $totalProductsCount = Product::count();
            $lowStockVariantsCount = ProductVariant::where('stock', '<=', 5)->count();

            $totalCustomersCount = User::whereHas('roles', function ($query) {
                $query->where('slug', 'customer');
            })->count();

            if ($totalCustomersCount === 0) {
                $totalCustomersCount = User::count();
            }

            return [
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
        });

        // 2. Trend Chart Aggregation (Cached for 60 seconds)
        $chartData = Cache::remember("admin_dashboard_chart_{$dateKey}", 60, function () use ($startDate, $endDate, $validStatuses, $now) {
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
                $dayKey = $now->copy()->subDays($i)->format('Y-m-d');
                $displayLabel = $now->copy()->subDays($i)->format('D, d M');

                $chartLabels[] = $displayLabel;
                $chartRevenueData[] = isset($dailyOrders[$dayKey]) ? (float) $dailyOrders[$dayKey]->total_revenue : 0;
                $chartOrderData[] = isset($dailyOrders[$dayKey]) ? (int) $dailyOrders[$dayKey]->total_orders : 0;
            }

            return [
                'labels' => $chartLabels,
                'revenue' => $chartRevenueData,
                'orders' => $chartOrderData,
            ];
        });

        // 3. Category Breakdown (Single query with withCount, Cached for 60 seconds)
        $topCategories = Cache::remember('admin_dashboard_categories', 60, function () use ($metrics) {
            $categories = Category::withCount('products')
                ->latest()
                ->take(5)
                ->get();

            $categoryColors = ['#2563EB', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
            $result = [];
            $totalProductsCount = $metrics['total_products'] ?? 0;
            $totalCatCount = max(1, $categories->count());

            foreach ($categories as $index => $category) {
                $productCount = (int) $category->products_count;
                $percentage = $totalProductsCount > 0 
                    ? round(($productCount / $totalProductsCount) * 100) 
                    : round(100 / $totalCatCount);

                $result[] = [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'products_count' => $productCount,
                    'percentage' => $percentage,
                    'color' => $categoryColors[$index % count($categoryColors)],
                ];
            }

            return $result;
        });

        // 4. Recent Transactions (Always fast eager loading for top 6 items)
        $recentOrders = Order::with(['user', 'orderItems.productVariant.product'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact('metrics', 'chartData', 'topCategories', 'recentOrders'));
    }
}
