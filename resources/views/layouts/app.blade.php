<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">

        {{-- Wrapper Utama dengan x-data untuk kontrol Sidebar --}}
        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden bg-gray-100">

            {{-- 1. INCLUDE SIDEBAR --}}
            @include('layouts.sidebar')

            {{-- 2. KONTEN UTAMA (Kanan) --}}
            <div class="flex flex-col flex-1 overflow-hidden">

                {{-- Header Mobile (Hanya muncul di layar kecil untuk buka sidebar) --}}
                <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200 lg:hidden">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <span class="ml-4 text-lg font-bold text-gray-800">JAS Farm</span>
                    </div>
                </header>

                {{-- Header Halaman (Slot dari View) --}}
                @if (isset($header))
                    <header class="z-10 bg-white shadow-sm">
                        <div class="px-6 py-4 mx-auto">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                {{-- Konten Halaman Scrollable --}}
                <main class="flex-1 p-6 overflow-x-hidden overflow-y-auto bg-gray-100">
                    {{ $slot }}
                </main>
            </div>

            {{-- Overlay Gelap untuk Mobile saat Sidebar Terbuka --}}
            <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"></div>
        </div>
    </body>
</html>
