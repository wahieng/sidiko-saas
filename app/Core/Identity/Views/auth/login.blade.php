<x-guest-layout>

    <div class="login-page">

        {{-- =====================================================
             BRAND
        ====================================================== --}}
        <section class="login-brand">

            <div class="shape shape-one"></div>
            <div class="shape shape-two"></div>

            <div class="brand-content">

                <div class="brand-logo">

                    <div class="brand-logo-icon">
                        S
                    </div>

                    <div class="brand-logo-text">
                        SIDIKO <span>SaaS</span>
                    </div>

                </div>

                <h1>
                    Kelola sekolah<br>
                    <span>lebih sederhana.</span>
                </h1>

                <p>
                    Platform manajemen sekolah terpadu untuk mengelola
                    data, akademik, keuangan, pengguna, dan operasional
                    sekolah dalam satu sistem.
                </p>

                <div class="brand-features">

                    <div class="brand-feature">
                        <strong>Terpadu</strong>
                        <span>Satu platform</span>
                    </div>

                    <div class="brand-feature">
                        <strong>Aman</strong>
                        <span>Kontrol akses</span>
                    </div>

                    <div class="brand-feature">
                        <strong>Modern</strong>
                        <span>Siap berkembang</span>
                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             LOGIN PANEL
        ====================================================== --}}
        <main class="login-panel">

            <div class="login-box">

                {{-- MOBILE LOGO --}}
                <div class="mobile-logo">

                    <div class="mobile-logo-icon">
                        S
                    </div>

                    <div class="mobile-logo-text">
                        SIDIKO SaaS
                    </div>

                </div>


                {{-- HEADING --}}
                <div class="login-heading">

                    <h2>
                        Selamat datang kembali
                    </h2>

                    <p>
                        Masuk ke akun SIDIKO Anda untuk melanjutkan.
                    </p>

                </div>


                {{-- SESSION STATUS --}}
                @if (session('status'))

                    <div class="status-message">
                        {{ session('status') }}
                    </div>

                @endif


                {{-- LOGIN FORM --}}
                <form method="POST" action="{{ route('login') }}">

                    @csrf


                    {{-- EMAIL --}}
                    <div class="form-group">

                        <label
                            class="form-label"
                            for="email"
                        >
                            Email
                        </label>

                        <input
                            id="email"
                            class="form-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@sekolah.sch.id"
                            required
                            autofocus
                            autocomplete="username"
                        >

                        @error('email')

                            <div class="error-message">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- PASSWORD --}}
                    <div class="form-group">

                        <label
                            class="form-label"
                            for="password"
                        >
                            Password
                        </label>

                        <input
                            id="password"
                            class="form-input"
                            type="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                            autocomplete="current-password"
                        >

                        @error('password')

                            <div class="error-message">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- OPTIONS --}}
                    <div class="form-options">

                        <label class="remember">

                            <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                            >

                            <span>
                                Ingat saya
                            </span>

                        </label>


                        @if (Route::has('password.request'))

                            <a
                                class="forgot"
                                href="{{ route('password.request') }}"
                            >
                                Lupa password?
                            </a>

                        @endif

                    </div>


                    {{-- SUBMIT --}}
                    <button
                        type="submit"
                        class="login-button"
                    >
                        Masuk ke SIDIKO
                    </button>

                </form>


                {{-- FOOTER --}}
                <div class="login-footer">

                    © {{ date('Y') }}

                    <strong>
                        SIDIKO SaaS
                    </strong>.

                    Sistem Informasi Digital Kolaboratif.

                </div>

            </div>

        </main>

    </div>

</x-guest-layout>
