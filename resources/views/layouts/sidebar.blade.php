<div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 h-screen transition-transform duration-300 transform -translate-x-full bg-white border-r border-gray-200 lg:translate-x-0 lg:static lg:inset-auto">

    {{-- LOGIC PHP: HITUNG NOTIFIKASI --}}
    @php
        $pendingReqCount = 0;
        if(Auth::check() && Auth::user()->role === 'mitra') {
            // Hitung request yang statusnya 'pending' untuk user ini
            // Menggunakan try-catch agar tidak error jika tabel belum dimigrasi
            try {
                $pendingReqCount = \App\Models\PlacementRequest::where('target_partner_id', Auth::id())
                    ->where('status', 'pending')
                    ->count();
            } catch (\Exception $e) {
                $pendingReqCount = 0;
            }
        }
    @endphp

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #e5e7eb;
            border-radius: 20px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #d1d5db;
        }

        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #e5e7eb transparent;
        }
    </style>

    <!-- HEADER: Logo & Tombol Close -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-100 shrink-0">

        <!-- Logo Group -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <img src="{{ asset('img/logo.png') }}" alt="Logo JAS Farm" class="object-contain w-10 h-10"
                onerror="this.style.display='none'">
            <span class="text-xl font-bold text-gray-800">JAS Farm</span>
        </a>

        <!-- Tombol Close (Mobile) -->
        <button @click="sidebarOpen = false"
            class="p-1 text-gray-500 transition-colors rounded-md lg:hidden hover:bg-gray-100 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Menu Utama -->
    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto custom-scrollbar">

        {{-- DASHBOARD (Semua User) --}}
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                </path>
            </svg>
            {{ __('Dashboard') }}
        </x-nav-link>

        {{-- GRUP 1: MANAJEMEN TERNAK / ASET --}}
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold tracking-wider text-gray-400 uppercase">
                {{ Auth::user()->role === 'mitra' ? 'Aset Saya' : 'Manajemen Ternak' }}
            </p>
        </div>

        {{-- Permintaan Masuk (KHUSUS MITRA) --}}
        @if (Auth::user()->role === 'mitra')
            <x-nav-link :href="route('placement-requests.index')" :active="request()->routeIs('placement-requests.*')"
                class="flex items-center justify-between px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('placement-requests.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
                <div class="flex items-center">
                    <span class="mr-3 text-xl">📥</span>
                    <span>{{ __('Permintaan Masuk') }}</span>
                </div>

                {{-- BADGE NOTIFIKASI --}}
                @if($pendingReqCount > 0)
                    <span class="flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse">
                        {{ $pendingReqCount }}
                    </span>
                @endif
            </x-nav-link>
        @endif

        {{-- Data Domba (SEMUA USER) --}}
        <x-nav-link :href="route('sheep.index')" :active="request()->routeIs('sheep.*')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('sheep.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <span class="mr-3 text-xl">🐑</span>
            {{ __('Data Domba') }}
        </x-nav-link>

        {{-- Lokasi Kandang (SEMUA USER) --}}
        <x-nav-link :href="route('shelters.index')" :active="request()->routeIs('shelters.*')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('shelters.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <span class="mr-3 text-xl">🏠</span>
            {{ __('Lokasi Kandang') }}
        </x-nav-link>

        {{-- Pakan Harian (SEMUA USER) --}}
        <x-nav-link :href="route('feeding-records.index')" :active="request()->routeIs('feeding-records.*')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('feeding-records.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <span class="mr-3 text-xl">🍽️</span>
            {{ __('Pakan Harian') }}
        </x-nav-link>

        {{-- Jenis Pakan (SEMUA USER - SUDAH DIPINDAHKAN) --}}


        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold tracking-wider text-gray-400 uppercase">Data Master</p>
        </div>

        <x-nav-link :href="route('feed-types.index')" :active="request()->routeIs('feed-types.*')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('feed-types.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <span class="mr-3 text-xl">🌾</span>
            {{ __('Jenis Pakan') }}
        </x-nav-link>

        <x-nav-link :href="route('symptoms.index')" :active="request()->routeIs('symptoms.*')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('symptoms.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <span class="mr-3 text-xl">🩺</span>
            {{ __('Data Gejala') }}
        </x-nav-link>

        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold tracking-wider text-gray-400 uppercase">Keuangan</p>
        </div>

        <x-nav-link :href="route('profit-loss.index')" :active="request()->routeIs('profit-loss.*')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('profit-loss.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <span class="mr-3 text-xl">💰</span>
            {{ __('Arus Kas') }}
        </x-nav-link>

        {{-- GRUP 2: ADMIN & STAFF AREA --}}
        @if (Auth::user()->role !== 'mitra')

            {{-- KEUANGAN (Hanya Admin) --}}

            {{-- ADMIN ONLY --}}
            @if (Auth::user()->role === 'admin')
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold tracking-wider text-gray-400 uppercase">Admin Area</p>
                </div>

                {{-- Manajemen User --}}
                <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')"
                    class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
                    <span class="mr-3 text-xl">👥</span>
                    {{ __('Manajemen User') }}
                </x-nav-link>

                {{-- Kemitraan --}}
                <x-nav-link :href="route('partners.index')" :active="request()->routeIs('partners.*')"
                    class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('partners.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
                    <span class="mr-3 text-xl">🤝</span>
                    {{ __('Data Mitra') }}
                </x-nav-link>
            @endif

        @endif
    </nav>

    <!-- FOOTER: User Profile & Logout -->
    <div class="p-4 border-t border-gray-200 bg-gray-50 shrink-0">
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 p-2 mb-3 transition-colors rounded-md hover:bg-gray-100 group"
            title="Edit Profil">
            <div
                class="flex items-center justify-center w-8 h-8 font-bold text-indigo-600 transition-colors bg-indigo-100 rounded-full group-hover:bg-indigo-200">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-medium text-gray-900 truncate transition-colors group-hover:text-indigo-700">
                    {{ Auth::user()->name }}</p>
                <p class="w-32 text-xs text-gray-500 uppercase truncate">{{ Auth::user()->role }}</p>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center justify-center w-full px-4 py-2 text-sm text-red-600 transition-colors rounded-md bg-red-50 hover:bg-red-100">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</div>
