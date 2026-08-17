<aside
    class="sidiko-sidebar"
    x-data="{ open: {} }"
>

    {{-- =====================================================
         MENU
    ====================================================== --}}
    <div class="sidiko-sidebar-menu">

        {{-- DASHBOARD --}}
        <a
            href="{{ route('dashboard') }}"
            class="sidiko-sidebar-link
                {{ request()->routeIs('dashboard') ? 'active' : '' }}"
        >

            <span class="sidiko-sidebar-icon">
                <svg
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                </svg>
            </span>

            <span>
                Dashboard
            </span>

        </a>


        {{-- TENANT --}}
        <div class="sidiko-sidebar-group">

            <div class="sidiko-sidebar-title">
                TENANT
            </div>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">🏫</span>
                <span>Sekolah</span>
            </a>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">👥</span>
                <span>Pengguna</span>
            </a>

        </div>


        {{-- ACCESS --}}
        <div class="sidiko-sidebar-group">

            <div class="sidiko-sidebar-title">
                ACCESS
            </div>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">👤</span>
                <span>User</span>
            </a>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">🛡</span>
                <span>Role & Permission</span>
            </a>

        </div>


        {{-- SUBSCRIPTION --}}
        <div class="sidiko-sidebar-group">

            <div class="sidiko-sidebar-title">
                SUBSCRIPTION
            </div>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">📦</span>
                <span>Paket Langganan</span>
            </a>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">🔄</span>
                <span>Langganan</span>
            </a>

        </div>


        {{-- BILLING --}}
        <div class="sidiko-sidebar-group">

            <div class="sidiko-sidebar-title">
                BILLING
            </div>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">🧾</span>
                <span>Tagihan</span>
            </a>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">💳</span>
                <span>Pembayaran</span>
            </a>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">📋</span>
                <span>Riwayat Pembayaran</span>
            </a>

        </div>


        {{-- BUSINESS --}}
        <div class="sidiko-sidebar-group">

            <div class="sidiko-sidebar-title">
                BUSINESS
            </div>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">🎓</span>
                <span>Akademik</span>
            </a>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">💰</span>
                <span>Keuangan</span>
            </a>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">📊</span>
                <span>Laporan</span>
            </a>

        </div>


        {{-- SYSTEM --}}
        <div class="sidiko-sidebar-group">

            <div class="sidiko-sidebar-title">
                SYSTEM
            </div>

            <a
                href="#"
                class="sidiko-sidebar-link"
            >
                <span class="sidiko-sidebar-icon">⚙</span>
                <span>Pengaturan</span>
            </a>

        </div>

    </div>


    {{-- =====================================================
         USER
    ====================================================== --}}
    <div class="sidiko-sidebar-footer">

        <div class="sidiko-avatar sidiko-avatar-sm">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>

        <div class="sidiko-sidebar-user-info">

            <strong>
                {{ Auth::user()->name }}
            </strong>

            <span>
                {{ Auth::user()->email }}
            </span>

        </div>

    </div>

</aside>