@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('styles')
<style>
    .dashboard-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1.25rem;
    }

    .header-title-wrap h1 {
        font-size: 1.625rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.03em;
        margin-bottom: 0.25rem;
    }

    .header-title-wrap p {
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .date-filter-pill {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-main);
        box-shadow: var(--shadow-sm);
    }

    .btn-export {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.15rem;
        background: var(--primary);
        color: #ffffff;
        border: none;
        border-radius: var(--radius-md);
        font-size: 0.8125rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }
    .btn-export:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
    }

    /* KPI Cards Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    /* Main Chart and Side Breakdown Layout */
    .chart-section-grid {
        display: grid;
        grid-template-columns: 2.3fr 1fr;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .chart-container {
        position: relative;
        height: 280px;
        width: 100%;
    }

    /* Category List Items */
    .category-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .category-item {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    .category-info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.84375rem;
    }

    .category-name {
        font-weight: 600;
        color: var(--text-main);
    }

    .category-amount {
        font-weight: 700;
        color: var(--text-muted);
    }

    .progress-bar-bg {
        width: 100%;
        height: 7px;
        background: var(--bg-body);
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .category-empty-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 250px;
        height: 100%;
        padding: 1.5rem 1rem;
    }

    /* Transactions Table */
    .table-responsive {
        overflow-x: auto;
        margin: -0.5rem -1.5rem -1.5rem -1.5rem;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.875rem;
    }

    .custom-table th {
        background: var(--bg-body);
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.6875rem;
        letter-spacing: 0.05em;
        padding: 0.95rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .custom-table td {
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-main);
        vertical-align: middle;
    }

    .custom-table tbody tr:hover {
        background: var(--bg-body);
    }

    .customer-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .customer-avatar-sm {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #E2E8F0;
        color: #475569;
        font-weight: 700;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .customer-name {
        font-weight: 600;
        color: var(--text-main);
    }

    .customer-email {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .order-num-link {
        font-weight: 700;
        color: var(--primary);
        text-decoration: none;
    }
    .order-num-link:hover {
        text-decoration: underline;
    }

    .action-btn {
        padding: 0.4rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        color: var(--text-main);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.2s ease;
    }
    .action-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
    }

    /* Empty State Styling */
    .empty-state-box {
        padding: 3.5rem 1.5rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .empty-icon-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.15rem;
    }

    .empty-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 0.35rem;
    }

    .empty-desc {
        font-size: 0.875rem;
        color: var(--text-muted);
        max-width: 420px;
        margin-bottom: 1.5rem;
        line-height: 1.5;
    }

    @media (max-width: 1200px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .chart-section-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .kpi-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')

    {{-- Top Header --}}
    <div class="dashboard-header">
        <div class="header-title-wrap">
            <h1>Markets & Store Overview</h1>
            <p>Track and analyze your store's sales metrics, order performance, and conversion rates.</p>
        </div>

        <div class="header-actions">
            <div class="date-filter-pill">
                <i data-lucide="calendar" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
                <span>{{ $metrics['date_range_label'] }}</span>
            </div>

            <button type="button" class="btn-export" onclick="showToast('info', 'Export Started', 'Generating monthly Excel report of orders and revenue.');">
                <i data-lucide="download" style="width: 16px; height: 16px;"></i>
                <span>Export Report</span>
            </button>
        </div>
    </div>

    {{-- 4 Reusable KPI Metric Cards --}}
    <div class="kpi-grid">
        <!-- Card 1: Net Revenue -->
        <x-kpi-card 
            label="NET REVENUE" 
            value="Rp {{ number_format($metrics['all_time_revenue'], 0, ',', '.') }}" 
            :change="($metrics['revenue_growth'] >= 0 ? '+' : '') . $metrics['revenue_growth'] . '%'"
            :isUp="$metrics['revenue_growth'] >= 0"
            :subtext="'7 hari: Rp ' . number_format($metrics['current_revenue'], 0, ',', '.')"
        />

        <!-- Card 2: Total Orders -->
        <x-kpi-card 
            label="TOTAL ORDERS" 
            value="{{ number_format($metrics['total_orders']) }}" 
            :change="($metrics['orders_growth'] >= 0 ? '+' : '') . $metrics['orders_growth'] . '%'"
            :isUp="$metrics['orders_growth'] >= 0"
            :subtext="$metrics['pending_orders'] . ' pesanan menunggu proses'"
        />

        <!-- Card 3: Total Products & Low Stock Alert -->
        <x-kpi-card 
            label="TOTAL PRODUK" 
            value="{{ number_format($metrics['total_products']) }}" 
            :change="$metrics['low_stock_count'] > 0 ? $metrics['low_stock_count'] . ' Alert' : 'Aman'"
            :isUp="$metrics['low_stock_count'] === 0"
            :subtext="$metrics['low_stock_count'] > 0 ? $metrics['low_stock_count'] . ' varian stok menipis (<= 5)' : 'Semua stok dalam batas aman'"
        />

        <!-- Card 4: Total Customers -->
        <x-kpi-card 
            label="TOTAL PELANGGAN" 
            value="{{ number_format($metrics['total_customers']) }}" 
            change="Aktif" 
            :isUp="true"
            subtext="Pelanggan terdaftar di sistem"
        />
    </div>

    {{-- Charts and Category Breakdown --}}
    <div class="chart-section-grid">
        <!-- Sales Chart Panel -->
        <x-panel title="Revenue & Order Trends" subtitle="Daily financial breakdown over the last 7 days">
            <x-slot name="action">
                <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.75rem; font-weight: 600;">
                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #2563EB;"></span>
                        <span style="color: var(--text-muted);">Revenue (Rp)</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #10B981;"></span>
                        <span style="color: var(--text-muted);">Orders</span>
                    </div>
                </div>
            </x-slot>

            <div class="chart-container">
                <canvas id="revenueOrdersChart"></canvas>
            </div>
        </x-panel>

        <!-- Top Categories Panel -->
        <x-panel title="Top Sales Categories" subtitle="By product catalog volume">
            @if(count($topCategories) > 0)
                <div class="category-list">
                    @foreach($topCategories as $cat)
                        <div class="category-item">
                            <div class="category-info-row">
                                <span class="category-name">{{ $cat['name'] }}</span>
                                <span class="category-amount">{{ $cat['products_count'] }} produk ({{ $cat['percentage'] }}%)</span>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: {{ $cat['percentage'] }}%; background: {{ $cat['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="category-empty-center">
                    <div class="empty-icon-circle" style="width: 48px; height: 48px; margin-bottom: 0.75rem;">
                        <i data-lucide="folder-open" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div style="font-weight: 700; color: var(--text-main); font-size: 0.9375rem; margin-bottom: 0.25rem;">Belum Ada Kategori</div>
                    <div style="color: var(--text-muted); font-size: 0.8125rem; max-width: 220px; line-height: 1.4;">Kategori produk akan otomatis teragregasi di sini.</div>
                </div>
            @endif
        </x-panel>
    </div>

    {{-- Recent Orders Table Panel --}}
    <x-panel title="Recent Transactions & Orders" subtitle="Latest customer orders processed in the system">
        <x-slot name="action">
            <a href="#" class="order-num-link" style="font-size: 0.8125rem;" onclick="showToast('info', 'Orders View', 'Loading full orders catalog...'); return false;">
                Total ({{ number_format($metrics['total_orders']) }}) &rarr;
            </a>
        </x-slot>

        @if($recentOrders->isNotEmpty())
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="#" class="order-num-link" onclick="openOrderModal('{{ $order->order_number }}', '{{ $order->user->name ?? 'Guest Customer' }}', '{{ $order->orderItems->count() }} item(s)', 'Rp {{ number_format($order->total_amount, 0, ',', '.') }}', '{{ $order->status }}'); return false;">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td>
                                    <div class="customer-cell">
                                        <div class="customer-avatar-sm">
                                            {{ substr($order->user->name ?? 'C', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="customer-name">{{ $order->user->name ?? 'Customer' }}</div>
                                            <div class="customer-email">{{ $order->user->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: var(--text-main);">
                                        {{ $order->orderItems->count() }} Item(s)
                                    </span>
                                </td>
                                <td>
                                    <span style="color: var(--text-muted); font-size: 0.8125rem;">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: var(--text-main); font-weight: 700;">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td>
                                    <x-status-pill :status="$order->status" />
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" class="action-btn" onclick="openOrderModal('{{ $order->order_number }}', '{{ $order->user->name ?? 'Guest' }}', '{{ $order->orderItems->count() }} items', 'Rp {{ number_format($order->total_amount, 0, ',', '.') }}', '{{ $order->status }}')">
                                        <i data-lucide="eye" style="width: 13px; height: 13px;"></i>
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            {{-- Empty State when 0 Orders in Database --}}
            <div class="empty-state-box">
                <div class="empty-icon-circle">
                    <i data-lucide="shopping-cart" style="width: 28px; height: 28px;"></i>
                </div>
                <div class="empty-title">Belum Ada Transaksi Pesanan</div>
                <div class="empty-desc">
                    Saat pelanggan mulai membuat pesanan di toko Anda, daftar transaksi terbaru dan rincian pembayarannya akan otomatis muncul di sini.
                </div>
                <button type="button" class="btn-export" onclick="showToast('info', 'Buat Pesanan', 'Formulir pembuatan pesanan manual siap diintegrasikan.');">
                    <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                    <span>Buat Pesanan Baru</span>
                </button>
            </div>
        @endif
    </x-panel>

    {{-- Order Detail Modal --}}
    <x-modal id="orderDetailModal" title="Detail Transaksi Pesanan" maxWidth="540px">
        <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.875rem;">
            <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                <span style="color: var(--text-muted);">Nomor Pesanan:</span>
                <strong id="modalOrderNumber" style="color: var(--primary); font-family: monospace;">-</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                <span style="color: var(--text-muted);">Nama Pelanggan:</span>
                <strong id="modalCustomerName" style="color: var(--text-main);">-</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                <span style="color: var(--text-muted);">Jumlah Item:</span>
                <span id="modalItemCount" style="color: var(--text-main); font-weight: 600;">-</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                <span style="color: var(--text-muted);">Status Pesanan:</span>
                <span id="modalOrderStatus" style="font-weight: 700;">-</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding-top: 0.5rem;">
                <span style="font-size: 1rem; font-weight: 700; color: var(--text-main);">Total Pembayaran:</span>
                <strong id="modalTotalAmount" style="font-size: 1.1rem; color: var(--primary); font-weight: 800;">-</strong>
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" class="action-btn" onclick="closeModal('orderDetailModal')">Tutup</button>
            <button type="button" class="btn-export" style="padding: 0.45rem 0.9rem;" onclick="showToast('info', 'Print Invoice', 'Mencetak invoice pesanan...'); closeModal('orderDetailModal');">
                <i data-lucide="printer" style="width: 14px; height: 14px;"></i>
                <span>Cetak Invoice</span>
            </button>
        </x-slot>
    </x-modal>

@endsection

@section('scripts')
<script>
    function openOrderModal(orderNumber, customerName, itemCount, totalAmount, status) {
        document.getElementById('modalOrderNumber').textContent = '#' + orderNumber;
        document.getElementById('modalCustomerName').textContent = customerName;
        document.getElementById('modalItemCount').textContent = itemCount;
        document.getElementById('modalTotalAmount').textContent = totalAmount;
        document.getElementById('modalOrderStatus').textContent = status;
        openModal('orderDetailModal');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueOrdersChart').getContext('2d');
        const chartBackendData = @json($chartData);

        const labels = chartBackendData.labels || ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        const revenueData = chartBackendData.revenue || [0, 0, 0, 0, 0, 0, 0];
        const ordersData = chartBackendData.orders || [0, 0, 0, 0, 0, 0, 0];

        // Gradient for Revenue Area
        const gradientRevenue = ctx.createLinearGradient(0, 0, 0, 280);
        gradientRevenue.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        gradientRevenue.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue (Rp)',
                        data: revenueData,
                        borderColor: '#2563EB',
                        backgroundColor: gradientRevenue,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#2563EB',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Orders',
                        data: ordersData,
                        borderColor: '#10B981',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.4,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: false,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        titleColor: '#F8FAFC',
                        bodyColor: '#94A3B8',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.label.includes('Revenue')) {
                                    return ' Revenue: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                                return ' Orders: ' + context.raw + ' items';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            color: '#94A3B8',
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        min: 0,
                        suggestedMax: 1000000,
                        grid: {
                            color: 'rgba(226, 232, 240, 0.6)'
                        },
                        ticks: {
                            color: '#94A3B8',
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        min: 0,
                        suggestedMax: 10,
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            color: '#10B981',
                            font: {
                                size: 11
                            },
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
