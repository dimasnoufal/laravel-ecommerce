<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\AuditLog;
use App\Enums\StockMovementType;
use App\Enums\AuditLogAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class InventoryController extends Controller
{
    /**
     * Display inventory dashboard & datatables.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if ($request->get('tab') === 'movements') {
                return $this->getMovementsDataTable($request);
            }
            return $this->getStocksDataTable($request);
        }

        // KPI Summary Cards
        $totalStock = ProductVariant::sum('stock');
        $totalSkus = ProductVariant::count();
        $lowStockCount = ProductVariant::where('stock', '>', 0)->where('stock', '<=', 5)->count();
        $outOfStockCount = ProductVariant::where('stock', 0)->count();
        $recentMovementsCount = StockMovement::where('created_at', '>=', now()->subDays(7))->count();

        // All variants list for select dropdown in Adjustment Drawer
        $variants = ProductVariant::with(['product', 'attributeValues.attribute'])
            ->orderBy('sku', 'asc')
            ->get();

        return view('admin.inventory.index', compact(
            'totalStock',
            'totalSkus',
            'lowStockCount',
            'outOfStockCount',
            'recentMovementsCount',
            'variants'
        ));
    }

    /**
     * Server-side DataTable for Product Stocks (Tab 1).
     */
    private function getStocksDataTable(Request $request)
    {
        $query = ProductVariant::with(['product.category', 'attributeValues.attribute'])
            ->select('product_variants.*');

        // Optional filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out_of_stock') {
                $query->where('stock', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('stock', '>', 0)->where('stock', '<=', 5);
            } elseif ($request->stock_status === 'in_stock') {
                $query->where('stock', '>', 5);
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('product_info', function ($row) {
                $productName = e($row->product->name ?? 'Produk Dihapus');
                $categoryName = e($row->product->category->name ?? '-');
                $sku = e($row->sku);

                return '
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <span style="font-weight: 700; color: var(--text-main); font-size: 0.9375rem;">' . $productName . '</span>
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem;">
                            <code style="background: var(--bg-body); border: 1px solid var(--border-color); padding: 0.15rem 0.4rem; border-radius: 4px; color: var(--primary); font-weight: 600;">' . $sku . '</code>
                            <span style="color: var(--text-muted);">&bull; ' . $categoryName . '</span>
                        </div>
                    </div>
                ';
            })
            ->addColumn('variant_attributes', function ($row) {
                if ($row->attributeValues->isEmpty()) {
                    return '<span class="status-badge" style="background: var(--bg-body); color: var(--text-muted); border: 1px solid var(--border-color); font-size: 0.75rem;">Single SKU (Tanpa Varian)</span>';
                }

                $html = '<div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">';
                foreach ($row->attributeValues as $val) {
                    $attrName = e($val->attribute->name ?? 'Opsi');
                    $attrVal = e($val->value);
                    $html .= '<span style="background: var(--bg-body); border: 1px solid var(--border-color); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; color: var(--text-main); font-weight: 500;"><strong>' . $attrName . ':</strong> ' . $attrVal . '</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('price_formatted', function ($row) {
                return '<span style="font-weight: 600; color: var(--text-main);">Rp ' . number_format($row->price, 0, ',', '.') . '</span>';
            })
            ->addColumn('stock_display', function ($row) {
                return '<div style="font-size: 1.05rem; font-weight: 800; color: var(--text-main);">' . number_format($row->stock) . ' <span style="font-size: 0.75rem; font-weight: 500; color: var(--text-muted);">unit</span></div>';
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->stock == 0) {
                    return '<span class="status-badge badge-danger" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; font-weight: 600;"><i data-lucide="alert-triangle" style="width: 12px; height: 12px; margin-right: 4px;"></i> Habis (0)</span>';
                } elseif ($row->stock <= 5) {
                    return '<span class="status-badge badge-warning" style="background: #FEF3C7; color: #D97706; border: 1px solid #FDE68A; font-weight: 600;"><i data-lucide="alert-circle" style="width: 12px; height: 12px; margin-right: 4px;"></i> Menipis (≤5)</span>';
                } else {
                    return '<span class="status-badge badge-success" style="background: #DCFCE7; color: #16A34A; border: 1px solid #BBF7D0; font-weight: 600;"><i data-lucide="check-circle-2" style="width: 12px; height: 12px; margin-right: 4px;"></i> In Stock</span>';
                }
            })
            ->addColumn('action', function ($row) {
                $sku = e($row->sku);
                $productName = e($row->product->name ?? 'Produk');
                return '
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                        <button type="button" class="btn-primary" style="padding: 0.4rem 0.75rem; font-size: 0.8125rem; display: inline-flex; align-items: center; gap: 0.35rem;" onclick="openAdjustmentDrawer(' . $row->id . ', \'' . addslashes($sku) . '\', ' . $row->stock . ')">
                            <i data-lucide="sliders-horizontal" style="width: 14px; height: 14px;"></i>
                            <span>Adjust Stok</span>
                        </button>
                        <button type="button" class="btn-secondary" style="padding: 0.4rem 0.65rem; font-size: 0.8125rem; display: inline-flex; align-items: center; gap: 0.35rem;" onclick="filterMovementsBySku(' . $row->id . ', \'' . addslashes($sku) . '\')" title="Lihat Mutasi SKU Ini">
                            <i data-lucide="history" style="width: 14px; height: 14px;"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['product_info', 'variant_attributes', 'price_formatted', 'stock_display', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Server-side DataTable for Stock Movements History (Tab 2).
     */
    private function getMovementsDataTable(Request $request)
    {
        $query = StockMovement::with(['productVariant.product', 'productVariant.attributeValues.attribute'])
            ->select('stock_movements.*');

        // Filter by Type
        if ($request->filled('movement_type')) {
            $query->where('type', $request->movement_type);
        }

        // Filter by specific SKU / Variant
        if ($request->filled('variant_id')) {
            $query->where('product_variant_id', $request->variant_id);
        }

        // Filter by Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('created_at_formatted', function ($row) {
                return '
                    <div style="display: flex; flex-direction: column; gap: 0.15rem;">
                        <span style="font-weight: 600; color: var(--text-main); font-size: 0.8125rem;">' . $row->created_at->format('d M Y') . '</span>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">' . $row->created_at->format('H:i:s') . ' WIB</span>
                    </div>
                ';
            })
            ->addColumn('sku_info', function ($row) {
                $variant = $row->productVariant;
                if (!$variant) {
                    return '<span style="color: var(--text-muted); font-size: 0.8125rem;">Varian Dihapus</span>';
                }

                $sku = e($variant->sku);
                $productName = e($variant->product->name ?? 'Produk');

                $attrList = [];
                foreach ($variant->attributeValues as $val) {
                    $attrList[] = e($val->value);
                }
                $attrText = !empty($attrList) ? ' (' . implode(', ', $attrList) . ')' : '';

                return '
                    <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                        <span style="font-weight: 700; color: var(--text-main); font-size: 0.875rem;">' . $productName . '</span>
                        <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.75rem;">
                            <code style="background: var(--bg-body); border: 1px solid var(--border-color); padding: 0.1rem 0.35rem; border-radius: 4px; color: var(--primary); font-weight: 600;">' . $sku . '</code>
                            <span style="color: var(--text-muted);">' . $attrText . '</span>
                        </div>
                    </div>
                ';
            })
            ->addColumn('type_badge', function ($row) {
                if ($row->type === 'IN') {
                    return '<span class="status-badge" style="background: #DCFCE7; color: #16A34A; border: 1px solid #BBF7D0; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;"><i data-lucide="arrow-down-left" style="width: 13px; height: 13px;"></i> MASUK (IN)</span>';
                } elseif ($row->type === 'OUT') {
                    return '<span class="status-badge" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;"><i data-lucide="arrow-up-right" style="width: 13px; height: 13px;"></i> KELUAR (OUT)</span>';
                } else {
                    return '<span class="status-badge" style="background: #E0E7FF; color: #4338CA; border: 1px solid #C7D2FE; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;"><i data-lucide="refresh-cw" style="width: 13px; height: 13px;"></i> OPNAME (ADJUST)</span>';
                }
            })
            ->addColumn('quantity_formatted', function ($row) {
                $qty = $row->quantity;
                if ($qty > 0) {
                    return '<span style="font-weight: 800; font-size: 0.9375rem; color: #16A34A;">+' . number_format($qty) . ' <span style="font-size: 0.75rem; font-weight: 500;">unit</span></span>';
                } else {
                    return '<span style="font-weight: 800; font-size: 0.9375rem; color: #DC2626;">' . number_format($qty) . ' <span style="font-size: 0.75rem; font-weight: 500;">unit</span></span>';
                }
            })
            ->addColumn('note_display', function ($row) {
                $note = e($row->note ?? '-');
                $ref = '';
                if ($row->reference_type) {
                    $refName = class_basename($row->reference_type);
                    $ref = '<div style="font-size: 0.75rem; color: var(--primary); font-weight: 600; margin-top: 0.2rem;">Ref: ' . $refName . ' #' . $row->reference_id . '</div>';
                }
                return '<div style="font-size: 0.8125rem; color: var(--text-main); line-height: 1.4;">' . $note . $ref . '</div>';
            })
            ->rawColumns(['created_at_formatted', 'sku_info', 'type_badge', 'quantity_formatted', 'note_display'])
            ->make(true);
    }

    /**
     * Get variant details for auto-filling drawer.
     */
    public function getVariant(ProductVariant $variant)
    {
        $variant->load(['product', 'attributeValues.attribute']);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'product_name' => $variant->product->name ?? '',
                'stock' => $variant->stock,
                'price' => $variant->price,
            ]
        ]);
    }

    /**
     * Process Manual Stock Adjustment (IN, OUT, ADJUSTMENT).
     */
    public function adjustStock(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'adjustment_type' => 'required|in:IN,OUT,ADJUSTMENT',
            'quantity' => 'nullable|integer|min:1',
            'target_stock' => 'nullable|integer|min:0',
            'note' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $variant = ProductVariant::lockForUpdate()->findOrFail($request->product_variant_id);
            $currentStock = (int)$variant->stock;
            $delta = 0;
            $type = StockMovementType::from($request->adjustment_type);

            if ($request->adjustment_type === 'IN') {
                if (!$request->filled('quantity') || (int)$request->quantity <= 0) {
                    return response()->json(['success' => false, 'message' => 'Jumlah unit penambahan wajib diisi minimal 1.'], 422);
                }
                $delta = (int)$request->quantity;
                $newStock = $currentStock + $delta;
            } elseif ($request->adjustment_type === 'OUT') {
                if (!$request->filled('quantity') || (int)$request->quantity <= 0) {
                    return response()->json(['success' => false, 'message' => 'Jumlah unit pengurangan wajib diisi minimal 1.'], 422);
                }
                $delta = -(int)$request->quantity;
                if ($currentStock + $delta < 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak mencukupi untuk pengurangan {$request->quantity} unit. Stok saat ini: {$currentStock} unit."
                    ], 422);
                }
                $newStock = $currentStock + $delta;
            } else { // ADJUSTMENT
                if ($request->target_stock === null || $request->target_stock === '') {
                    return response()->json(['success' => false, 'message' => 'Stok fisik hasil opname wajib diisi.'], 422);
                }
                $targetStock = (int)$request->target_stock;
                $delta = $targetStock - $currentStock;
                if ($delta === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok fisik yang diinput sama persis dengan stok sistem saat ini ({$currentStock} unit). Tidak ada mutasi yang perlu dicatat."
                    ], 422);
                }
                $newStock = $targetStock;
            }

            // Update Variant Stock
            $variant->stock = $newStock;
            $variant->save();

            // Record Movement Ledger
            $movement = StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => $type->value,
                'quantity' => $delta,
                'note' => $request->note,
                'created_at' => now(),
            ]);

            // Record System Activity Log
            AuditLog::log(
                AuditLogAction::STOCK_ADJUSTMENT,
                "Penyesuaian stok manual untuk SKU {$variant->sku}: {$currentStock} -> {$newStock} unit ({$type->value})",
                $movement,
                [
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'sku' => $variant->sku,
                    'old_stock' => $currentStock,
                    'new_stock' => $newStock,
                    'delta' => $delta,
                    'adjustment_type' => $type->value,
                    'note' => $request->note,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Stok SKU {$variant->sku} berhasil disesuaikan menjadi {$newStock} unit.",
                'new_stock' => $newStock,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses penyesuaian stok: ' . $e->getMessage()
            ], 500);
        }
    }
}
