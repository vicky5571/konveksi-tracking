<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tracing Konveksi')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    {{-- Overlay backdrop saat drawer mobile terbuka --}}
    <div class="nav-overlay" id="navOverlay" onclick="closeNav()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-top">
            <div class="brand">
                <span class="brand-mark">TK</span>
                <div>
                    <strong>Tracing</strong>
                    <small>Konveksi</small>
                </div>
            </div>
            {{-- Tombol close di dalam sidebar (mobile) --}}
            <button class="sidebar-close" id="sidebarClose" onclick="closeNav()" aria-label="Tutup menu">&#10005;</button>
        </div>

        <nav class="sidebar-nav" aria-label="Navigasi utama">
            @php
                $menus = [
                    ['route' => 'dashboard',    'label' => 'Dashboard',  'icon' => '◉'],
                    ['route' => 'orders.index', 'label' => 'Input PO',   'icon' => '✚'],
                    ['route' => 'jahit.index',  'label' => 'Jahit',      'icon' => '✂'],
                    ['route' => 'qc-awal.index','label' => 'QC Awal',    'icon' => '◈'],
                    ['route' => 'permak.index', 'label' => 'Permak',     'icon' => '↺'],
                    ['route' => 'cuci.index',   'label' => 'Cuci',       'icon' => '◌'],
                    ['route' => 'qc-ulang.index','label'=> 'QC Ulang',   'icon' => '◈'],
                    ['route' => 'packing.index','label' => 'Packing',    'icon' => '▣'],
                    ['route' => 'tracking.index','label'=> 'Tracking',   'icon' => '⊙'],
                    ['route' => 'reports.index','label' => 'Rekap',      'icon' => '≡'],
                ];
            @endphp

            @foreach ($menus as $menu)
                <a class="nav-link {{ Route::currentRouteName() === $menu['route'] ? 'active' : '' }}"
                   href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
                   onclick="closeNav()">
                    <span class="nav-icon">{{ $menu['icon'] }}</span>
                    <span class="nav-label">{{ $menu['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </aside>

    <main class="app-shell">
        <header class="topbar">
            {{-- Hamburger button (mobile only) --}}
            <button class="hamburger" id="hamburger" onclick="openNav()" aria-label="Buka menu">
                <span></span><span></span><span></span>
            </button>

            <div class="topbar-titles">
                <p class="eyebrow">Sistem Produksi</p>
                <h1>@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="topbar-card">@yield('topbar_action')</div>
        </header>

        <section class="content-card">
            @yield('content')
        </section>
    </main>

    <script>
        function openNav() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('navOverlay').classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function closeNav() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('navOverlay').classList.remove('show');
            document.body.style.overflow = '';
        }
    </script>
</body>
</html>
