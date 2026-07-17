@extends('layouts.app')

@section('title', 'Detail PO - ' . $order->kode)
@section('page_title', 'Detail Purchase Order')

@section('topbar_action')
    <a href="{{ route('orders.index') }}" class="btn-secondary">Kembali ke Daftar</a>
@endsection

@section('content')
    <div class="detail-grid">
        <div class="detail-card">
            <h3 class="card-title">Informasi PO</h3>
            <dl class="detail-list">
                <dt>Kode PO</dt>
                <dd><strong>{{ $order->kode }}</strong></dd>

                <dt>Tanggal</dt>
                <dd>{{ $order->tanggal->format('d/m/Y') }}</dd>

                <dt>Style</dt>
                <dd>{{ $order->style }}</dd>

                <dt>Warna</dt>
                <dd>{{ $order->warna }}</dd>

                <dt>Total Kuantitas</dt>
                <dd><strong>{{ $order->total }} pcs</strong></dd>

                <dt>Status</dt>
                <dd>
                    <span class="status-badge status-{{ $order->status }}">
                        {{ str_replace('_', ' ', ucfirst($order->status)) }}
                    </span>
                </dd>

                @if ($order->penjahit)
                    <dt>Penjahit</dt>
                    <dd>{{ $order->penjahit }}</dd>
                @endif

                @if ($order->delivered_at)
                    <dt>Tanggal Kirim</dt>
                    <dd>{{ $order->delivered_at->format('d/m/Y') }}</dd>
                @endif
            </dl>
        </div>

        <div class="detail-card">
            <h3 class="card-title">Rincian Ukuran</h3>
            <div class="size-breakdown">
                @foreach ($order->sizes as $size)
                    <div class="size-item">
                        <span class="size-label">{{ $size->size }}</span>
                        <span class="size-qty">{{ $size->quantity }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="detail-card detail-card-full">
            <h3 class="card-title">Riwayat Status</h3>
            <div class="history-timeline">
                @foreach ($order->histories as $history)
                    <div class="history-item">
                        <div class="history-dot"></div>
                        <div class="history-content">
                            <span class="status-badge status-{{ $history->status }}">
                                {{ str_replace('_', ' ', ucfirst($history->status)) }}
                            </span>
                            <span class="history-time">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        .detail-grid {
            display: grid;
            gap: 24px;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        }

        .detail-card {
            padding: 24px;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
        }

        .detail-card-full {
            grid-column: 1 / -1;
        }

        .card-title {
            margin: 0 0 20px;
            color: var(--text);
            font-size: 1.2rem;
            font-weight: 600;
        }

        .detail-list {
            display: grid;
            gap: 14px;
        }

        .detail-list dt {
            color: var(--muted);
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .detail-list dd {
            margin: 4px 0 0;
            color: var(--text);
            font-size: 1rem;
        }

        .size-breakdown {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        }

        .size-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 16px;
            background: rgba(124, 58, 237, 0.08);
            border: 1px solid rgba(124, 58, 237, 0.2);
            border-radius: 12px;
        }

        .size-label {
            color: var(--muted);
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.06em;
        }

        .size-qty {
            color: var(--text);
            font-size: 1.5rem;
            font-weight: 800;
        }

        .history-timeline {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .history-item {
            display: grid;
            align-items: center;
            gap: 16px;
            grid-template-columns: 12px 1fr;
        }

        .history-dot {
            width: 12px;
            height: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            border-radius: 50%;
            box-shadow: 0 0 12px rgba(124, 58, 237, 0.5);
        }

        .history-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
        }

        .history-time {
            color: var(--muted);
            font-size: 0.85rem;
        }

        .btn-secondary {
            display: inline-block;
            padding: 10px 20px;
            color: var(--text);
            font-weight: 600;
            text-decoration: none;
            background: var(--surface-soft);
            border: 1px solid var(--line);
            border-radius: 14px;
            transition: all 180ms ease;
        }

        .btn-secondary:hover {
            background: var(--surface-strong);
        }
    </style>
@endsection
