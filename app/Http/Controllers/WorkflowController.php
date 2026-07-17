<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkflowController extends Controller
{
    public function jahit(Request $request): View
    {
        $penjahitOptions = Order::select('penjahit')
            ->whereNotNull('penjahit')
            ->where('penjahit', '!=', '')
            ->distinct()
            ->orderBy('penjahit')
            ->pluck('penjahit');

        return $this->statusPage($request, 'jahit.index', 'Distribusi Penjahit', [
            Order::STATUS_POTONGAN_SELESAI,
            Order::STATUS_DALAM_JAHIT,
            Order::STATUS_QC_AWAL,
        ], true)->with('penjahitOptions', $penjahitOptions);
    }

    public function qcAwal(Request $request): View
    {
        return $this->statusPage($request, 'qc-awal.index', 'QC Awal', [Order::STATUS_QC_AWAL]);
    }

    public function permak(Request $request): View
    {
        return $this->statusPage($request, 'permak.index', 'Pending Permak', [Order::STATUS_PENDING_PERMAK]);
    }

    public function cuci(Request $request): View
    {
        return $this->statusPage($request, 'cuci.index', 'Pending Cuci', [Order::STATUS_PENDING_CUCI]);
    }

    public function qcUlang(Request $request): View
    {
        return $this->statusPage($request, 'qc-ulang.index', 'QC Ulang', [Order::STATUS_QC_ULANG]);
    }

    public function packing(Request $request): View
    {
        return $this->statusPage($request, 'packing.index', 'Packing', [Order::STATUS_PACKING]);
    }

    public function tracking(Request $request): View
    {
        return $this->statusPage($request, 'tracking.index', 'Tracking Order', [], true);
    }

    public function reports(Request $request): View
    {
        $filters = $request->only(['kode', 'penjahit', 'status', 'tanggal_mulai', 'tanggal_selesai']);
        
        $queryFilters = $filters;
        unset($queryFilters['tanggal_mulai'], $queryFilters['tanggal_selesai']);

        $query = Order::with('sizes')->filter($queryFilters);

        if (!empty($filters['tanggal_mulai'])) {
            $query->whereDate('delivered_at', '>=', $filters['tanggal_mulai']);
        }
        if (!empty($filters['tanggal_selesai'])) {
            $query->whereDate('delivered_at', '<=', $filters['tanggal_selesai']);
        }

        $orders = $query->orderBy('delivered_at', 'desc')->orderBy('tanggal', 'desc')->get();
        $summary = [
            'total_orders' => $orders->count(),
            'total_quantity' => $orders->sum('total'),
            'sent_orders' => $orders->where('status', Order::STATUS_DIKIRIM)->count(),
            'packing_orders' => $orders->where('status', Order::STATUS_PACKING)->count(),
        ];

        return view('workflow.reports', [
            'orders' => $orders,
            'summary' => $summary,
            'filters' => $filters,
            'statuses' => Order::statuses(),
        ]);
    }

    public function assignTailor(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate(['penjahit' => 'required|string|max:100']);

        $order->update([
            'penjahit' => $validated['penjahit'],
            'status' => Order::STATUS_DALAM_JAHIT,
        ]);
        $order->histories()->create(['status' => Order::STATUS_DALAM_JAHIT]);

        return redirect()->route('jahit.index')->with('success', "PO {$order->kode} masuk proses jahit.");
    }

    public function finishSewing(Order $order): RedirectResponse
    {
        $order->update(['status' => Order::STATUS_QC_AWAL]);
        $order->histories()->create(['status' => Order::STATUS_QC_AWAL]);

        return redirect()->route('jahit.index')->with('success', "PO {$order->kode} dikirim ke QC Awal.");
    }

    public function ship(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'delivered_at' => 'nullable|date',
        ]);

        $order->update([
            'delivered_at' => $validated['delivered_at'] ?? now(),
            'status'       => Order::STATUS_DIKIRIM,
        ]);
        $order->histories()->create(['status' => Order::STATUS_DIKIRIM]);

        return redirect()->route('packing.index')->with('success', "PO {$order->kode} berhasil dikirim.");
    }

    private function statusPage(Request $request, string $routeName, string $title, array $statuses, bool $filterable = false): View
    {
        $filters = $request->only(['kode', 'penjahit', 'status', 'tanggal_mulai', 'tanggal_selesai']);
        $query = Order::with('sizes')->orderBy('updated_at', 'desc');

        if ($statuses) {
            $query->whereIn('status', $statuses);
        }

        if ($filterable) {
            $query->filter($filters);
        }

        return view('workflow.status-page', [
            'orders' => $query->paginate(20)->withQueryString(),
            'routeName' => $routeName,
            'title' => $title,
            'statuses' => Order::statuses(),
            'filters' => $filters,
            'filterable' => $filterable,
        ]);
    }
}
