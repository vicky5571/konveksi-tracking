<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderSize;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('sizes')
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $styleOptions = Order::select('style')->distinct()->orderBy('style')->pluck('style');
        $warnaOptions = Order::select('warna')->distinct()->orderBy('warna')->pluck('warna');

        return view('orders.create', [
            'sizes'        => Order::SIZES,
            'styleOptions' => $styleOptions,
            'warnaOptions' => $warnaOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50',
            'tanggal' => 'required|date',
            'style' => 'required|string|max:100',
            'warna' => 'required|string|max:100',
            'sizes' => 'required|array',
            'sizes.*' => 'integer|min:0',
        ]);
        $validated['sizes'] = $this->normalizeSizes($validated['sizes']);

        if ($this->isDuplicate($validated)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'PO dengan data identik sudah ada di sistem.');
        }

        $order = Order::create([
            'kode' => $validated['kode'],
            'tanggal' => $validated['tanggal'],
            'style' => $validated['style'],
            'warna' => $validated['warna'],
            'total' => array_sum($validated['sizes']),
            'status' => Order::STATUS_POTONGAN_SELESAI,
        ]);

        foreach (Order::SIZES as $size) {
            $quantity = $validated['sizes'][$size] ?? 0;
            if ($quantity > 0) {
                OrderSize::create([
                    'order_id' => $order->id,
                    'size' => $size,
                    'quantity' => $quantity,
                ]);
            }
        }

        $order->histories()->create([
            'status' => Order::STATUS_POTONGAN_SELESAI,
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', "PO {$order->kode} berhasil dibuat.");
    }

    public function show(Order $order)
    {
        $order->load('sizes', 'histories');

        return view('orders.show', compact('order'));
    }

    private function normalizeSizes(array $sizes): array
    {
        $normalized = [];

        foreach (Order::SIZES as $size) {
            $normalized[$size] = (int) ($sizes[$size] ?? 0);
        }

        return $normalized;
    }

    private function isDuplicate(array $data): bool
    {
        $candidates = Order::where('kode', $data['kode'])
            ->where('tanggal', $data['tanggal'])
            ->where('style', $data['style'])
            ->where('warna', $data['warna'])
            ->with('sizes')
            ->get();

        foreach ($candidates as $candidate) {
            $isExactMatch = true;

            foreach (Order::SIZES as $size) {
                $incomingQuantity = $data['sizes'][$size] ?? 0;
                $existingSize = $candidate->sizes
                    ->where('size', $size)
                    ->first();
                $existingQuantity = $existingSize?->quantity ?? 0;

                if ($incomingQuantity !== $existingQuantity) {
                    $isExactMatch = false;
                    break;
                }
            }

            if ($isExactMatch) {
                return true;
            }
        }

        return false;
    }
}
