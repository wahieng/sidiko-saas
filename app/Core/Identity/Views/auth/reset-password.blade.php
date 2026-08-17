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
                    Pulihkan akun<br>
                    <span>lebih mudah.</span>
                </h1>


                <p>
                    Atur ulang password akun SIDIKO Anda dengan aman
                    dan lanjutkan pengelolaan sekolah dalam satu sistem.
                </p>


                <div class="brand-features">

                    <div class="brand-feature">
                        <strong>Aman</strong>
                        <span>Reset password</span>
                    </div>

                    <div class="brand-feature">
                        <strong>Mudah</strong>
                        <span>Proses sederhana</span>
                    </div>

                    <div class="brand-feature">
                        <strong>Terpadu</strong>
                        <span>Satu platform</span>
                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             RESET PASSWORD PANEL
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
                        Reset password
                    </h2>

                    <p>
                        Buat password baru untuk mengamankan akun SIDIKO Anda.
                    </p>

                </div>


                {{-- FORM --}}
                <form
                    method="POST"
                    action="{{ route('password.store') }}"
                >

                    @csrf


                    {{-- TOKEN --}}
                    <input
                        type="hidden"
                        name="token"
                        value="{{ $request->route('token') }}"
                    >


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
                            value="{{ old('email', $request->email) }}"
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


                    {{-- PASSWORD BARU --}}
                    <div class="form-group">

                        <label
                            class="form-label"
                            for="password"
                        >
                            Password Baru
                        </label>

                        <input
                            id="password"
                            class="form-input"
                            type="password"
                            name="password"
                            placeholder="Masukkan password baru"
                            required
                            autocomplete="new-password"
                        >

                        @error('password')

                            <div class="error-message">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- KONFIRMASI --}}
                    <div class="form-group">

                        <label
                            class="form-label"
                            for="password_confirmation"
                        >
                            Konfirmasi Password Baru
                        </label>

                        <input
                            id="password_confirmation"
                            class="form-input"
                            type="password"
                            name="password_confirmation"
                            placeholder="Ulangi password baru"
                            required
                            autocomplete="new-password"
                        >

                        @error('password_confirmation')

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
                        Reset Password
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