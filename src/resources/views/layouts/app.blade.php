<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HMPay') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        x-data="{ open: false }"
        @@livewire:navigated.window="open = false"
        class="font-sans antialiased bg-gray-100"
    >
        {{-- Backdrop mobile --}}
        <div
            x-show="open"
            x-cloak
            x-transition.opacity
            @click="open = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        ></div>

        <div class="flex h-screen overflow-hidden">

            {{-- Sidebar wrapper: overlay fixo no mobile, estático no desktop --}}
            <div
                class="fixed inset-y-0 left-0 z-50 w-64 transition-transform duration-300 ease-in-out lg:static lg:z-auto lg:translate-x-0"
                :class="open ? 'translate-x-0' : '-translate-x-full'"
            >
                <livewire:layout.navigation />
            </div>

            <div class="flex flex-col flex-1 overflow-hidden min-w-0">

                {{-- Header mobile --}}
                <header class="flex-shrink-0 h-14 bg-white border-b border-gray-200 flex items-center gap-3 px-4 lg:hidden">
                    <button
                        @click="open = !open"
                        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                        aria-label="Abrir menu"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="text-lg font-bold text-gray-900">HMPay</span>
                </header>

                <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                    {{ $slot }}
                </main>

            </div>
        </div>
    </body>
</html>
