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
             AUTH PANEL
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
                        Lupa password?
                    </h2>

                    <p>
                        Masukkan email Anda dan kami akan mengirimkan
                        tautan untuk mengatur ulang password.
                    </p>

                </div>


                {{-- SESSION STATUS --}}

                @if (session('status'))

                    <div class="status-message">
                        {{ session('status') }}
                    </div>

                @endif


                {{-- FORM --}}

                <form
                    method="POST"
                    action="{{ route('password.email') }}"
                >

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
                            autocomplete="email"
                        >

                        @error('email')

                            <div class="error-message">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- SUBMIT --}}

                    <button
                        type="submit"
                        class="login-button"
                    >
                        Kirim Link Reset Password
                    </button>

                </form>


                {{-- BACK TO LOGIN --}}

                @if (Route::has('login'))

                    <div
                        style="
                            margin-top: 20px;
                            text-align: center;
                        "
                    >

                        <a
                            href="{{ route('login') }}"
                            class="forgot"
                        >
                            ← Kembali ke halaman login
                        </a>

                    </div>

                @endif


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