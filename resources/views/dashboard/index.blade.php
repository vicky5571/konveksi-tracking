@extends('layouts.app')

@section('title', 'Dashboard - Tracing Konveksi')
@section('page_title', 'Dashboard Produksi')

@section('topbar_action')
    <a href="{{ route('orders.create') }}" class="btn-primary" id="btn-input-po">+ Input PO Baru</a>
@endsection

@section('content')

    {{-- ── Stat Cards ── --}}
    <div class="dash-stats">
        <div class="stat-card" id="stat-total-po">
            <div class="stat-icon" style="--c: #7c3aed; --c2: #38bdf8;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            </div>
            <div class="stat-body">
                <span class="stat-label">Total PO</span>
                <strong class="stat-value" data-target="{{ $totalOrders }}">0</strong>
            </div>
        </div>

        <div class="stat-card" id="stat-total-qty">
            <div class="stat-icon" style="--c: #38bdf8; --c2: #34d399;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            </div>
            <div class="stat-body">
                <span class="stat-label">Total Kuantitas</span>
                <strong class="stat-value" data-target="{{ $totalQuantity }}">0</strong>
                <span class="stat-unit">pcs</span>
            </div>
        </div>

        <div class="stat-card" id="stat-packing">
            <div class="stat-icon" style="--c: #34d399; --c2: #22c55e;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
            </div>
            <div class="stat-body">
                <span class="stat-label">Siap Packing</span>
                <strong class="stat-value" data-target="{{ $totalPacking }}">0</strong>
                <span class="stat-unit">PO</span>
            </div>
        </div>

        <div class="stat-card" id="stat-dikirim">
            <div class="stat-icon" style="--c: #22c55e; --c2: #4ade80;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <div class="stat-body">
                <span class="stat-label">Total Dikirim</span>
                <strong class="stat-value" data-target="{{ $totalDikirim }}">0</strong>
                <span class="stat-unit">PO</span>
            </div>
        </div>
    </div>

    {{-- ── Pipeline Produksi ── --}}
    <div class="dash-section">
        <h2 class="dash-section-title">Alur Produksi</h2>
        <div class="pipeline-grid">
            @foreach ($pipeline as $stage)
                <a href="{{ route($stage['route']) }}" class="pipeline-card {{ $stage['count'] > 0 ? 'pipeline-card--active' : '' }}" id="pipeline-{{ $stage['status'] }}" style="--stage-color: {{ $stage['color'] }};">
                    <div class="pipeline-dot"></div>
                    <div class="pipeline-body">
                        <span class="pipeline-label">{{ $stage['label'] }}</span>
                        <strong class="pipeline-count">{{ $stage['count'] }}</strong>
                        <span class="pipeline-unit">PO</span>
                    </div>
                    @if ($stage['count'] > 0)
                        <div class="pipeline-glow"></div>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    {{-- ── Bottom Grid: PO Terbaru + Top Style ── --}}
    <div class="dash-bottom">

        {{-- PO Terbaru --}}
        <div class="dash-card" id="recent-po">
            <div class="dash-card-header">
                <h3>PO Terbaru</h3>
                <a href="{{ route('orders.index') }}" class="link-action">Lihat semua →</a>
            </div>
            @if ($recentOrders->isEmpty())
                <p class="dash-empty">Belum ada PO yang diinput.</p>
            @else
                <ul class="recent-list">
                    @foreach ($recentOrders as $order)
                        <li class="recent-item">
                            <a href="{{ route('orders.show', $order) }}" class="recent-link">
                                <div class="recent-main">
                                    <strong>{{ $order->kode }}</strong>
                                    <small>{{ $order->style }} / {{ $order->warna }}</small>
                                </div>
                                <div class="recent-right">
                                    <span class="status-badge status-{{ $order->status }}">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
                                    <small>{{ $order->total }} pcs</small>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Top Style --}}
        <div class="dash-card" id="top-styles">
            <div class="dash-card-header">
                <h3>Style Terbanyak</h3>
            </div>
            @if ($topStyles->isEmpty())
                <p class="dash-empty">Belum ada data style.</p>
            @else
                @php $maxJumlah = $topStyles->first()->jumlah; @endphp
                <ul class="style-list">
                    @foreach ($topStyles as $i => $style)
                        <li class="style-item">
                            <div class="style-rank">{{ $i + 1 }}</div>
                            <div class="style-info">
                                <span class="style-name">{{ $style->style }}</span>
                                <div class="style-bar-wrap">
                                    <div class="style-bar" style="width: {{ $maxJumlah > 0 ? round($style->jumlah / $maxJumlah * 100) : 0 }}%"></div>
                                </div>
                            </div>
                            <span class="style-count">{{ $style->jumlah }} PO</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

    </div>

    <style>
        /* ── Stat Cards ── */
        .dash-stats {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            margin-bottom: 28px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 22px 20px;
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            transition: transform 200ms ease, box-shadow 200ms ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.3);
        }

        .stat-icon {
            flex-shrink: 0;
            display: grid;
            place-items: center;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--c), var(--c2));
            box-shadow: 0 8px 20px color-mix(in srgb, var(--c) 40%, transparent);
        }

        .stat-icon svg {
            width: 22px;
            height: 22px;
            color: #fff;
        }

        .stat-body {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, var(--text), var(--muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-unit {
            color: var(--muted);
            font-size: 0.78rem;
        }

        /* ── Pipeline ── */
        .dash-section {
            margin-bottom: 28px;
        }

        .dash-section-title {
            margin: 0 0 14px;
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .pipeline-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }

        .pipeline-card {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 18px 16px;
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            text-decoration: none;
            overflow: hidden;
            transition: transform 180ms ease, border-color 180ms ease, box-shadow 180ms ease;
        }

        .pipeline-card:hover {
            transform: translateY(-3px);
            border-color: color-mix(in srgb, var(--stage-color) 60%, transparent);
            box-shadow: 0 12px 30px rgba(0,0,0,0.25);
        }

        .pipeline-card--active {
            border-color: color-mix(in srgb, var(--stage-color) 40%, transparent);
            background: color-mix(in srgb, var(--stage-color) 8%, rgba(15, 23, 42, 0.55));
        }

        .pipeline-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--stage-color);
            box-shadow: 0 0 10px var(--stage-color);
        }

        .pipeline-card--active .pipeline-dot {
            animation: pulse-dot 2s ease infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { box-shadow: 0 0 6px var(--stage-color); }
            50% { box-shadow: 0 0 18px var(--stage-color), 0 0 30px var(--stage-color); }
        }

        .pipeline-body {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .pipeline-label {
            color: var(--muted);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .pipeline-count {
            color: var(--stage-color);
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
        }

        .pipeline-unit {
            color: var(--muted);
            font-size: 0.72rem;
        }

        .pipeline-glow {
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--stage-color);
            opacity: 0.07;
            filter: blur(20px);
            pointer-events: none;
        }

        /* ── Bottom Grid ── */
        .dash-bottom {
            display: grid;
            gap: 20px;
            grid-template-columns: 1fr 1fr;
        }

        @media (max-width: 900px) {
            .dash-bottom { grid-template-columns: 1fr; }
        }

        .dash-card {
            padding: 22px;
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
        }

        .dash-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .dash-card-header h3 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .dash-empty {
            color: var(--muted);
            font-size: 0.88rem;
            text-align: center;
            padding: 24px 0;
        }

        /* ── Recent PO List ── */
        .recent-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .recent-item {
            border-radius: 12px;
            transition: background 160ms ease;
        }

        .recent-item:hover {
            background: rgba(255,255,255,0.04);
        }

        .recent-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 12px;
            text-decoration: none;
            color: inherit;
        }

        .recent-main strong {
            display: block;
            font-size: 0.92rem;
        }

        .recent-main small {
            color: var(--muted);
            font-size: 0.78rem;
            margin-top: 2px;
        }

        .recent-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
            flex-shrink: 0;
        }

        .recent-right small {
            color: var(--muted);
            font-size: 0.75rem;
        }

        /* ── Style Bar Chart ── */
        .style-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .style-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .style-rank {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: rgba(124, 58, 237, 0.15);
            color: var(--accent);
            font-size: 0.72rem;
            font-weight: 800;
        }

        .style-info {
            flex: 1;
            min-width: 0;
        }

        .style-name {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 5px;
        }

        .style-bar-wrap {
            height: 5px;
            background: rgba(255,255,255,0.07);
            border-radius: 999px;
            overflow: hidden;
        }

        .style-bar {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--accent), var(--accent-2));
            transition: width 800ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .style-count {
            flex-shrink: 0;
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 700;
        }
    </style>

    <script>
        // Animasi angka counter
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.stat-value[data-target]').forEach(function (el) {
                const target = parseInt(el.dataset.target, 10);
                if (isNaN(target) || target === 0) { el.textContent = '0'; return; }
                const duration = 900;
                const step = Math.ceil(duration / target);
                let current = 0;
                const timer = setInterval(function () {
                    current += Math.ceil(target / 40);
                    if (current >= target) { current = target; clearInterval(timer); }
                    el.textContent = current.toLocaleString('id-ID');
                }, duration / 40);
            });

            // Animasi bar chart style (trigger setelah render)
            setTimeout(function () {
                document.querySelectorAll('.style-bar').forEach(function (bar) {
                    const w = bar.style.width;
                    bar.style.width = '0%';
                    requestAnimationFrame(function () {
                        bar.style.width = w;
                    });
                });
            }, 100);
        });
    </script>

@endsection
