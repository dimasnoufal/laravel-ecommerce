<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportType;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display list of saved report archives.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Report::with('generator')->select('reports.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('report_info', function ($row) {
                    $hasFile = $row->file_path && Storage::disk('local')->exists($row->file_path);
                    $iconColor = $hasFile ? 'var(--primary)' : 'var(--text-muted)';
                    $iconBg = $hasFile ? 'var(--primary-light)' : 'var(--bg-body)';

                    return '<div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 38px; height: 38px; border-radius: var(--radius-md); background: ' . $iconBg . '; color: ' . $iconColor . '; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 0.9375rem;">' . e($row->name) . '</div>
                                    <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.2rem;">
                                        <code class="code-pill" style="font-size: 0.7rem; padding: 0.1rem 0.35rem;">#' . $row->id . '</code>
                                        ' . ($hasFile ? '<span style="font-size: 0.7rem; color: var(--success); font-weight: 600;">CSV Tersedia</span>' : '<span style="font-size: 0.7rem; color: var(--text-muted);">Tidak ada file fisik</span>') . '
                                    </div>
                                </div>
                            </div>';
                })
                ->addColumn('type_badge', function ($row) {
                    return '<span class="status-pill status-processing" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">' . e($row->type) . '</span>';
                })
                ->addColumn('date_range', function ($row) {
                    $start = $row->start_date ? $row->start_date->format('d M Y') : '-';
                    $end = $row->end_date ? $row->end_date->format('d M Y') : '-';
                    return '<div style="font-weight: 600; font-size: 0.8125rem; color: var(--text-main);">' . $start . ' <span style="color: var(--text-muted); font-weight: 400;">s/d</span> ' . $end . '</div>';
                })
                ->addColumn('generator_name', function ($row) {
                    $name = $row->generator->name ?? 'System Admin';
                    return '<div style="display: flex; align-items: center; gap: 0.4rem;">
                                <div style="width: 22px; height: 22px; border-radius: 50%; background: var(--primary-light); color: var(--primary); font-size: 0.65rem; font-weight: 800; display: flex; align-items: center; justify-content: center;">
                                    ' . strtoupper(substr($name, 0, 1)) . '
                                </div>
                                <span style="font-weight: 600; font-size: 0.8125rem; color: var(--text-main);">' . e($name) . '</span>
                            </div>';
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return '<div style="font-size: 0.78125rem; color: var(--text-muted);">' . ($row->created_at ? $row->created_at->format('d M Y H:i') : '-') . '</div>';
                })
                ->addColumn('action', function ($row) {
                    $downloadBtn = '';
                    if ($row->file_path) {
                        $downloadBtn = '<a href="' . route('admin.reports.download', $row) . '" class="tbl-btn tbl-btn-edit" style="padding: 0.35rem 0.65rem; text-decoration: none;" title="Download CSV">
                                            <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                                            <span>CSV</span>
                                        </a>';
                    }

                    $printBtn = '<a href="' . route('admin.reports.print', $row) . '" target="_blank" class="tbl-btn" style="background: var(--bg-body); border-color: var(--border-color); color: var(--text-main); padding: 0.35rem 0.65rem; text-decoration: none;" title="Print / PDF">
                                    <i data-lucide="printer" style="width: 14px; height: 14px;"></i>
                                    <span>Print</span>
                                </a>';

                    $metaBtn = '';
                    if (!empty($row->metadata)) {
                        $metaJson = htmlspecialchars(json_encode($row->metadata), ENT_QUOTES, 'UTF-8');
                        $metaName = htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8');
                        $metaBtn = '<button type="button" class="tbl-btn" style="background: var(--bg-body); border-color: var(--border-color); color: var(--text-main); padding: 0.35rem 0.65rem;" onclick="openMetadataInspector(' . $metaJson . ', \'' . $metaName . '\')" title="Lihat Snapshot">
                                        <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                    </button>';
                    }

                    $deleteBtn = '<button type="button" class="tbl-btn tbl-btn-delete" style="padding: 0.35rem 0.55rem;" onclick="deleteReportArchive(' . $row->id . ', \'' . addslashes(e($row->name)) . '\')" title="Hapus Dokumen">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                  </button>';

                    return '<div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 0.35rem;">
                                ' . $downloadBtn . '
                                ' . $printBtn . '
                                ' . $metaBtn . '
                                ' . $deleteBtn . '
                            </div>';
                })
                ->rawColumns(['report_info', 'type_badge', 'date_range', 'generator_name', 'created_at_formatted', 'action'])
                ->make(true);
        }

        $totalReports = Report::count();

        return view('admin.reports.index', compact('totalReports'));
    }

    /**
     * Generate & persist report archive in database + physical CSV file.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_column(ReportType::cases(), 'value')),
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $userId = Auth::id();

        $report = $this->reportService->saveReportArchive(
            $request->name,
            $request->type,
            $startDate,
            $endDate,
            $userId
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Dokumen laporan '{$report->name}' berhasil di-generate dan disimpan.",
                'data' => $report
            ]);
        }

        return redirect()->route('admin.reports.index')
            ->with('success', "Dokumen laporan '{$report->name}' berhasil di-generate dan disimpan.");
    }

    /**
     * Download an archived report file.
     */
    public function download(Report $report)
    {
        if (!$report->file_path || !Storage::disk('local')->exists($report->file_path)) {
            return back()->with('error', 'File laporan tidak ditemukan di server.');
        }

        $downloadName = Str::slug($report->name) . '_' . $report->created_at->format('Ymd_His') . '.csv';
        return Storage::disk('local')->download($report->file_path, $downloadName);
    }

    /**
     * Print / PDF-ready view for saved report.
     */
    public function printReport(Report $report)
    {
        $startDate = Carbon::parse($report->start_date)->startOfDay();
        $endDate = Carbon::parse($report->end_date)->endOfDay();

        $reportData = $this->reportService->getReportData($report->type, $startDate, $endDate);
        $detailRecords = $this->reportService->getDetailRecords($report->type, $startDate, $endDate, 500);

        $reportMeta = [
            'name' => $report->name,
            'type' => $report->type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generator_name' => $report->generator?->name ?? 'System',
            'created_at' => $report->created_at,
        ];

        return view('admin.reports.print', compact('reportMeta', 'reportData', 'detailRecords'));
    }

    /**
     * Delete an archived report from DB and Storage.
     */
    public function destroy(Request $request, Report $report)
    {
        if ($report->file_path && Storage::disk('local')->exists($report->file_path)) {
            Storage::disk('local')->delete($report->file_path);
        }

        $report->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen laporan berhasil dihapus permanen.'
            ]);
        }

        return redirect()->route('admin.reports.index')
            ->with('success', 'Dokumen laporan berhasil dihapus.');
    }
}
