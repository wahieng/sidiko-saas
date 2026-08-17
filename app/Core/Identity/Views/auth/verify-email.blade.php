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


        {{-- VERIFICATION PANEL --}}
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
                        Verifikasi email
                    </h2>

                    <p>
                        Satu langkah lagi untuk mengaktifkan akun Anda.
                    </p>

                </div>


                {{-- INFORMATION --}}
                <div class="status-message">

                    Terima kasih telah mendaftar.

                    Sebelum melanjutkan, silakan verifikasi alamat
                    email Anda dengan mengklik tautan yang telah kami
                    kirimkan ke email Anda.

                    Jika Anda belum menerima email tersebut,
                    kami dapat mengirimkan tautan baru.

                </div>


                {{-- SUCCESS --}}
                @if (session('status') == 'verification-link-sent')

                    <div class="status-message">

                        Tautan verifikasi baru telah dikirim ke alamat
                        email yang Anda gunakan saat pendaftaran.

                    </div>

                @endif


                {{-- RESEND --}}
                <form
                    method="POST"
                    action="{{ route('verification.send') }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="login-button"
                    >
                        Kirim Ulang Email Verifikasi
                    </button>

                </form>


                {{-- LOGOUT --}}
                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    style="margin-top: 12px;"
                >

                    @csrf

                    <button
                        type="submit"
                        class="forgot"
                        style="
                            width: 100%;
                            border: 0;
                            background: transparent;
                            padding: 10px;
                        "
                    >
                        Keluar
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