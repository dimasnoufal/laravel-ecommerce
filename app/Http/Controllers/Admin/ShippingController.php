<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCarrier;
use App\Models\ShippingService;
use App\Models\AuditLog;
use App\Enums\AuditLogAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ShippingController extends Controller
{
    /**
     * Display a listing of shipping carriers with server-side DataTable.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ShippingCarrier::withCount([
                'services',
                'services as active_services_count' => function ($q) {
                    $q->where('is_active', true);
                }
            ]);

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('is_active', $request->status === 'active');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('carrier_info', function ($row) {
                    $initials = strtoupper(substr($row->code, 0, 3));
                    return '<div class="carrier-cell">
                                <div class="carrier-logo-box">' . $initials . '</div>
                                <div class="carrier-meta">
                                    <div class="carrier-name-row">
                                        <span class="carrier-fullname">' . e($row->name) . '</span>
                                    </div>
                                    <code class="code-pill">' . e($row->code) . '</code>
                                </div>
                            </div>';
                })
                ->addColumn('tracking_preview', function ($row) {
                    if (!$row->tracking_url_template) {
                        return '<span class="text-muted" style="font-size: 0.8125rem; font-style: italic;">Tidak ada template</span>';
                    }

                    $previewUrl = str_replace('{tracking_number}', 'TEST123456', $row->tracking_url_template);
                    return '<div class="tracking-template-cell">
                                <span class="tracking-url-text" title="' . e($row->tracking_url_template) . '">' . e($row->tracking_url_template) . '</span>
                                <a href="' . e($previewUrl) . '" target="_blank" rel="noopener noreferrer" class="tbl-btn-test-url" title="Uji Coba Tautan Lacak Resi">
                                    <i data-lucide="external-link" style="width: 12px; height: 12px;"></i>
                                    <span>Tes Link</span>
                                </a>
                            </div>';
                })
                ->addColumn('services_count_badge', function ($row) {
                    return '<button type="button" class="badge-services-btn" onclick="openServicesDrawer(' . $row->id . ', \'' . addslashes(htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8')) . '\', \'' . $row->code . '\')" title="Lihat dan kelola layanan pengiriman">
                                <i data-lucide="layers" style="width: 13px; height: 13px;"></i>
                                <span>' . $row->active_services_count . ' / ' . $row->services_count . ' Layanan</span>
                                <i data-lucide="chevron-right" style="width: 13px; height: 13px; margin-left: 2px;"></i>
                            </button>';
                })
                ->addColumn('status_pill', function ($row) {
                    $statusText = $row->is_active ? 'Aktif' : 'Nonaktif';
                    $statusClass = $row->is_active ? 'status-pill-success' : 'status-pill-danger';

                    return '<button type="button" class="status-pill-btn ' . $statusClass . '" 
                                onclick="toggleCarrierStatus(' . $row->id . ')" 
                                title="Klik untuk mengubah status aktif/nonaktif">
                                <span class="status-dot"></span>
                                <span>' . $statusText . '</span>
                            </button>';
                })
                ->addColumn('action', function ($row) {
                    $safeName = addslashes(htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'));

                    return '<div class="table-actions">
                                <button type="button" class="tbl-btn tbl-btn-primary-subtle" onclick="openServicesDrawer(' . $row->id . ', \'' . $safeName . '\', \'' . $row->code . '\')" title="Kelola Layanan">
                                    <i data-lucide="list-plus" style="width: 14px; height: 14px;"></i>
                                    <span>Layanan</span>
                                </button>
                                <button type="button" class="tbl-btn tbl-btn-edit" onclick="editCarrier(' . $row->id . ')" title="Edit Kurir">
                                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                    <span>Edit</span>
                                </button>
                                <button type="button" class="tbl-btn tbl-btn-delete" onclick="deleteCarrier(' . $row->id . ', \'' . $safeName . '\')" title="Hapus Kurir">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    <span>Hapus</span>
                                </button>
                            </div>';
                })
                ->rawColumns(['carrier_info', 'tracking_preview', 'services_count_badge', 'status_pill', 'action'])
                ->make(true);
        }

        // Metrics Summary
        $totalCarriers = ShippingCarrier::count();
        $activeCarriers = ShippingCarrier::where('is_active', true)->count();
        $totalServices = ShippingService::count();
        $activeServices = ShippingService::where('is_active', true)->count();

        return view('admin.master-data.shipping', compact(
            'totalCarriers',
            'activeCarriers',
            'totalServices',
            'activeServices'
        ));
    }

    /**
     * Get list of services belonging to a carrier (AJAX for drawer).
     */
    public function getCarrierServices(ShippingCarrier $carrier)
    {
        $services = $carrier->services()->orderBy('code')->get();

        return response()->json([
            'success' => true,
            'carrier' => [
                'id' => $carrier->id,
                'code' => $carrier->code,
                'name' => $carrier->name,
                'is_active' => $carrier->is_active,
            ],
            'services' => $services->map(function ($s) {
                $duration = $s->estimated_min_days === $s->estimated_max_days
                    ? ($s->estimated_min_days === 0 ? 'Hari yang sama (Same Day)' : "{$s->estimated_min_days} Hari Kerja")
                    : "{$s->estimated_min_days} - {$s->estimated_max_days} Hari Kerja";

                return [
                    'id' => $s->id,
                    'code' => $s->code,
                    'name' => $s->name,
                    'estimated_min_days' => $s->estimated_min_days,
                    'estimated_max_days' => $s->estimated_max_days,
                    'duration_text' => $duration,
                    'is_active' => $s->is_active,
                ];
            })
        ]);
    }

    /**
     * Store a newly created carrier in storage.
     */
    public function storeCarrier(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:shipping_carriers,code',
            'name' => 'required|string|max:100',
            'tracking_url_template' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $carrier = ShippingCarrier::create([
            'code' => strtoupper(trim($request->code)),
            'name' => trim($request->name),
            'tracking_url_template' => $request->tracking_url_template ? trim($request->tracking_url_template) : null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLog::log(
            AuditLogAction::CREATE,
            "Menambahkan kurir ekspedisi baru: {$carrier->name} ({$carrier->code})",
            $carrier,
            $carrier->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => "Kurir '{$carrier->name}' berhasil ditambahkan.",
            'data' => $carrier
        ], 201);
    }

    /**
     * Show the form for editing the specified carrier.
     */
    public function editCarrier(ShippingCarrier $carrier)
    {
        return response()->json([
            'success' => true,
            'data' => $carrier
        ]);
    }

    /**
     * Update the specified carrier in storage.
     */
    public function updateCarrier(Request $request, ShippingCarrier $carrier)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('shipping_carriers', 'code')->ignore($carrier->id)],
            'name' => 'required|string|max:100',
            'tracking_url_template' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $oldData = $carrier->toArray();

        $carrier->update([
            'code' => strtoupper(trim($request->code)),
            'name' => trim($request->name),
            'tracking_url_template' => $request->tracking_url_template ? trim($request->tracking_url_template) : null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLog::log(
            AuditLogAction::UPDATE,
            "Memperbarui data kurir ekspedisi: {$carrier->name} ({$carrier->code})",
            $carrier,
            ['old' => $oldData, 'new' => $carrier->toArray()]
        );

        return response()->json([
            'success' => true,
            'message' => "Data kurir '{$carrier->name}' berhasil diperbarui.",
            'data' => $carrier
        ]);
    }

    /**
     * Toggle active/inactive status of a carrier.
     */
    public function toggleCarrierStatus(ShippingCarrier $carrier)
    {
        $carrier->is_active = !$carrier->is_active;
        $carrier->save();

        $statusText = $carrier->is_active ? 'AKTIF' : 'NONAKTIF';
        AuditLog::log(
            AuditLogAction::UPDATE,
            "Mengubah status kurir {$carrier->name} menjadi {$statusText}",
            $carrier,
            ['is_active' => $carrier->is_active]
        );

        return response()->json([
            'success' => true,
            'message' => "Status kurir {$carrier->name} berhasil diubah menjadi {$statusText}.",
            'is_active' => $carrier->is_active,
        ]);
    }

    /**
     * Remove the specified carrier from storage.
     */
    public function destroyCarrier(ShippingCarrier $carrier)
    {
        // Dependency check: Check if services of this carrier are used in shipments
        $carrierName = $carrier->name;
        $hasShipments = DB::table('shipments')
            ->whereIn('service_id', $carrier->services()->pluck('id'))
            ->exists();

        if ($hasShipments) {
            return response()->json([
                'success' => false,
                'message' => "Kurir '{$carrierName}' tidak dapat dihapus karena sudah memiliki riwayat transaksi pengiriman barang.",
            ], 422);
        }

        DB::transaction(function () use ($carrier) {
            $carrier->services()->delete();
            $carrier->delete();
        });

        AuditLog::log(
            AuditLogAction::DELETE,
            "Menghapus kurir ekspedisi: {$carrierName}",
            $carrier
        );

        return response()->json([
            'success' => true,
            'message' => "Kurir '{$carrierName}' dan seluruh layanannya berhasil dihapus.",
        ]);
    }

    /**
     * Store a newly created shipping service for a carrier.
     */
    public function storeService(Request $request)
    {
        $request->validate([
            'carrier_id' => 'required|exists:shipping_carriers,id',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('shipping_services', 'code')->where('carrier_id', $request->carrier_id)
            ],
            'name' => 'required|string|max:100',
            'estimated_min_days' => 'required|integer|min:0',
            'estimated_max_days' => 'required|integer|gte:estimated_min_days',
            'is_active' => 'nullable|boolean',
        ]);

        $service = ShippingService::create([
            'carrier_id' => $request->carrier_id,
            'code' => strtoupper(trim($request->code)),
            'name' => trim($request->name),
            'estimated_min_days' => (int) $request->estimated_min_days,
            'estimated_max_days' => (int) $request->estimated_max_days,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $carrier = $service->carrier;

        AuditLog::log(
            AuditLogAction::CREATE,
            "Menambahkan layanan pengiriman baru '{$service->name}' ({$service->code}) pada kurir {$carrier->name}",
            $service,
            $service->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => "Layanan '{$service->name}' berhasil ditambahkan.",
            'data' => $service
        ], 201);
    }

    /**
     * Show the form for editing the specified service.
     */
    public function editService(ShippingService $service)
    {
        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    /**
     * Update the specified shipping service.
     */
    public function updateService(Request $request, ShippingService $service)
    {
        $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('shipping_services', 'code')
                    ->where('carrier_id', $service->carrier_id)
                    ->ignore($service->id)
            ],
            'name' => 'required|string|max:100',
            'estimated_min_days' => 'required|integer|min:0',
            'estimated_max_days' => 'required|integer|gte:estimated_min_days',
            'is_active' => 'nullable|boolean',
        ]);

        $oldData = $service->toArray();

        $service->update([
            'code' => strtoupper(trim($request->code)),
            'name' => trim($request->name),
            'estimated_min_days' => (int) $request->estimated_min_days,
            'estimated_max_days' => (int) $request->estimated_max_days,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLog::log(
            AuditLogAction::UPDATE,
            "Memperbarui layanan pengiriman: {$service->name} ({$service->code})",
            $service,
            ['old' => $oldData, 'new' => $service->toArray()]
        );

        return response()->json([
            'success' => true,
            'message' => "Layanan '{$service->name}' berhasil diperbarui.",
            'data' => $service
        ]);
    }

    /**
     * Toggle active/inactive status of a service.
     */
    public function toggleServiceStatus(ShippingService $service)
    {
        $service->is_active = !$service->is_active;
        $service->save();

        $statusText = $service->is_active ? 'AKTIF' : 'NONAKTIF';
        AuditLog::log(
            AuditLogAction::UPDATE,
            "Mengubah status layanan {$service->name} menjadi {$statusText}",
            $service,
            ['is_active' => $service->is_active]
        );

        return response()->json([
            'success' => true,
            'message' => "Status layanan {$service->name} berhasil diubah menjadi {$statusText}.",
            'is_active' => $service->is_active,
        ]);
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroyService(ShippingService $service)
    {
        // Dependency check in shipments
        $hasShipments = DB::table('shipments')->where('service_id', $service->id)->exists();

        if ($hasShipments) {
            return response()->json([
                'success' => false,
                'message' => "Layanan '{$service->name}' tidak dapat dihapus karena sudah tercatat dalam riwayat pengiriman pesanan.",
            ], 422);
        }

        $serviceName = $service->name;
        $service->delete();

        AuditLog::log(
            AuditLogAction::DELETE,
            "Menghapus layanan pengiriman: {$serviceName}",
            $service
        );

        return response()->json([
            'success' => true,
            'message' => "Layanan '{$serviceName}' berhasil dihapus.",
        ]);
    }
}
