<nav
    class="sidiko-nav"
    x-data="{ open: false, userOpen: false }"
>

    <div class="sidiko-nav-container">

        {{-- =====================================================
             BRAND
        ====================================================== --}}
        <div class="sidiko-nav-brand">

            <a
                href="{{ route('dashboard') }}"
                class="sidiko-brand"
            >
                <div class="sidiko-brand-logo">
                    S
                </div>

                <div class="sidiko-brand-text">
                    <strong>SIDIKO</strong>
                    <span>SaaS</span>
                </div>
            </a>

        </div>


        {{-- =====================================================
             DESKTOP NAVIGATION
        ====================================================== --}}
        <div class="sidiko-nav-links">

            <a
                href="{{ route('dashboard') }}"
                class="sidiko-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >
                Dashboard
            </a>

        </div>


        {{-- =====================================================
             USER
        ====================================================== --}}
        <div class="sidiko-nav-user">

            <button
                type="button"
                class="sidiko-user-button"
                @click="userOpen = !userOpen"
            >

                <div class="sidiko-avatar sidiko-avatar-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>

                <div class="sidiko-user-info">

                    <strong>
                        {{ Auth::user()->name }}
                    </strong>

                    <span>
                        {{ Auth::user()->email }}
                    </span>

                </div>

                <svg
                    class="sidiko-user-chevron"
                    width="16"
                    height="16"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 0l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>

            </button>


            {{-- USER MENU --}}
            <div
                x-show="userOpen"
                x-transition
                @click.outside="userOpen = false"
                class="sidiko-user-menu"
                style="display: none;"
            >

                <div class="sidiko-user-menu-header">

                    <div class="sidiko-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                    <div>
                        <strong>
                            {{ Auth::user()->name }}
                        </strong>

                        <span>
                            {{ Auth::user()->email }}
                        </span>
                    </div>

                </div>


                <div class="sidiko-divider"></div>


                <a
                    href="{{ route('profile.edit') }}"
                    class="sidiko-user-menu-item"
                >
                    Profil
                </a>


                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="sidiko-user-menu-item sidiko-user-menu-danger"
                    >
                        Keluar
                    </button>

                </form>

            </div>

        </div>


        {{-- =====================================================
             MOBILE BUTTON
        ====================================================== --}}
        <button
            type="button"
            class="sidiko-mobile-menu-button"
            @click="open = !open"
            aria-label="Buka menu"
        >

            <svg
                x-show="!open"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="M4 6h16"/>
                <path d="M4 12h16"/>
                <path d="M4 18h16"/>
            </svg>

            <svg
                x-show="open"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                style="display: none;"
            >
                <path d="M6 6l12 12"/>
                <path d="M18 6L6 18"/>
            </svg>

        </button>

    </div>


    {{-- =====================================================
         MOBILE MENU
    ====================================================== --}}
    <div
        x-show="open"
        x-transition
        class="sidiko-mobile-menu"
        style="display: none;"
    >

        <a
            href="{{ route('dashboard') }}"
            class="sidiko-mobile-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
        >
            Dashboard
        </a>


        <div class="sidiko-mobile-user">

            <div class="sidiko-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>

            <div>

                <strong>
                    {{ Auth::user()->name }}
                </strong>

                <span>
                    {{ Auth::user()->email }}
                </span>

            </div>

        </div>


        <a
            href="{{ route('profile.edit') }}"
            class="sidiko-mobile-nav-link"
        >
            Profil
        </a>


        <form
            method="POST"
            action="{{ route('logout') }}"
        >
            @csrf

            <button
                type="submit"
                class="sidiko-mobile-nav-link sidiko-mobile-danger"
            >
                Keluar
            </button>
        </form>

    </div>

</nav>