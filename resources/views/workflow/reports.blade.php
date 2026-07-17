@extends('layouts.app')

@section('title', 'Rekap Produksi')
@section('page_title', 'Rekap Produksi')

@section('topbar_action')
    <button type="button" class="btn-primary no-print" onclick="window.print()">Cetak Rekap</button>
@endsection

@section('content')
    <form method="GET" action="{{ route('reports.index') }}" class="filter-grid no-print">
        <input type="text" name="kode" value="{{ $filters['kode'] ?? '' }}" placeholder="Cari kode PO">
        <input type="text" name="penjahit" value="{{ $filters['penjahit'] ?? '' }}" placeholder="Cari penjahit">
        <select name="status">
            <option value="">Semua status</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
            @endforeach
        </select>
        <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}">
        <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}">
        <button type="submit" class="btn-small">Filter</button>
        <a href="{{ route('reports.index') }}" class="btn-secondary">Reset</a>
    </form>

    <div class="summary-grid">
        <div class="summary-card"><span>Total PO</span><strong>{{ $summary['total_orders'] }}</strong></div>
        <div class="summary-card"><span>Total Qty</span><strong>{{ $summary['total_quantity'] }}</strong></div>
        <div class="summary-card"><span>Siap Packing</span><strong>{{ $summary['packing_orders'] }}</strong></div>
        <div class="summary-card"><span>Terkirim</span><strong>{{ $summary['sent_orders'] }}</strong></div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th><th>Tanggal</th><th>Style</th><th>Warna</th><th>Penjahit</th><th>Total</th><th>Status</th><th>Tgl Kirim</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><strong>{{ $order->kode }}</strong></td>
                        <td>{{ $order->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $order->style }}</td>
                        <td>{{ $order->warna }}</td>
                        <td>{{ $order->penjahit ?: '-' }}</td>
                        <td>{{ $order->total }}</td>
                        <td><span class="status-badge status-{{ $order->status }}">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span></td>
                        <td>{{ $order->delivered_at ? $order->delivered_at->format('d/m/Y') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8">Tidak ada data sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
