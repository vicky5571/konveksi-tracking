<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Services\OrderWorkflowService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderWorkflowServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_qc_awal_splits_permak_and_cuci_from_original_order(): void
    {
        $order = $this->makeOrder([
            'S' => 10,
            'M' => 5,
            'L' => 0,
            'XL' => 0,
            'XXL' => 0,
        ]);
        $service = app(OrderWorkflowService::class);

        $service->splitFromQcAwal($order, ['S' => 2], ['M' => 1]);

        $order->refresh()->load('sizes');
        $this->assertSame(Order::STATUS_PACKING, $order->status);
        $this->assertSame(12, $order->total);
        $this->assertSame(8, $order->sizes->where('size', 'S')->first()->quantity);
        $this->assertSame(4, $order->sizes->where('size', 'M')->first()->quantity);

        $this->assertDatabaseHas('orders', [
            'kode' => 'PO-001-P',
            'original_kode' => 'PO-001',
            'total' => 2,
            'status' => Order::STATUS_PENDING_PERMAK,
        ]);
        $this->assertDatabaseHas('orders', [
            'kode' => 'PO-001-C',
            'original_kode' => 'PO-001',
            'total' => 1,
            'status' => Order::STATUS_PENDING_CUCI,
        ]);
    }

    public function test_qc_ulang_pass_merges_split_order_back_to_original(): void
    {
        $original = $this->makeOrder(['S' => 8, 'M' => 4]);
        $split = $this->makeOrder(['S' => 2], [
            'kode' => 'PO-001-P',
            'original_kode' => 'PO-001',
            'total' => 2,
            'status' => Order::STATUS_QC_ULANG,
        ]);
        $service = app(OrderWorkflowService::class);

        $service->passQcUlang($split);

        $original->refresh()->load('sizes');
        $this->assertSame(14, $original->total);
        $this->assertSame(10, $original->sizes->where('size', 'S')->first()->quantity);
        $this->assertDatabaseMissing('orders', ['kode' => 'PO-001-P']);
    }

    private function makeOrder(array $sizes, array $attributes = []): Order
    {
        $order = Order::create(array_merge([
            'kode' => 'PO-001',
            'tanggal' => '2026-06-23',
            'style' => 'Basic Tee',
            'warna' => 'Hitam',
            'total' => array_sum($sizes),
            'status' => Order::STATUS_PACKING,
        ], $attributes));

        foreach ($sizes as $size => $quantity) {
            $order->sizes()->create(['size' => $size, 'quantity' => $quantity]);
        }

        return $order;
    }
}

