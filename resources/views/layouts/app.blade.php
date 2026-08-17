<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', config('app.name', 'SIDIKO SaaS'))
    </title>

    {{-- SIDIKO GLOBAL UI --}}
    @vite('resources/css/sidiko.css')
    @vite('resources/js/app.js')
</head>

<body>

    <div class="sidiko-app">

        {{-- HORIZONTAL NAVIGATION --}}
        @include('layouts.navigation')

        <div class="sidiko-layout">

            {{-- VERTICAL SIDEBAR --}}
            @include('layouts.partials.sidebar')

            {{-- MAIN CONTENT --}}
            <div class="sidiko-layout-content">

                @isset($header)
                    <header class="sidiko-page-header">
                        {{ $header }}
                    </header>
                @endisset

                <main class="sidiko-main">
                    {{ $slot }}
                </main>

            </div>

        </div>

    </div>

</body>

</html>