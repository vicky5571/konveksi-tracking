<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderWorkflowService
{
    public function splitFromQcAwal(Order $order, array $permakSizes, array $cuciSizes): void
    {
        DB::transaction(function () use ($order, $permakSizes, $cuciSizes): void {
            $order->load('sizes');
            $permakSizes = $this->normalizeSizes($permakSizes);
            $cuciSizes = $this->normalizeSizes($cuciSizes);
            $this->validateSplitQuantities($order, $permakSizes, $cuciSizes);

            $this->reduceOriginalSizes($order, $permakSizes, $cuciSizes);
            $this->createSplitOrder($order, '-P', Order::STATUS_PENDING_PERMAK, $permakSizes);
            $this->createSplitOrder($order, '-C', Order::STATUS_PENDING_CUCI, $cuciSizes);

            $order->refresh();
            $order->update(['status' => Order::STATUS_PACKING]);
            $order->histories()->create(['status' => Order::STATUS_PACKING]);
        });
    }

    public function moveToQcUlang(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $order->update(['status' => Order::STATUS_QC_ULANG]);
            $order->histories()->create(['status' => Order::STATUS_QC_ULANG]);
        });
    }

    public function passQcUlang(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $order->update(['status' => Order::STATUS_PACKING]);
            $order->histories()->create(['status' => Order::STATUS_PACKING]);
            $this->tryMergeOrder($order->fresh(['sizes']));
        });
    }

    public function tryMergeOrder(Order $splitOrder): bool
    {
        if (!$splitOrder->original_kode || $splitOrder->status !== Order::STATUS_PACKING) {
            return false;
        }

        $original = Order::where('kode', $splitOrder->original_kode)
            ->where('status', Order::STATUS_PACKING)
            ->with('sizes')
            ->first();

        if (!$original) {
            return false;
        }

        foreach ($splitOrder->sizes as $splitSize) {
            $originalSize = $original->sizes()->firstOrCreate(
                ['size' => $splitSize->size],
                ['quantity' => 0]
            );
            $originalSize->increment('quantity', $splitSize->quantity);
        }

        $original->increment('total', $splitOrder->total);
        $original->histories()->create(['status' => Order::STATUS_PACKING]);
        $splitOrder->delete();

        return true;
    }

    private function normalizeSizes(array $sizes): array
    {
        $normalized = [];

        foreach (Order::SIZES as $size) {
            $normalized[$size] = max(0, (int) ($sizes[$size] ?? 0));
        }

        return $normalized;
    }

    private function validateSplitQuantities(Order $order, array $permakSizes, array $cuciSizes): void
    {
        foreach (Order::SIZES as $size) {
            $available = $order->sizes->where('size', $size)->first()?->quantity ?? 0;
            $requested = $permakSizes[$size] + $cuciSizes[$size];

            if ($requested > $available) {
                throw ValidationException::withMessages([
                    "sizes.{$size}" => "Jumlah cacat {$size} melebihi stok PO.",
                ]);
            }
        }
    }

    private function reduceOriginalSizes(Order $order, array $permakSizes, array $cuciSizes): void
    {
        foreach (Order::SIZES as $size) {
            $quantity = $permakSizes[$size] + $cuciSizes[$size];
            if ($quantity === 0) {
                continue;
            }

            $orderSize = $order->sizes->where('size', $size)->first();
            $orderSize->decrement('quantity', $quantity);
        }

        $order->update(['total' => max(0, $order->total - array_sum($permakSizes) - array_sum($cuciSizes))]);
    }

    private function createSplitOrder(Order $order, string $suffix, string $status, array $sizes): ?Order
    {
        $total = array_sum($sizes);
        if ($total === 0) {
            return null;
        }

        $splitOrder = Order::create([
            'kode' => $order->kode . $suffix,
            'original_kode' => $order->kode,
            'tanggal' => $order->tanggal,
            'style' => $order->style,
            'warna' => $order->warna,
            'total' => $total,
            'penjahit' => $order->penjahit,
            'status' => $status,
        ]);

        foreach ($sizes as $size => $quantity) {
            if ($quantity > 0) {
                $splitOrder->sizes()->create(['size' => $size, 'quantity' => $quantity]);
            }
        }

        $splitOrder->histories()->create(['status' => $status]);

        return $splitOrder;
    }
}
