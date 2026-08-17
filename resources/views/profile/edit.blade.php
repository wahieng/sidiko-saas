<x-app-layout>

    <x-slot name="header">

        <div class="sidiko-container-lg">

            <h1 class="sidiko-text-2xl sidiko-font-bold">
                Profil
            </h1>

            <p class="sidiko-text-sm sidiko-text-muted">
                Kelola informasi akun dan keamanan akun Anda.
            </p>

        </div>

    </x-slot>


    <main>

        <div class="sidiko-container-lg" style="padding-top: 2rem; padding-bottom: 2rem;">

            {{-- =====================================================
                 INFORMASI PROFIL
            ====================================================== --}}

            <section class="sidiko-card" style="margin-bottom: 1rem;">

                <div class="sidiko-card-header">

                    <div>

                        <h2 class="sidiko-card-title">
                            Informasi Profil
                        </h2>

                        <p class="sidiko-card-description">
                            Perbarui informasi dasar akun Anda.
                        </p>

                    </div>

                </div>

                <div class="sidiko-card-body">

                    @include(
                        'profile.partials.update-profile-information-form'
                    )

                </div>

            </section>


            {{-- =====================================================
                 PASSWORD
            ====================================================== --}}

            <section class="sidiko-card" style="margin-bottom: 1rem;">

                <div class="sidiko-card-header">

                    <div>

                        <h2 class="sidiko-card-title">
                            Password
                        </h2>

                        <p class="sidiko-card-description">
                            Pastikan akun Anda menggunakan password yang kuat
                            dan aman.
                        </p>

                    </div>

                </div>

                <div class="sidiko-card-body">

                    @include(
                        'profile.partials.update-password-form'
                    )

                </div>

            </section>


            {{-- =====================================================
                 HAPUS AKUN
            ====================================================== --}}

            <section class="sidiko-card">

                <div class="sidiko-card-header">

                    <div>

                        <h2 class="sidiko-card-title">
                            Hapus Akun
                        </h2>

                        <p class="sidiko-card-description">
                            Tindakan ini akan menghapus akun secara permanen.
                        </p>

                    </div>

                </div>

                <div class="sidiko-card-body">

                    @include(
                        'profile.partials.delete-user-form'
                    )

                </div>

            </section>

        </div>

    </main>

</x-app-layout>