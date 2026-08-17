<form
    method="post"
    action="{{ route('password.update') }}"
>

    @csrf
    @method('put')


    {{-- PASSWORD SAAT INI --}}

    <div class="sidiko-form-group">

        <label
            for="update_password_current_password"
            class="sidiko-label"
        >
            Password Saat Ini
        </label>

        <input
            id="update_password_current_password"
            name="current_password"
            type="password"
            class="sidiko-input"
            autocomplete="current-password"
        >

        @if ($errors->updatePassword->has('current_password'))

            <p class="sidiko-error">
                {{ $errors->updatePassword->first('current_password') }}
            </p>

        @endif

    </div>


    {{-- PASSWORD BARU --}}

    <div class="sidiko-form-group">

        <label
            for="update_password_password"
            class="sidiko-label"
        >
            Password Baru
        </label>

        <input
            id="update_password_password"
            name="password"
            type="password"
            class="sidiko-input"
            autocomplete="new-password"
        >

        @if ($errors->updatePassword->has('password'))

            <p class="sidiko-error">
                {{ $errors->updatePassword->first('password') }}
            </p>

        @endif

    </div>


    {{-- KONFIRMASI PASSWORD --}}

    <div class="sidiko-form-group">

        <label
            for="update_password_password_confirmation"
            class="sidiko-label"
        >
            Konfirmasi Password Baru
        </label>

        <input
            id="update_password_password_confirmation"
            name="password_confirmation"
            type="password"
            class="sidiko-input"
            autocomplete="new-password"
        >

        @if ($errors->updatePassword->has('password_confirmation'))

            <p class="sidiko-error">
                {{ $errors->updatePassword->first('password_confirmation') }}
            </p>

        @endif

    </div>


    {{-- ACTION --}}

    <div class="sidiko-form-actions sidiko-form-actions-start">

        <button
            type="submit"
            class="sidiko-btn sidiko-btn-primary"
        >
            Simpan Password
        </button>

        @if (session('status') === 'password-updated')

            <span
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="sidiko-badge sidiko-badge-success"
            >
                Password berhasil diperbarui
            </span>

        @endif

    </div>

</form>