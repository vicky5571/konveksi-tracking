<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QcController extends Controller
{
    public function __construct(
        private readonly OrderWorkflowService $workflowService,
    ) {
    }

    public function split(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'permak' => 'required|array',
            'permak.*' => 'integer|min:0',
            'cuci' => 'required|array',
            'cuci.*' => 'integer|min:0',
        ]);

        $this->workflowService->splitFromQcAwal(
            $order,
            $validated['permak'],
            $validated['cuci']
        );

        return redirect()->route('qc-awal.index')
            ->with('success', "QC Awal PO {$order->kode} berhasil diproses.");
    }

    public function moveToQcUlang(Order $order): RedirectResponse
    {
        $this->workflowService->moveToQcUlang($order);

        return redirect()->back()
            ->with('success', "PO {$order->kode} masuk QC Ulang.");
    }

    public function passQcUlang(Order $order): RedirectResponse
    {
        $kode = $order->kode;
        $this->workflowService->passQcUlang($order);

        return redirect()->route('qc-ulang.index')
            ->with('success', "PO {$kode} lolos QC Ulang dan digabung ke PO asli jika sejajar.");
    }
}
