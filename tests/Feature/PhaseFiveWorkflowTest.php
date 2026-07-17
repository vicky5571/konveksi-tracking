<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PhaseFiveWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tracking_filter_finds_matching_order(): void
    {
        Order::create([
            'kode' => 'PO-FILTER-1',
            'tanggal' => '2026-06-23',
            'style' => 'Kemeja',
            'warna' => 'Biru',
            'total' => 10,
            'penjahit' => 'Sari',
            'status' => Order::STATUS_PACKING,
        ]);
        Order::create([
            'kode' => 'PO-FILTER-2',
            'tanggal' => '2026-06-23',
            'style' => 'Kemeja',
            'warna' => 'Merah',
            'total' => 5,
            'penjahit' => 'Budi',
            'status' => Order::STATUS_DALAM_JAHIT,
        ]);

        $response = $this->get('/tracking?penjahit=Sari&status=' . Order::STATUS_PACKING);

        $response->assertOk();
        $response->assertSee('PO-FILTER-1');
        $response->assertDontSee('PO-FILTER-2');
    }

    public function test_packing_order_can_be_shipped(): void
    {
        $order = Order::create([
            'kode' => 'PO-SHIP-1',
            'tanggal' => '2026-06-23',
            'style' => 'Kaos',
            'warna' => 'Hitam',
            'total' => 12,
            'status' => Order::STATUS_PACKING,
        ]);

        $response = $this->patch("/packing/{$order->id}/ship", [
            'kurir' => 'JNE',
            'resi' => 'JNE123456',
            'delivered_at' => '2026-06-23T10:00',
        ]);

        $response->assertRedirect('/packing');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'kurir' => 'JNE',
            'resi' => 'JNE123456',
            'status' => Order::STATUS_DIKIRIM,
        ]);
        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'status' => Order::STATUS_DIKIRIM,
        ]);
    }
}
