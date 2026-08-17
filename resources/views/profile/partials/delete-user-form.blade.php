<section>

    <div class="sidiko-flex sidiko-flex-column sidiko-gap-sm">

        <div>
            <h2 class="sidiko-text-lg sidiko-font-semibold">
                Hapus Akun
            </h2>

            <p class="sidiko-text-sm sidiko-text-muted">
                Setelah akun dihapus, seluruh data dan sumber daya yang terkait
                akan dihapus secara permanen.
            </p>
        </div>

        <div>
            <button
                type="button"
                class="sidiko-btn sidiko-btn-danger"
                x-data
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            >
                Hapus Akun
            </button>
        </div>

    </div>


    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable
    >

        <form
            method="post"
            action="{{ route('profile.destroy') }}"
            class="sidiko-card-body"
        >

            @csrf
            @method('delete')

            <div class="sidiko-flex sidiko-flex-column sidiko-gap-md">

                <div>
                    <h2 class="sidiko-text-lg sidiko-font-semibold">
                        Konfirmasi Hapus Akun
                    </h2>

                    <p class="sidiko-text-sm sidiko-text-muted">
                        Tindakan ini permanen dan tidak dapat dibatalkan.
                    </p>
                </div>

                <div class="sidiko-alert sidiko-alert-danger">

                    <div>
                        <p class="sidiko-alert-title">
                            Perhatian
                        </p>

                        <p class="sidiko-alert-message">
                            Seluruh data yang terkait dengan akun ini akan
                            dihapus secara permanen.
                        </p>
                    </div>

                </div>

                <div class="sidiko-form-group">

                    <label
                        for="password"
                        class="sidiko-label"
                    >
                        Password
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="sidiko-input"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                    >

                    @if ($errors->userDeletion->has('password'))
                        <p class="sidiko-error">
                            {{ $errors->userDeletion->first('password') }}
                        </p>
                    @endif

                </div>

                <div class="sidiko-form-actions">

                    <button
                        type="button"
                        class="sidiko-btn sidiko-btn-secondary"
                        x-on:click="$dispatch('close')"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="sidiko-btn sidiko-btn-danger"
                    >
                        Hapus Akun Permanen
                    </button>

                </div>

            </div>

        </form>

    </x-modal>

</section>