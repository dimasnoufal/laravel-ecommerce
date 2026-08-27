<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportType;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display live interactive Business Intelligence & Analytics Dashboard.
     */
    public function index(Request $request)
    {
        $reportType = strtoupper($request->get('type', ReportType::SALES->value));
        $validTypes = array_column(ReportType::cases(), 'value');
        if (!in_array($reportType, $validTypes)) {
            $reportType = ReportType::SALES->value;
        }

        // Date Range parsing
        $preset = $request->get('preset', 'last_30_days');
        $now = Carbon::now();

        switch ($preset) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                break;
            case 'last_7_days':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $startDate = $now->copy()->subMonth()->startOfMonth();
                $endDate = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'custom':
                $startDate = $request->filled('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : $now->copy()->subDays(29)->startOfDay();
                $endDate = $request->filled('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : $now->copy()->endOfDay();
                break;
            case 'last_30_days':
            default:
                $preset = 'last_30_days';
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
        }

        if ($startDate->gt($endDate)) {
            $startDate = $endDate->copy()->subDays(7)->startOfDay();
        }

        $forceRefresh = $request->boolean('refresh');

        // Fetch Live Aggregate Analytics Data & Chart
        $reportData = $this->reportService->getReportData($reportType, $startDate, $endDate, $forceRefresh);

        // Fetch Paginated Detail Records
        $detailRecords = $this->reportService->getDetailRecords($reportType, $startDate, $endDate, 50);

        return view('admin.analytics.index', compact(
            'reportType',
            'preset',
            'startDate',
            'endDate',
            'reportData',
            'detailRecords'
        ));
    }

    /**
     * Quick stream CSV download for current analytics filter.
     */
    public function exportLive(Request $request)
    {
        $reportType = strtoupper($request->get('type', ReportType::SALES->value));
        $startDate = $request->filled('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        $filename = 'analytics_' . strtolower($reportType) . '_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.csv';

        $response = new StreamedResponse(function () use ($reportType, $startDate, $endDate) {
            $this->reportService->streamCsv($reportType, $startDate, $endDate);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    /**
     * Print / PDF-ready view for live filter.
     */
    public function printLive(Request $request)
    {
        $reportType = strtoupper($request->get('type', ReportType::SALES->value));
        $startDate = $request->filled('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        $reportData = $this->reportService->getReportData($reportType, $startDate, $endDate);
        $detailRecords = $this->reportService->getDetailRecords($reportType, $startDate, $endDate, 500);

        $reportMeta = [
            'name' => 'Live ' . ucfirst(strtolower($reportType)) . ' Analytics Report',
            'type' => $reportType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generator_name' => Auth::user()?->name ?? 'System Admin',
            'created_at' => Carbon::now(),
        ];

        return view('admin.reports.print', compact('reportMeta', 'reportData', 'detailRecords'));
    }
}
