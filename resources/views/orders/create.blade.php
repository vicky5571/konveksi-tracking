@extends('layouts.app')

@section('title', 'Input PO Baru')
@section('page_title', 'Input Purchase Order')

@section('topbar_action')
    <a href="{{ route('orders.index') }}" class="btn-secondary">Kembali ke Daftar</a>
@endsection

@section('content')
    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('orders.store') }}" class="form-grid">
        @csrf

        <div class="form-group">
            <label for="kode">Kode PO</label>
            <input type="text" id="kode" name="kode" value="{{ old('kode') }}" required>
            @error('kode')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="tanggal">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            @error('tanggal')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="style">Style</label>
            <div class="select-wrapper">
                <select id="style" name="style" required>
                    <option value="" disabled {{ old('style') ? '' : 'selected' }}>-- Pilih atau ketik style --</option>
                    @foreach ($styleOptions as $opt)
                        <option value="{{ $opt }}" {{ old('style') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                    @if (old('style') && !$styleOptions->contains(old('style')))
                        <option value="{{ old('style') }}" selected>{{ old('style') }}</option>
                    @endif
                </select>
                <span class="select-arrow">&#8964;</span>
            </div>
            <div class="custom-input-row">
                <input type="text" id="style_custom" placeholder="Atau masukkan style baru..." class="custom-input">
                <button type="button" class="btn-add-custom" onclick="addCustomOption('style', 'style_custom')">+ Tambah</button>
            </div>
            @error('style')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="warna">Warna</label>
            <div class="select-wrapper">
                <select id="warna" name="warna" required>
                    <option value="" disabled {{ old('warna') ? '' : 'selected' }}>-- Pilih atau ketik warna --</option>
                    @foreach ($warnaOptions as $opt)
                        <option value="{{ $opt }}" {{ old('warna') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                    @if (old('warna') && !$warnaOptions->contains(old('warna')))
                        <option value="{{ old('warna') }}" selected>{{ old('warna') }}</option>
                    @endif
                </select>
                <span class="select-arrow">&#8964;</span>
            </div>
            <div class="custom-input-row">
                <input type="text" id="warna_custom" placeholder="Atau masukkan warna baru..." class="custom-input">
                <button type="button" class="btn-add-custom" onclick="addCustomOption('warna', 'warna_custom')">+ Tambah</button>
            </div>
            @error('warna')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group-full">
            <label>Rincian Ukuran</label>
            <div class="size-grid">
                @foreach ($sizes as $size)
                    <div class="size-input">
                        <label for="size_{{ $size }}">{{ $size }}</label>
                        <input type="number" 
                               id="size_{{ $size }}" 
                               name="sizes[{{ $size }}]" 
                               value="{{ old('sizes.' . $size, 0) }}" 
                               min="0">
                    </div>
                @endforeach
            </div>
            @error('sizes')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Simpan PO</button>
            <button type="reset" class="btn-secondary">Reset</button>
        </div>
    </form>

    <style>
        .alert {
            padding: 16px 20px;
            margin-bottom: 24px;
            border-radius: 14px;
            border: 1px solid;
        }

        .alert-error {
            color: var(--danger);
            background: rgba(251, 113, 133, 0.1);
            border-color: rgba(251, 113, 133, 0.3);
        }

        .form-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .form-group,
        .form-group-full {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group-full {
            grid-column: 1 / -1;
        }

        label {
            color: var(--muted);
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .error-text {
            color: var(--danger);
            font-size: 0.85rem;
        }

        .size-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        }

        .size-input {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .size-input label {
            font-size: 0.95rem;
            text-align: center;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            grid-column: 1 / -1;
            margin-top: 12px;
        }

        .btn-primary,
        .btn-secondary {
            padding: 14px 28px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: all 180ms ease;
        }

        .btn-primary {
            color: white;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            box-shadow: 0 12px 24px rgba(124, 58, 237, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(124, 58, 237, 0.4);
        }

        .btn-secondary {
            color: var(--text);
            background: var(--surface-soft);
            border: 1px solid var(--line);
        }

        .btn-secondary:hover {
            background: var(--surface-strong);
        }

        /* ── Dropdown select styling ── */
        .select-wrapper {
            position: relative;
        }

        .select-wrapper select {
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
            padding-right: 40px;
        }

        .select-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: var(--muted);
            pointer-events: none;
            line-height: 1;
        }

        /* ── Custom input row ── */
        .custom-input-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .custom-input {
            flex: 1;
            font-size: 0.85rem !important;
            padding: 9px 12px !important;
        }

        .btn-add-custom {
            flex-shrink: 0;
            padding: 9px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--accent);
            background: rgba(124, 58, 237, 0.12);
            border: 1px solid rgba(124, 58, 237, 0.35);
            border-radius: 10px;
            cursor: pointer;
            transition: all 160ms ease;
            white-space: nowrap;
        }

        .btn-add-custom:hover {
            background: rgba(124, 58, 237, 0.22);
            border-color: rgba(124, 58, 237, 0.6);
        }
    </style>

    <script>
        function addCustomOption(selectId, inputId) {
            const select = document.getElementById(selectId);
            const input  = document.getElementById(inputId);
            const value  = input.value.trim();

            if (!value) {
                input.focus();
                return;
            }

            // Cek duplikat
            const exists = Array.from(select.options).some(o => o.value.toLowerCase() === value.toLowerCase());
            if (exists) {
                // Pilih opsi yang sudah ada
                select.value = Array.from(select.options).find(o => o.value.toLowerCase() === value.toLowerCase()).value;
                input.value = '';
                return;
            }

            const option = new Option(value, value, true, true);
            select.add(option);
            select.value = value;
            input.value  = '';
        }

        // Tekan Enter di input custom juga trigger tambah
        document.addEventListener('DOMContentLoaded', function () {
            ['style_custom', 'warna_custom'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const selectId = id.replace('_custom', '');
                            addCustomOption(selectId, id);
                        }
                    });
                }
            });
        });
    </script>
@endsection
