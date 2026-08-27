<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Display activity log dashboard.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = AuditLog::with('user')->select('audit_logs.*');

            if ($request->filled('action_filter')) {
                $query->where('action', $request->action_filter);
            }

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

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
                ->addColumn('user_info', function ($row) {
                    if (!$row->user) {
                        return '
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--bg-body); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: var(--text-muted);">
                                    <i data-lucide="bot" style="width: 14px; height: 14px;"></i>
                                </div>
                                <span style="font-weight: 500; font-size: 0.8125rem; color: var(--text-muted);">Sistem / Guest</span>
                            </div>
                        ';
                    }

                    $name = e($row->user->name);
                    $email = e($row->user->email);
                    $initial = strtoupper(substr($name, 0, 1));

                    return '
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                                ' . $initial . '
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 600; font-size: 0.8125rem; color: var(--text-main);">' . $name . '</span>
                                <span style="font-size: 0.7rem; color: var(--text-muted);">' . $email . '</span>
                            </div>
                        </div>
                    ';
                })
                ->addColumn('action_badge', function ($row) {
                    $act = $row->action;
                    $style = 'background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1;';
                    $icon = 'activity';

                    if (in_array($act, ['CREATE', 'LOGIN'])) {
                        $style = 'background: #DCFCE7; color: #16A34A; border: 1px solid #BBF7D0;';
                        $icon = 'plus-circle';
                    } elseif (in_array($act, ['UPDATE', 'STOCK_ADJUSTMENT'])) {
                        $style = 'background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE;';
                        $icon = 'edit-3';
                    } elseif (in_array($act, ['DELETE', 'LOGOUT', 'ORDER_CANCELLED'])) {
                        $style = 'background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA;';
                        $icon = 'trash-2';
                    }

                    return '<span class="status-badge" style="' . $style . ' font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.75rem;"><i data-lucide="' . $icon . '" style="width: 12px; height: 12px;"></i> ' . e($act) . '</span>';
                })
                ->addColumn('description_display', function ($row) {
                    return '<div style="font-size: 0.8125rem; color: var(--text-main); line-height: 1.4;">' . e($row->description) . '</div>';
                })
                ->addColumn('ip_address_display', function ($row) {
                    $ip = e($row->ip_address ?? '-');
                    return '<code style="background: var(--bg-body); border: 1px solid var(--border-color); padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.75rem; color: var(--text-muted);">' . $ip . '</code>';
                })
                ->addColumn('payload_action', function ($row) {
                    if (empty($row->metadata)) {
                        return '<span style="color: var(--text-muted); font-size: 0.75rem;">-</span>';
                    }
                    $payloadJson = htmlspecialchars(json_encode($row->metadata), ENT_QUOTES, 'UTF-8');
                    $action = htmlspecialchars($row->action, ENT_QUOTES, 'UTF-8');
                    $description = htmlspecialchars($row->description ?? '', ENT_QUOTES, 'UTF-8');

                    return '<button type="button" class="btn-secondary btn-view-diff" ' .
                        'data-log-id="' . $row->id . '" ' .
                        'data-action="' . $action . '" ' .
                        'data-desc="' . $description . '" ' .
                        'data-payload="' . $payloadJson . '" ' .
                        'style="padding: 0.35rem 0.7rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">' .
                        '<i data-lucide="file-diff" style="width: 13px; height: 13px;"></i>' .
                        '<span>Inspeksi Diff</span>' .
                    '</button>';
                })
                ->rawColumns(['created_at_formatted', 'user_info', 'action_badge', 'description_display', 'ip_address_display', 'payload_action'])
                ->make(true);
        }

        $users = User::select('id', 'name', 'email')->orderBy('name', 'asc')->get();
        $totalLogs = AuditLog::count();
        $todayLogs = AuditLog::where('created_at', '>=', now()->startOfDay())->count();

        return view('admin.system.activity-logs', compact('users', 'totalLogs', 'todayLogs'));
    }
}
