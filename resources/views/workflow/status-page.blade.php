@extends('layouts.app')

@section('title', $title . ' - Tracing Konveksi')
@section('page_title', $title)

@section('topbar_action')
    <span class="status-badge status-info">{{ $orders->total() }} PO</span>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    @if ($filterable ?? false)
        <form method="GET" action="{{ route($routeName) }}" class="filter-grid no-print">
            <input type="text" name="kode" value="{{ $filters['kode'] ?? '' }}" placeholder="Cari kode PO">
            <input type="text" name="penjahit" value="{{ $filters['penjahit'] ?? '' }}" placeholder="Cari penjahit">
            <button type="submit" class="btn-small">Filter</button>
            <a href="{{ route($routeName) }}" class="btn-secondary">Reset</a>
        </form>
    @endif

    @if ($orders->isEmpty())
        <div class="empty-state">
            <p class="eyebrow">{{ $title }}</p>
            <h2>Belum ada PO di tahap ini</h2>
            <p>Data akan muncul otomatis saat status PO masuk ke alur {{ strtolower($title) }}.</p>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Kode</th><th>Info</th><th>Qty Size</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td><strong>{{ $order->kode }}</strong></td>
                            <td>
                                <div>{{ $order->tanggal->format('d/m/Y') }}</div>
                                <small>{{ $order->style }} / {{ $order->warna }}</small>
                                <small>Penjahit: {{ $order->penjahit ?: '-' }}</small>
                                @if ($order->delivered_at)
                                    <small style="color: var(--success);">Terkirim: {{ $order->delivered_at->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="size-pills">
                                    @foreach (\App\Models\Order::SIZES as $size)
                                        @php $quantity = $order->sizes->where('size', $size)->first()?->quantity ?? 0; @endphp
                                        <span class="size-pill {{ $quantity === 0 ? 'muted' : '' }}">{{ $size }}: {{ $quantity }}</span>
                                    @endforeach
                                    <span class="size-pill" style="background: rgba(56, 189, 248, 0.12); border-color: rgba(56, 189, 248, 0.3); color: var(--accent-2);">Jml: {{ $order->total }}</span>
                                </div>
                            </td>
                            <td><span class="status-badge status-{{ $order->status }}">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span></td>
                            <td class="action-cell-wide">
                                @if ($routeName === 'packing.index')
                                    <form method="POST" action="{{ route('packing.ship', $order) }}" class="ship-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="date" name="delivered_at">
                                        <button type="submit" class="btn-small">Kirim</button>
                                    </form>
                                @elseif ($routeName === 'jahit.index' && $order->status === \App\Models\Order::STATUS_POTONGAN_SELESAI)
                                    <form method="POST" action="{{ route('jahit.assign', $order) }}" class="assign-form">
                                        @csrf @method('PATCH')
                                        <div class="penjahit-select-wrap">
                                            <div class="penjahit-sel-row">
                                                <div class="select-rel">
                                                    <select name="penjahit" id="penjahit_{{ $order->id }}" required>
                                                        <option value="" disabled selected>-- Pilih penjahit --</option>
                                                        @foreach ($penjahitOptions ?? [] as $opt)
                                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="sel-arrow">&#8964;</span>
                                                </div>
                                                <button type="submit" class="btn-small">Assign</button>
                                            </div>
                                            <div class="penjahit-add-row">
                                                <input type="text" id="new_penjahit_{{ $order->id }}" placeholder="Penjahit baru..." class="penjahit-new-input">
                                                <button type="button" class="btn-add-penjahit" onclick="addPenjahit('{{ $order->id }}')">+ Tambah</button>
                                            </div>
                                        </div>
                                    </form>
                                @elseif ($routeName === 'jahit.index' && $order->status === \App\Models\Order::STATUS_DALAM_JAHIT)
                                    <form method="POST" action="{{ route('jahit.finish', $order) }}">@csrf @method('PATCH')<button type="submit" class="btn-small">Selesai Jahit</button></form>
                                @elseif ($routeName === 'qc-awal.index')
                                    <form method="POST" action="{{ route('qc-awal.split', $order) }}" class="qc-grid-form">
                                        @csrf
                                        <div class="qc-grid-head"><span></span><span>S</span><span>M</span><span>L</span><span>XL</span><span>XXL</span></div>
                                        @foreach (['permak' => 'Permak', 'cuci' => 'Cuci'] as $field => $label)
                                            <div class="qc-grid-row"><strong>{{ $label }}</strong>
                                                @foreach (\App\Models\Order::SIZES as $size)
                                                    @php $quantity = $order->sizes->where('size', $size)->first()?->quantity ?? 0; @endphp
                                                    <input type="number" name="{{ $field }}[{{ $size }}]" value="0" min="0" max="{{ $quantity }}" {{ $quantity === 0 ? 'disabled' : '' }}>
                                                @endforeach
                                            </div>
                                        @endforeach
                                        <button type="submit" class="btn-small">Simpan QC</button>
                                    </form>
                                @elseif ($routeName === 'permak.index')
                                    <form method="POST" action="{{ route('permak.qc-ulang', $order) }}">@csrf @method('PATCH')<button type="submit" class="btn-small">Permak Selesai</button></form>
                                @elseif ($routeName === 'cuci.index')
                                    <form method="POST" action="{{ route('cuci.qc-ulang', $order) }}">@csrf @method('PATCH')<button type="submit" class="btn-small">Cuci Selesai</button></form>
                                @elseif ($routeName === 'qc-ulang.index')
                                    <form method="POST" action="{{ route('qc-ulang.pass', $order) }}">@csrf @method('PATCH')<button type="submit" class="btn-small">Lolos QC Ulang</button></form>
                                @else
                                    <a href="{{ route('orders.show', $order) }}" class="link-action">Detail</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $orders->links() }}</div>
    @endif

    <style>
        .assign-form { width: 100%; }

        .penjahit-select-wrap {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .penjahit-sel-row {
            display: grid;
            gap: 6px;
            grid-template-columns: 1fr auto;
            align-items: center;
        }

        .select-rel { position: relative; }

        .select-rel select {
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
            padding-right: 32px;
            font-size: 0.82rem;
        }

        .sel-arrow {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
            font-size: 1.1rem;
            line-height: 1;
        }

        .penjahit-add-row {
            display: grid;
            gap: 6px;
            grid-template-columns: 1fr auto;
            align-items: center;
        }

        .penjahit-new-input { font-size: 0.78rem !important; padding: 7px 10px !important; }

        .btn-add-penjahit {
            flex-shrink: 0;
            padding: 7px 11px;
            font-size: 0.74rem;
            font-weight: 700;
            color: var(--accent);
            background: rgba(124, 58, 237, 0.12);
            border: 1px solid rgba(124, 58, 237, 0.35);
            border-radius: 9px;
            cursor: pointer;
            transition: background 160ms ease;
            white-space: nowrap;
        }

        .btn-add-penjahit:hover {
            background: rgba(124, 58, 237, 0.22);
        }
    </style>

    <script>
        function addPenjahit(orderId) {
            const input  = document.getElementById('new_penjahit_' + orderId);
            const select = document.getElementById('penjahit_' + orderId);
            const value  = input.value.trim();

            if (!value) { input.focus(); return; }

            // Cek duplikat (case-insensitive)
            const exists = Array.from(select.options)
                .some(o => o.value.toLowerCase() === value.toLowerCase());

            if (exists) {
                select.value = Array.from(select.options)
                    .find(o => o.value.toLowerCase() === value.toLowerCase()).value;
            } else {
                const opt = new Option(value, value, true, true);
                select.add(opt);
                select.value = value;
            }

            input.value = '';
        }

        // Enter di input juga trigger tambah
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[id^="new_penjahit_"]').forEach(function (el) {
                el.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const id = el.id.replace('new_penjahit_', '');
                        addPenjahit(id);
                    }
                });
            });
        });
    </script>
@endsection
