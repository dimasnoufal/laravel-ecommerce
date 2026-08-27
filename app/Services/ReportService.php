<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReportType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\Report;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportService
{
    /**
     * Cache TTL in seconds (30 minutes)
     */
    protected int $cacheTtl = 1800;

    /**
     * Get aggregate report metrics and charts for a specific type and date window.
     */
    public function getReportData(string $type, Carbon $startDate, Carbon $endDate, bool $forceRefresh = false): array
    {
        $type = strtoupper($type);
        $cacheKey = "reports:v2:{$type}:{$startDate->toDateString()}:{$endDate->toDateString()}";

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($type, $startDate, $endDate) {
            return match ($type) {
                ReportType::SALES->value => $this->getSalesReport($startDate, $endDate),
                ReportType::ORDERS->value => $this->getOrdersReport($startDate, $endDate),
                ReportType::INVENTORY->value => $this->getInventoryReport($startDate, $endDate),
                ReportType::PAYMENTS->value => $this->getPaymentsReport($startDate, $endDate),
                ReportType::CUSTOMERS->value => $this->getCustomersReport($startDate, $endDate),
                default => $this->getSalesReport($startDate, $endDate),
            };
        });
    }

    /**
     * Flush cache for a specific report filter.
     */
    public function clearCache(string $type, Carbon $startDate, Carbon $endDate): void
    {
        $type = strtoupper($type);
        $cacheKey = "reports:v2:{$type}:{$startDate->toDateString()}:{$endDate->toDateString()}";
        Cache::forget($cacheKey);
    }

    /**
     * 1. SALES REPORT AGGREGATION
     */
    public function getSalesReport(Carbon $startDate, Carbon $endDate): array
    {
        $validStatuses = [
            OrderStatus::CONFIRMED->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::DELIVERED->value,
        ];

        // Overall KPIs
        $salesAggregates = Order::whereIn('status', $validStatuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COALESCE(SUM(total_amount), 0) as gross_sales,
                COALESCE(SUM(subtotal), 0) as net_sales,
                COALESCE(SUM(shipping_cost), 0) as total_shipping,
                COALESCE(SUM(discount_amount), 0) as total_discount,
                COUNT(*) as total_orders
            ')
            ->first();

        $grossSales = (float) ($salesAggregates->gross_sales ?? 0);
        $netSales = (float) ($salesAggregates->net_sales ?? 0);
        $totalShipping = (float) ($salesAggregates->total_shipping ?? 0);
        $totalDiscount = (float) ($salesAggregates->total_discount ?? 0);
        $totalOrders = (int) ($salesAggregates->total_orders ?? 0);
        $aov = $totalOrders > 0 ? round($grossSales / $totalOrders, 2) : 0;

        // Daily Trend Series
        $dailySales = Order::whereIn('status', $validStatuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COALESCE(SUM(total_amount), 0) as revenue, COUNT(*) as orders_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];

        $periodDays = max(1, $startDate->diffInDays($endDate) + 1);
        for ($i = 0; $i < $periodDays; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $label = $startDate->copy()->addDays($i)->format('d M');
            $chartLabels[] = $label;
            $chartRevenue[] = isset($dailySales[$date]) ? (float) $dailySales[$date]->revenue : 0;
            $chartOrders[] = isset($dailySales[$date]) ? (int) $dailySales[$date]->orders_count : 0;
        }

        // Top Selling Products in Period (Pure Array)
        $topProducts = OrderItem::whereHas('order', function ($query) use ($validStatuses, $startDate, $endDate) {
            $query->whereIn('status', $validStatuses)
                  ->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->select('product_name', 'sku', DB::raw('SUM(quantity) as units_sold'), DB::raw('SUM(subtotal) as total_revenue'))
        ->groupBy('product_name', 'sku')
        ->orderByDesc('units_sold')
        ->take(8)
        ->get()
        ->map(fn($p) => [
            'product_name' => (string) $p->product_name,
            'sku' => (string) $p->sku,
            'units_sold' => (int) $p->units_sold,
            'total_revenue' => (float) $p->total_revenue,
        ])
        ->toArray();

        return [
            'type' => ReportType::SALES->value,
            'title' => 'Sales & Revenue Report',
            'kpis' => [
                ['label' => 'Gross Revenue', 'value' => 'Rp ' . number_format($grossSales, 0, ',', '.'), 'raw' => $grossSales, 'icon' => 'banknote', 'color' => 'primary', 'desc' => 'Total transacted revenue'],
                ['label' => 'Net Product Sales', 'value' => 'Rp ' . number_format($netSales, 0, ',', '.'), 'raw' => $netSales, 'icon' => 'shopping-bag', 'color' => 'success', 'desc' => 'Product subtotal'],
                ['label' => 'Avg Order Value (AOV)', 'value' => 'Rp ' . number_format($aov, 0, ',', '.'), 'raw' => $aov, 'icon' => 'trending-up', 'color' => 'info', 'desc' => 'Mean spend per transaction'],
                ['label' => 'Total Discounts Given', 'value' => 'Rp ' . number_format($totalDiscount, 0, ',', '.'), 'raw' => $totalDiscount, 'icon' => 'percent', 'color' => 'warning', 'desc' => 'Voucher & promo savings'],
            ],
            'chart' => [
                'type' => 'mixed',
                'labels' => $chartLabels,
                'datasets' => [
                    ['name' => 'Revenue', 'type' => 'area', 'data' => $chartRevenue, 'color' => '#2563EB'],
                    ['name' => 'Orders', 'type' => 'bar', 'data' => $chartOrders, 'color' => '#10B981'],
                ]
            ],
            'top_products' => $topProducts,
            'summary_meta' => [
                'gross_sales' => $grossSales,
                'net_sales' => $netSales,
                'total_orders' => $totalOrders,
                'aov' => $aov,
                'total_shipping' => $totalShipping,
                'total_discount' => $totalDiscount,
            ]
        ];
    }

    /**
     * 2. ORDERS REPORT AGGREGATION
     */
    public function getOrdersReport(Carbon $startDate, Carbon $endDate): array
    {
        $ordersAgg = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("
                COUNT(*) as total_orders,
                COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending_count,
                COUNT(CASE WHEN status = 'CONFIRMED' THEN 1 END) as confirmed_count,
                COUNT(CASE WHEN status = 'PROCESSING' THEN 1 END) as processing_count,
                COUNT(CASE WHEN status = 'SHIPPED' THEN 1 END) as shipped_count,
                COUNT(CASE WHEN status = 'DELIVERED' THEN 1 END) as delivered_count,
                COUNT(CASE WHEN status = 'CANCELLED' THEN 1 END) as cancelled_count
            ")
            ->first();

        $totalOrders = (int) ($ordersAgg->total_orders ?? 0);
        $deliveredCount = (int) ($ordersAgg->delivered_count ?? 0);
        $cancelledCount = (int) ($ordersAgg->cancelled_count ?? 0);
        $processingCount = (int) ($ordersAgg->processing_count ?? 0) + (int) ($ordersAgg->confirmed_count ?? 0) + (int) ($ordersAgg->shipped_count ?? 0);
        $pendingCount = (int) ($ordersAgg->pending_count ?? 0);

        $fulfillmentRate = $totalOrders > 0 ? round(($deliveredCount / $totalOrders) * 100, 1) : 0;
        $cancellationRate = $totalOrders > 0 ? round(($cancelledCount / $totalOrders) * 100, 1) : 0;

        // Daily Orders Trend
        $dailyOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("
                DATE(created_at) as date,
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'DELIVERED' THEN 1 END) as completed,
                COUNT(CASE WHEN status = 'CANCELLED' THEN 1 END) as cancelled
            ")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $totalOrdersSeries = [];
        $completedOrdersSeries = [];
        $cancelledOrdersSeries = [];

        $periodDays = max(1, $startDate->diffInDays($endDate) + 1);
        for ($i = 0; $i < $periodDays; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $chartLabels[] = $startDate->copy()->addDays($i)->format('d M');
            $totalOrdersSeries[] = isset($dailyOrders[$date]) ? (int) $dailyOrders[$date]->total : 0;
            $completedOrdersSeries[] = isset($dailyOrders[$date]) ? (int) $dailyOrders[$date]->completed : 0;
            $cancelledOrdersSeries[] = isset($dailyOrders[$date]) ? (int) $dailyOrders[$date]->cancelled : 0;
        }

        $statusDistribution = [
            'Delivered' => $deliveredCount,
            'In Progress' => $processingCount,
            'Pending' => $pendingCount,
            'Cancelled' => $cancelledCount,
        ];

        return [
            'type' => ReportType::ORDERS->value,
            'title' => 'Orders & Fulfillment Report',
            'kpis' => [
                ['label' => 'Total Orders', 'value' => number_format($totalOrders), 'raw' => $totalOrders, 'icon' => 'shopping-cart', 'color' => 'primary', 'desc' => 'All placed orders'],
                ['label' => 'Delivered (Completed)', 'value' => number_format($deliveredCount), 'raw' => $deliveredCount, 'icon' => 'check-circle-2', 'color' => 'success', 'desc' => "{$fulfillmentRate}% fulfillment rate"],
                ['label' => 'In Processing & Shipped', 'value' => number_format($processingCount), 'raw' => $processingCount, 'icon' => 'truck', 'color' => 'info', 'desc' => 'Active orders in pipeline'],
                ['label' => 'Cancelled Rate', 'value' => "{$cancellationRate}% ({$cancelledCount})", 'raw' => $cancellationRate, 'icon' => 'x-circle', 'color' => 'danger', 'desc' => 'Unfulfilled / voided orders'],
            ],
            'chart' => [
                'type' => 'bar',
                'labels' => $chartLabels,
                'datasets' => [
                    ['name' => 'Total Placed', 'type' => 'bar', 'data' => $totalOrdersSeries, 'color' => '#2563EB'],
                    ['name' => 'Delivered', 'type' => 'bar', 'data' => $completedOrdersSeries, 'color' => '#10B981'],
                    ['name' => 'Cancelled', 'type' => 'bar', 'data' => $cancelledOrdersSeries, 'color' => '#EF4444'],
                ]
            ],
            'status_distribution' => $statusDistribution,
            'summary_meta' => [
                'total_orders' => $totalOrders,
                'delivered_count' => $deliveredCount,
                'processing_count' => $processingCount,
                'pending_count' => $pendingCount,
                'cancelled_count' => $cancelledCount,
                'fulfillment_rate' => $fulfillmentRate,
                'cancellation_rate' => $cancellationRate,
            ]
        ];
    }

    /**
     * 3. INVENTORY REPORT AGGREGATION
     */
    public function getInventoryReport(Carbon $startDate, Carbon $endDate): array
    {
        $stockAgg = ProductVariant::selectRaw('
            COUNT(*) as total_variants,
            COALESCE(SUM(stock * price), 0) as total_asset_value,
            COALESCE(SUM(stock), 0) as total_units_in_stock,
            COUNT(CASE WHEN stock <= 5 AND stock > 0 THEN 1 END) as low_stock_count,
            COUNT(CASE WHEN stock = 0 THEN 1 END) as out_of_stock_count
        ')->first();

        $totalVariants = (int) ($stockAgg->total_variants ?? 0);
        $totalAssetValue = (float) ($stockAgg->total_asset_value ?? 0);
        $totalUnits = (int) ($stockAgg->total_units_in_stock ?? 0);
        $lowStockCount = (int) ($stockAgg->low_stock_count ?? 0);
        $outOfStockCount = (int) ($stockAgg->out_of_stock_count ?? 0);

        // Stock Movements in date range
        $movementsAgg = StockMovement::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                type,
                COUNT(*) as movement_count,
                COALESCE(SUM(quantity), 0) as total_quantity
            ')
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        // Low stock variant items for alert display (Pure Array)
        $lowStockItems = ProductVariant::with('product')
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(10)
            ->get()
            ->map(fn($v) => [
                'product_name' => (string) ($v->product->name ?? 'N/A'),
                'sku' => (string) $v->sku,
                'stock' => (int) $v->stock,
            ])
            ->toArray();

        return [
            'type' => ReportType::INVENTORY->value,
            'title' => 'Inventory Valuation & Stock Health Report',
            'kpis' => [
                ['label' => 'Total Inventory Value', 'value' => 'Rp ' . number_format($totalAssetValue, 0, ',', '.'), 'raw' => $totalAssetValue, 'icon' => 'layers', 'color' => 'primary', 'desc' => 'Total valuation (units × price)'],
                ['label' => 'Total In-Stock Units', 'value' => number_format($totalUnits) . ' units', 'raw' => $totalUnits, 'icon' => 'box', 'color' => 'success', 'desc' => "Across {$totalVariants} active SKUs"],
                ['label' => 'Low Stock Warning', 'value' => number_format($lowStockCount) . ' SKUs', 'raw' => $lowStockCount, 'icon' => 'alert-triangle', 'color' => 'warning', 'desc' => 'Stock is <= 5 units'],
                ['label' => 'Out of Stock', 'value' => number_format($outOfStockCount) . ' SKUs', 'raw' => $outOfStockCount, 'icon' => 'slash', 'color' => 'danger', 'desc' => 'Zero stock available'],
            ],
            'chart' => [
                'type' => 'doughnut',
                'labels' => ['Healthy Stock (> 5)', 'Low Stock (1-5)', 'Out of Stock (0)'],
                'data' => [
                    max(0, $totalVariants - $lowStockCount - $outOfStockCount),
                    $lowStockCount,
                    $outOfStockCount
                ],
                'colors' => ['#10B981', '#F59E0B', '#EF4444']
            ],
            'low_stock_items' => $lowStockItems,
            'movements_summary' => $movementsAgg,
            'summary_meta' => [
                'total_asset_value' => $totalAssetValue,
                'total_units' => $totalUnits,
                'total_variants' => $totalVariants,
                'low_stock_count' => $lowStockCount,
                'out_of_stock_count' => $outOfStockCount,
            ]
        ];
    }

    /**
     * 4. PAYMENTS REPORT AGGREGATION
     */
    public function getPaymentsReport(Carbon $startDate, Carbon $endDate): array
    {
        $paymentsAgg = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("
                COUNT(*) as total_transactions,
                COALESCE(SUM(CASE WHEN status = 'PAID' THEN amount ELSE 0 END), 0) as paid_amount,
                COALESCE(SUM(CASE WHEN status = 'PENDING' THEN amount ELSE 0 END), 0) as pending_amount,
                COALESCE(SUM(CASE WHEN status IN ('FAILED', 'EXPIRED', 'CANCELLED') THEN amount ELSE 0 END), 0) as failed_amount,
                COUNT(CASE WHEN status = 'PAID' THEN 1 END) as paid_count,
                COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending_count,
                COUNT(CASE WHEN status IN ('FAILED', 'EXPIRED', 'CANCELLED') THEN 1 END) as failed_count
            ")
            ->first();

        $totalTransactions = (int) ($paymentsAgg->total_transactions ?? 0);
        $paidAmount = (float) ($paymentsAgg->paid_amount ?? 0);
        $pendingAmount = (float) ($paymentsAgg->pending_amount ?? 0);
        $failedAmount = (float) ($paymentsAgg->failed_amount ?? 0);
        $paidCount = (int) ($paymentsAgg->paid_count ?? 0);
        $successRate = $totalTransactions > 0 ? round(($paidCount / $totalTransactions) * 100, 1) : 0;

        // Payment Provider breakdown (Pure Array)
        $providers = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->select('provider', DB::raw('COUNT(*) as count'), DB::raw('COALESCE(SUM(amount), 0) as total_amount'))
            ->groupBy('provider')
            ->orderByDesc('total_amount')
            ->get()
            ->map(fn($p) => [
                'provider' => (string) ($p->provider ?: 'Direct'),
                'count' => (int) $p->count,
                'total_amount' => (float) $p->total_amount,
            ])
            ->toArray();

        $providerLabels = [];
        $providerAmounts = [];
        foreach ($providers as $p) {
            $providerLabels[] = strtoupper($p['provider']);
            $providerAmounts[] = (float) $p['total_amount'];
        }

        return [
            'type' => ReportType::PAYMENTS->value,
            'title' => 'Payments & Financial Settlements Report',
            'kpis' => [
                ['label' => 'Total Paid / Settled', 'value' => 'Rp ' . number_format($paidAmount, 0, ',', '.'), 'raw' => $paidAmount, 'icon' => 'credit-card', 'color' => 'success', 'desc' => "{$paidCount} successful payments"],
                ['label' => 'Pending Settlements', 'value' => 'Rp ' . number_format($pendingAmount, 0, ',', '.'), 'raw' => $pendingAmount, 'icon' => 'clock', 'color' => 'warning', 'desc' => 'Awaiting payment verification'],
                ['label' => 'Failed / Expired Amount', 'value' => 'Rp ' . number_format($failedAmount, 0, ',', '.'), 'raw' => $failedAmount, 'icon' => 'alert-circle', 'color' => 'danger', 'desc' => 'Unsuccessful transactions'],
                ['label' => 'Payment Success Rate', 'value' => "{$successRate}%", 'raw' => $successRate, 'icon' => 'shield-check', 'color' => 'primary', 'desc' => "From {$totalTransactions} total attempts"],
            ],
            'chart' => [
                'type' => 'pie',
                'labels' => count($providerLabels) > 0 ? $providerLabels : ['No Data'],
                'data' => count($providerAmounts) > 0 ? $providerAmounts : [1],
                'colors' => ['#2563EB', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#64748B']
            ],
            'providers' => $providers,
            'summary_meta' => [
                'paid_amount' => $paidAmount,
                'pending_amount' => $pendingAmount,
                'failed_amount' => $failedAmount,
                'total_transactions' => $totalTransactions,
                'paid_count' => $paidCount,
                'success_rate' => $successRate,
            ]
        ];
    }

    /**
     * 5. CUSTOMERS REPORT AGGREGATION
     */
    public function getCustomersReport(Carbon $startDate, Carbon $endDate): array
    {
        $totalCustomers = User::whereHas('roles', function ($q) {
            $q->where('slug', 'customer');
        })->count();

        if ($totalCustomers === 0) {
            $totalCustomers = User::count();
        }

        $newCustomers = User::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('roles', function ($q) {
                $q->where('slug', 'customer');
            })->count();

        if ($newCustomers === 0) {
            $newCustomers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        }

        // Active transacting buyers in period
        $activeBuyers = Order::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('user_id')
            ->count('user_id');

        // Customer spend aggregates
        $totalSpendInPeriod = (float) Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::SHIPPED->value, OrderStatus::DELIVERED->value])
            ->sum('total_amount');

        $avgCustomerSpend = $activeBuyers > 0 ? round($totalSpendInPeriod / $activeBuyers, 2) : 0;

        // Top Spenders Leaderboard in period (Pure Array)
        $topSpenders = User::select('users.id', 'users.name', 'users.email', 'users.created_at', DB::raw('COUNT(orders.id) as orders_count'), DB::raw('COALESCE(SUM(orders.total_amount), 0) as total_spent'))
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::SHIPPED->value, OrderStatus::DELIVERED->value])
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at')
            ->orderByDesc('total_spent')
            ->take(10)
            ->get()
            ->map(fn($u) => [
                'id' => (int) $u->id,
                'name' => (string) $u->name,
                'email' => (string) $u->email,
                'orders_count' => (int) $u->orders_count,
                'total_spent' => (float) $u->total_spent,
            ])
            ->toArray();

        // Customer Signup Trend
        $dailySignups = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as signups_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartSignups = [];
        $periodDays = max(1, $startDate->diffInDays($endDate) + 1);
        for ($i = 0; $i < $periodDays; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $chartLabels[] = $startDate->copy()->addDays($i)->format('d M');
            $chartSignups[] = isset($dailySignups[$date]) ? (int) $dailySignups[$date]->signups_count : 0;
        }

        return [
            'type' => ReportType::CUSTOMERS->value,
            'title' => 'Customer Acquisition & VIP Loyalty Report',
            'kpis' => [
                ['label' => 'Total Registered Base', 'value' => number_format($totalCustomers), 'raw' => $totalCustomers, 'icon' => 'users', 'color' => 'primary', 'desc' => 'All registered accounts'],
                ['label' => 'New Customers', 'value' => '+' . number_format($newCustomers), 'raw' => $newCustomers, 'icon' => 'user-plus', 'color' => 'success', 'desc' => 'Signups within selected dates'],
                ['label' => 'Active Transacting Buyers', 'value' => number_format($activeBuyers), 'raw' => $activeBuyers, 'icon' => 'user-check', 'color' => 'info', 'desc' => 'Placed at least 1 order'],
                ['label' => 'Avg Spend / Buyer', 'value' => 'Rp ' . number_format($avgCustomerSpend, 0, ',', '.'), 'raw' => $avgCustomerSpend, 'icon' => 'wallet', 'color' => 'warning', 'desc' => 'Revenue per active customer'],
            ],
            'chart' => [
                'type' => 'bar',
                'labels' => $chartLabels,
                'datasets' => [
                    ['name' => 'New Signups', 'type' => 'bar', 'data' => $chartSignups, 'color' => '#8B5CF6']
                ]
            ],
            'top_spenders' => $topSpenders,
            'summary_meta' => [
                'total_customers' => $totalCustomers,
                'new_customers' => $newCustomers,
                'active_buyers' => $activeBuyers,
                'avg_customer_spend' => $avgCustomerSpend,
                'total_spend' => $totalSpendInPeriod,
            ]
        ];
    }

    /**
     * Get paginated detailed records for tabular display.
     */
    public function getDetailRecords(string $type, Carbon $startDate, Carbon $endDate, int $perPage = 50)
    {
        $type = strtoupper($type);

        return match ($type) {
            ReportType::SALES->value => OrderItem::with(['order.user', 'productVariant.product'])
                ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                ->latest()
                ->paginate($perPage),

            ReportType::ORDERS->value => Order::with(['user', 'orderItems'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->latest()
                ->paginate($perPage),

            ReportType::INVENTORY->value => ProductVariant::with('product')
                ->latest()
                ->paginate($perPage),

            ReportType::PAYMENTS->value => Payment::with(['order.user'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->latest()
                ->paginate($perPage),

            ReportType::CUSTOMERS->value => User::withCount('orders')
                ->latest()
                ->paginate($perPage),

            default => Order::whereBetween('created_at', [$startDate, $endDate])->latest()->paginate($perPage),
        };
    }

    /**
     * Stream CSV download directly to browser with memory-efficient chunking.
     */
    public function streamCsv(string $type, Carbon $startDate, Carbon $endDate): void
    {
        $type = strtoupper($type);
        $handle = fopen('php://output', 'w');

        // Add UTF-8 BOM for Excel compatibility
        fputs($handle, "\xEF\xBB\xBF");

        match ($type) {
            ReportType::SALES->value => $this->streamSalesCsv($handle, $startDate, $endDate),
            ReportType::ORDERS->value => $this->streamOrdersCsv($handle, $startDate, $endDate),
            ReportType::INVENTORY->value => $this->streamInventoryCsv($handle),
            ReportType::PAYMENTS->value => $this->streamPaymentsCsv($handle, $startDate, $endDate),
            ReportType::CUSTOMERS->value => $this->streamCustomersCsv($handle, $startDate, $endDate),
            default => $this->streamSalesCsv($handle, $startDate, $endDate),
        };

        fclose($handle);
    }

    protected function streamSalesCsv($handle, Carbon $startDate, Carbon $endDate): void
    {
        fputcsv($handle, ['Order Number', 'Date', 'Customer Name', 'Customer Email', 'Product Name', 'SKU', 'Unit Price (IDR)', 'Quantity', 'Subtotal (IDR)', 'Order Status']);

        OrderItem::with(['order.user'])
            ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->lazy(200)
            ->each(function ($item) use ($handle) {
                fputcsv($handle, [
                    $item->order->order_number ?? 'N/A',
                    $item->order->created_at ? $item->order->created_at->format('Y-m-d H:i:s') : '-',
                    $item->order->user->name ?? 'Guest/Deleted',
                    $item->order->user->email ?? '-',
                    $item->product_name,
                    $item->sku,
                    $item->unit_price,
                    $item->quantity,
                    $item->subtotal,
                    $item->order->status ?? 'UNKNOWN',
                ]);
            });
    }

    protected function streamOrdersCsv($handle, Carbon $startDate, Carbon $endDate): void
    {
        fputcsv($handle, ['Order Number', 'Date', 'Customer Name', 'Customer Email', 'Items Count', 'Subtotal (IDR)', 'Shipping Cost (IDR)', 'Discount (IDR)', 'Total Amount (IDR)', 'Status']);

        Order::with(['user', 'orderItems'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->lazy(200)
            ->each(function ($order) use ($handle) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->user->name ?? 'Deleted User',
                    $order->user->email ?? '-',
                    $order->orderItems->sum('quantity'),
                    $order->subtotal,
                    $order->shipping_cost,
                    $order->discount_amount,
                    $order->total_amount,
                    $order->status,
                ]);
            });
    }

    protected function streamInventoryCsv($handle): void
    {
        fputcsv($handle, ['SKU', 'Product Name', 'Unit Price (IDR)', 'Stock Quantity', 'Asset Value (IDR)', 'Status', 'Last Updated']);

        ProductVariant::with('product')
            ->lazy(200)
            ->each(function ($variant) use ($handle) {
                $status = 'In Stock';
                if ($variant->stock == 0) {
                    $status = 'Out of Stock';
                } elseif ($variant->stock <= 5) {
                    $status = 'Low Stock';
                }

                fputcsv($handle, [
                    $variant->sku,
                    $variant->product->name ?? 'N/A',
                    $variant->price,
                    $variant->stock,
                    $variant->stock * $variant->price,
                    $status,
                    $variant->updated_at ? $variant->updated_at->format('Y-m-d H:i:s') : '-',
                ]);
            });
    }

    protected function streamPaymentsCsv($handle, Carbon $startDate, Carbon $endDate): void
    {
        fputcsv($handle, ['Payment ID', 'Order Number', 'Provider', 'Reference Code', 'Amount (IDR)', 'Status', 'Paid At', 'Created At']);

        Payment::with('order')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->lazy(200)
            ->each(function ($payment) use ($handle) {
                fputcsv($handle, [
                    $payment->id,
                    $payment->order->order_number ?? 'N/A',
                    $payment->provider,
                    $payment->provider_reference,
                    $payment->amount,
                    $payment->status,
                    $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : '-',
                    $payment->created_at->format('Y-m-d H:i:s'),
                ]);
            });
    }

    protected function streamCustomersCsv($handle, Carbon $startDate, Carbon $endDate): void
    {
        fputcsv($handle, ['Customer ID', 'Full Name', 'Email', 'Phone', 'Orders Count', 'Total Spent (IDR)', 'Registered Date']);

        User::withCount('orders')
            ->withSum(['orders as total_spent' => function ($query) {
                $query->whereIn('status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value, OrderStatus::SHIPPED->value, OrderStatus::DELIVERED->value]);
            }], 'total_amount')
            ->lazy(200)
            ->each(function ($user) use ($handle) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone ?? '-',
                    $user->orders_count,
                    $user->total_spent ?? 0,
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            });
    }

    /**
     * Persist generated report archive into database and physical storage file.
     */
    public function saveReportArchive(string $name, string $type, Carbon $startDate, Carbon $endDate, ?int $userId = null): Report
    {
        $type = strtoupper($type);
        $data = $this->getReportData($type, $startDate, $endDate, true);

        // Generate filename
        $fileName = 'reports/' . strtolower($type) . '_' . date('Ymd_His') . '_' . Str::random(6) . '.csv';

        // Stream to in-memory temporary resource first to avoid filesystem permission issues
        $tempStream = fopen('php://temp', 'r+');
        fputs($tempStream, "\xEF\xBB\xBF"); // UTF-8 BOM

        match ($type) {
            ReportType::SALES->value => $this->streamSalesCsv($tempStream, $startDate, $endDate),
            ReportType::ORDERS->value => $this->streamOrdersCsv($tempStream, $startDate, $endDate),
            ReportType::INVENTORY->value => $this->streamInventoryCsv($tempStream),
            ReportType::PAYMENTS->value => $this->streamPaymentsCsv($tempStream, $startDate, $endDate),
            ReportType::CUSTOMERS->value => $this->streamCustomersCsv($tempStream, $startDate, $endDate),
            default => $this->streamSalesCsv($tempStream, $startDate, $endDate),
        };

        rewind($tempStream);
        Storage::disk('local')->put($fileName, $tempStream);
        if (is_resource($tempStream)) {
            fclose($tempStream);
        }

        return Report::create([
            'name' => $name,
            'type' => $type,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'generated_by' => $userId,
            'file_path' => $fileName,
            'metadata' => $data['summary_meta'] ?? [],
            'created_at' => Carbon::now(),
        ]);
    }
}
