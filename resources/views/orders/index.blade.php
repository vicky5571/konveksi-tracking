@extends('layouts.app')

@section('title', 'Daftar PO')
@section('page_title', 'Daftar Purchase Order')

@section('topbar_action')
    <a href="{{ route('orders.create') }}" class="btn-primary">+ Input PO Baru</a>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($orders->isEmpty())
        <div class="empty-state">
            <p class="eyebrow">Belum ada data</p>
            <h2>Belum ada PO yang diinput</h2>
            <p>Klik tombol "Input PO Baru" untuk mulai menambahkan purchase order.</p>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Style</th>
                        <th>Warna</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td><strong>{{ $order->kode }}</strong></td>
                            <td>{{ $order->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $order->style }}</td>
                            <td>{{ $order->warna }}</td>
                            <td>{{ $order->total }}</td>
                            <td>
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ str_replace('_', ' ', ucfirst($order->status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="link-action">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            {{ $orders->links() }}
        </div>
    @endif
@endsection

