<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Hitung jumlah PO per status
        $statusCounts = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Total keseluruhan
        $totalOrders    = Order::count();
        $totalQuantity  = Order::sum('total');
        $totalDikirim   = $statusCounts[Order::STATUS_DIKIRIM] ?? 0;
        $totalPacking   = $statusCounts[Order::STATUS_PACKING] ?? 0;

        // Alur produksi: jumlah PO per tahap
        $pipeline = [
            ['label' => 'Potongan Selesai', 'status' => Order::STATUS_POTONGAN_SELESAI, 'color' => '#60a5fa', 'route' => 'jahit.index'],
            ['label' => 'Dalam Jahit',      'status' => Order::STATUS_DALAM_JAHIT,      'color' => '#fbbf24', 'route' => 'jahit.index'],
            ['label' => 'QC Awal',          'status' => Order::STATUS_QC_AWAL,          'color' => '#a78bfa', 'route' => 'qc-awal.index'],
            ['label' => 'Pending Permak',   'status' => Order::STATUS_PENDING_PERMAK,   'color' => '#fb923c', 'route' => 'permak.index'],
            ['label' => 'Pending Cuci',     'status' => Order::STATUS_PENDING_CUCI,     'color' => '#fb923c', 'route' => 'cuci.index'],
            ['label' => 'QC Ulang',         'status' => Order::STATUS_QC_ULANG,         'color' => '#f472b6', 'route' => 'qc-ulang.index'],
            ['label' => 'Packing',          'status' => Order::STATUS_PACKING,          'color' => '#34d399', 'route' => 'packing.index'],
            ['label' => 'Dikirim',          'status' => Order::STATUS_DIKIRIM,          'color' => '#22c55e', 'route' => 'tracking.index'],
        ];

        foreach ($pipeline as &$stage) {
            $stage['count'] = $statusCounts[$stage['status']] ?? 0;
        }
        unset($stage);

        // 5 PO terbaru
        $recentOrders = Order::with('sizes')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Distribusi style terbanyak (top 5)
        $topStyles = Order::selectRaw('style, COUNT(*) as jumlah')
            ->groupBy('style')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalOrders',
            'totalQuantity',
            'totalDikirim',
            'totalPacking',
            'pipeline',
            'recentOrders',
            'topStyles',
        ));
    }
}
