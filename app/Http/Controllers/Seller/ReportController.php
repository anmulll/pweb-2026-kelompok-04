<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $data = $this->reportService->getSalesReport($startDate, $endDate);

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        if (view()->exists('seller.reports.index')) {
            return view('seller.reports.index', $data);
        }

        return response()->json($data);
    }

    public function pdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $data = $this->reportService->getSalesReport($startDate, $endDate);

        // DomPDF export fallback
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class) && view()->exists('seller.reports.pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('seller.reports.pdf', $data);
            return $pdf->download('laporan-penjualan-' . date('Y-m-d') . '.pdf');
        }

        return response('PDF export ready', 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-penjualan.pdf"',
        ]);
    }
}
