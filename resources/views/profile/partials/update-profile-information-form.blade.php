<section>

    <form
        id="send-verification"
        method="post"
        action="{{ route('verification.send') }}"
    >
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
    >

        @csrf
        @method('patch')

        <div class="sidiko-form-group">

            <label
                for="name"
                class="sidiko-label"
            >
                Nama
            </label>

            <input
                id="name"
                name="name"
                type="text"
                class="sidiko-input"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
            >

            @error('name')
                <p class="sidiko-error">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <div class="sidiko-form-group">

            <label
                for="email"
                class="sidiko-label"
            >
                Email
            </label>

            <input
                id="email"
                name="email"
                type="email"
                class="sidiko-input"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            >

            @error('email')
                <p class="sidiko-error">
                    {{ $message }}
                </p>
            @enderror

            @if (
                $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
                && ! $user->hasVerifiedEmail()
            )

                <div class="sidiko-alert sidiko-alert-warning">

                    <div>
                        <p class="sidiko-alert-title">
                            Email belum diverifikasi
                        </p>

                        <p class="sidiko-alert-message">
                            Silakan verifikasi alamat email Anda.
                        </p>

                        <button
                            form="send-verification"
                            type="submit"
                            class="sidiko-btn sidiko-btn-ghost sidiko-btn-sm"
                        >
                            Kirim ulang email verifikasi
                        </button>
                    </div>

                </div>

                @if (session('status') === 'verification-link-sent')

                    <div class="sidiko-alert sidiko-alert-success">
                        Email verifikasi baru telah dikirim.
                    </div>

                @endif

            @endif

        </div>

        <div class="sidiko-form-actions sidiko-form-actions-start">

            <button
                type="submit"
                class="sidiko-btn sidiko-btn-primary"
            >
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')

                <span class="sidiko-badge sidiko-badge-success">
                    Tersimpan
                </span>

            @endif

        </div>

    </form>

</section>