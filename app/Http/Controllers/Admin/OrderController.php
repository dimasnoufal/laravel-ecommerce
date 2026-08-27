<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AuditLogAction;
use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;
use App\Enums\StockMovementType;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\ShipmentStatusHistory;
use App\Models\ShippingCarrier;
use App\Models\ShippingService;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with DataTables & KPIs.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::with(['user', 'items', 'payment', 'shipments.service.carrier'])
                ->select('orders.*');

            // Status Tab Filter
            if ($request->filled('status') && $request->status !== 'ALL') {
                $query->where('status', $request->status);
            }

            // Payment Status Filter
            if ($request->filled('payment_status')) {
                $query->whereHas('payment', function ($q) use ($request) {
                    $q->where('status', $request->payment_status);
                });
            }

            // Date Range Filter
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('order_info', function ($order) {
                    $itemCount = $order->items->sum('quantity');
                    return '
                        <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                            <a href="' . route('admin.orders.show', $order->id) . '" style="font-weight: 700; color: var(--primary); font-size: 0.875rem; text-decoration: none;" onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">
                                ' . e($order->order_number) . '
                            </a>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">' . $itemCount . ' Produk</span>
                        </div>
                    ';
                })
                ->addColumn('customer_info', function ($order) {
                    if (!$order->user) {
                        return '<span style="color: var(--text-muted); font-size: 0.8125rem;">Guest / Deleted</span>';
                    }
                    $name = e($order->user->name);
                    $email = e($order->user->email);
                    $initial = strtoupper(substr($name, 0, 1));
                    return '
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; flex-shrink: 0;">
                                ' . $initial . '
                            </div>
                            <div style="display: flex; flex-direction: column; line-height: 1.25;">
                                <span style="font-weight: 600; font-size: 0.8125rem; color: var(--text-main);">' . $name . '</span>
                                <span style="font-size: 0.72rem; color: var(--text-muted);">' . $email . '</span>
                            </div>
                        </div>
                    ';
                })
                ->addColumn('placed_at_formatted', function ($order) {
                    $dt = $order->placed_at ?? $order->created_at;
                    return '
                        <div style="display: flex; flex-direction: column; gap: 0.15rem;">
                            <span style="font-weight: 600; font-size: 0.8125rem; color: var(--text-main);">' . $dt->format('d M Y') . '</span>
                            <span style="font-size: 0.72rem; color: var(--text-muted);">' . $dt->format('H:i') . ' WIB</span>
                        </div>
                    ';
                })
                ->addColumn('payment_status_badge', function ($order) {
                    $pay = $order->payment;
                    if (!$pay) {
                        return '<span class="status-pill status-pending" style="font-size: 0.72rem;">UNPAID</span>';
                    }
                    $status = $pay->status;
                    $class = match($status) {
                        'PAID', 'SETTLEMENT', 'CAPTURE' => 'status-paid',
                        'PENDING' => 'status-pending',
                        default => 'status-cancelled',
                    };
                    $provider = strtoupper($pay->provider ?? 'GATEWAY');
                    return '
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.2rem;">
                            <span class="status-pill ' . $class . '" style="font-size: 0.72rem;">' . e($status) . '</span>
                            <span style="font-size: 0.68rem; color: var(--text-muted); font-weight: 600;">' . e($provider) . '</span>
                        </div>
                    ';
                })
                ->addColumn('order_status_badge', function ($order) {
                    $status = $order->status instanceof OrderStatus ? $order->status->value : (string) $order->status;
                    $class = match($status) {
                        'DELIVERED' => 'status-paid',
                        'SHIPPED' => 'status-paid',
                        'PROCESSING', 'CONFIRMED' => 'status-processing',
                        'PENDING' => 'status-pending',
                        'CANCELLED' => 'status-cancelled',
                        default => 'status-pending',
                    };
                    return '<span class="status-pill ' . $class . '" style="font-weight: 700; font-size: 0.75rem;">' . e($status) . '</span>';
                })
                ->addColumn('total_amount_formatted', function ($order) {
                    return '
                        <div style="display: flex; flex-direction: column; text-align: right; gap: 0.15rem;">
                            <strong style="font-size: 0.875rem; color: var(--text-main);">Rp ' . number_format($order->total_amount, 0, ',', '.') . '</strong>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Ongkir: Rp ' . number_format($order->shipping_cost, 0, ',', '.') . '</span>
                        </div>
                    ';
                })
                ->addColumn('action', function ($order) {
                    $currentStatus = $order->status instanceof OrderStatus ? $order->status->value : (string) $order->status;
                    return '
                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.4rem;">
                            <a href="' . route('admin.orders.show', $order->id) . '" class="btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.3rem;" title="Lihat Detail Pesanan">
                                <i data-lucide="eye" style="width: 13px; height: 13px;"></i>
                                <span>Detail</span>
                            </a>
                            <button type="button" class="btn-secondary btn-update-status" data-order-id="' . $order->id . '" data-order-number="' . e($order->order_number) . '" data-current-status="' . $currentStatus . '" style="padding: 0.35rem 0.65rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.3rem;" title="Ubah Status">
                                <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i>
                                <span>Status</span>
                            </button>
                            <a href="' . route('admin.orders.invoice', $order->id) . '" target="_blank" class="btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.3rem;" title="Cetak Faktur / Invoice">
                                <i data-lucide="printer" style="width: 13px; height: 13px;"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['order_info', 'customer_info', 'placed_at_formatted', 'payment_status_badge', 'order_status_badge', 'total_amount_formatted', 'action'])
                ->make(true);
        }

        // Summary Metric KPIs
        $kpis = [
            'total_orders' => Order::count(),
            'pending_count' => Order::where('status', OrderStatus::PENDING->value)->count(),
            'processing_count' => Order::whereIn('status', [OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value])->count(),
            'shipped_count' => Order::where('status', OrderStatus::SHIPPED->value)->count(),
            'delivered_count' => Order::where('status', OrderStatus::DELIVERED->value)->count(),
            'cancelled_count' => Order::where('status', OrderStatus::CANCELLED->value)->count(),
            'total_revenue' => Order::where('status', '!=', OrderStatus::CANCELLED->value)->sum('total_amount'),
        ];

        $carriers = ShippingCarrier::with('services')->where('is_active', true)->get();

        return view('admin.orders.index', compact('kpis', 'carriers'));
    }

    /**
     * Display the detailed order view.
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'items.productVariant.product.images',
            'address',
            'statusHistories' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
            'payment.histories' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
            'shipments.service.carrier',
            'shipments.statusHistories' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }
        ]);

        $carriers = ShippingCarrier::with('services')->where('is_active', true)->get();

        return view('admin.orders.show', compact('order', 'carriers'));
    }

    /**
     * Update order status with business automation, resi input, and stock return.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validStatuses = array_column(OrderStatus::cases(), 'value');
        $request->validate([
            'status' => 'required|string|in:' . implode(',', $validStatuses),
            'note' => 'nullable|string|max:500',
            'tracking_number' => 'nullable|string|max:100',
            'shipping_service_id' => 'nullable|exists:shipping_services,id',
        ]);

        $oldStatus = $order->status instanceof OrderStatus ? $order->status->value : (string) $order->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan tidak berubah.',
            ]);
        }

        DB::transaction(function () use ($order, $oldStatus, $newStatus, $request) {
            // 1. Update Order Status
            $order->update([
                'status' => $newStatus,
            ]);

            // 2. Add Order Status History
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'note' => $request->note ?: "Status diubah dari {$oldStatus} ke {$newStatus} oleh Admin.",
                'created_at' => now(),
            ]);

            // 3. Handle SHIPPED Transition (Create or Update Shipment & Tracking Number)
            if ($newStatus === OrderStatus::SHIPPED->value) {
                $shipment = $order->shipments()->first();

                if (!$shipment) {
                    $serviceId = $request->shipping_service_id ?: ShippingService::first()?->id;
                    $shipment = Shipment::create([
                        'order_id' => $order->id,
                        'service_id' => $serviceId,
                        'shipment_number' => 'SHP-' . strtoupper(uniqid()),
                        'status' => ShipmentStatus::SHIPPED->value,
                        'tracking_number' => $request->tracking_number,
                        'shipped_at' => now(),
                    ]);
                } else {
                    $shipment->update([
                        'status' => ShipmentStatus::SHIPPED->value,
                        'tracking_number' => $request->tracking_number ?: $shipment->tracking_number,
                        'shipped_at' => now(),
                    ]);
                }

                // Add Shipment Status History
                ShipmentStatusHistory::create([
                    'shipment_id' => $shipment->id,
                    'status' => ShipmentStatus::SHIPPED->value,
                    'note' => 'Paket telah dikirimkan melalui kurir' . ($request->tracking_number ? ' dengan No. Resi: ' . $request->tracking_number : '') . '.',
                    'created_at' => now(),
                ]);
            }

            // 4. Handle DELIVERED Transition
            if ($newStatus === OrderStatus::DELIVERED->value) {
                $shipment = $order->shipments()->first();
                if ($shipment) {
                    $shipment->update([
                        'status' => ShipmentStatus::DELIVERED->value,
                        'delivered_at' => now(),
                    ]);

                    ShipmentStatusHistory::create([
                        'shipment_id' => $shipment->id,
                        'status' => ShipmentStatus::DELIVERED->value,
                        'note' => 'Paket pesanan telah berhasil diterima oleh penerima.',
                        'created_at' => now(),
                    ]);
                }
            }

            // 5. Handle CANCELLED Transition (Restore Stock)
            if ($newStatus === OrderStatus::CANCELLED->value && $oldStatus !== OrderStatus::CANCELLED->value) {
                foreach ($order->items as $item) {
                    if ($item->productVariant) {
                        $item->productVariant->increment('stock', $item->quantity);

                        StockMovement::create([
                            'product_variant_id' => $item->product_variant_id,
                            'type' => StockMovementType::IN->value,
                            'quantity' => $item->quantity,
                            'reference_type' => Order::class,
                            'reference_id' => $order->id,
                            'note' => "Pengembalian stok dari pembatalan Pesanan #{$order->order_number}",
                            'created_at' => now(),
                        ]);
                    }
                }
            }

            // 6. Record System Audit Log
            AuditLog::log(
                $newStatus === OrderStatus::CANCELLED->value ? AuditLogAction::ORDER_CANCELLED : AuditLogAction::UPDATE,
                "Memperbarui status Pesanan #{$order->order_number} dari {$oldStatus} ke {$newStatus}",
                $order,
                [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'note' => $request->note,
                    'tracking_number' => $request->tracking_number,
                ]
            );
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Status pesanan #{$order->order_number} berhasil diperbarui ke {$newStatus}.",
            ]);
        }

        return back()->with('success', "Status pesanan #{$order->order_number} berhasil diperbarui ke {$newStatus}.");
    }

    /**
     * Display printable official invoice / packing slip.
     */
    public function invoice(Order $order)
    {
        $order->load([
            'user',
            'items.productVariant.product',
            'address',
            'payment',
            'shipments.service.carrier',
        ]);

        return view('admin.orders.invoice', compact('order'));
    }
}
