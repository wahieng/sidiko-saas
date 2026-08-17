<x-guest-layout>

    <div class="login-page">

        {{-- BRAND --}}
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


        {{-- REGISTER --}}
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
                        Buat akun SIDIKO
                    </h2>

                    <p>
                        Daftarkan akun untuk mulai menggunakan SIDIKO.
                    </p>

                </div>


                {{-- FORM --}}
                <form method="POST" action="{{ route('register') }}">

                    @csrf


                    {{-- NAME --}}
                    <div class="form-group">

                        <label
                            class="form-label"
                            for="name"
                        >
                            Nama
                        </label>

                        <input
                            id="name"
                            class="form-input"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Nama lengkap"
                            required
                            autofocus
                            autocomplete="name"
                        >

                        @error('name')
                            <div class="error-message">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


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
                            autocomplete="new-password"
                        >

                        @error('password')
                            <div class="error-message">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- CONFIRM PASSWORD --}}
                    <div class="form-group">

                        <label
                            class="form-label"
                            for="password_confirmation"
                        >
                            Konfirmasi Password
                        </label>

                        <input
                            id="password_confirmation"
                            class="form-input"
                            type="password"
                            name="password_confirmation"
                            placeholder="Ulangi password"
                            required
                            autocomplete="new-password"
                        >

                        @error('password_confirmation')
                            <div class="error-message">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- ACTION --}}
                    <div class="form-options">

                        <a
                            class="forgot"
                            href="{{ route('login') }}"
                        >
                            Sudah punya akun?
                        </a>

                    </div>

                    <button
                        type="submit"
                        class="login-button"
                    >
                        Buat Akun
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