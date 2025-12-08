{{--
    PERUBAHAN STRUKTUR & STYLE:
    1. Menambahkan block <style> untuk kustomisasi scrollbar minimalis.
    2. Menambahkan class 'custom-scrollbar' pada elemen <nav>.
--}}
<div x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 h-screen transition-transform duration-300 transform bg-white border-r border-gray-200 lg:translate-x-0 lg:static lg:inset-auto">

    {{-- CSS Kustom untuk Scrollbar Minimalis --}}
    <style>
        /* Untuk Webkit Browsers (Chrome, Safari, Edge) */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            /* Sangat tipis */
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
            /* Track transparan */
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #e5e7eb;
            /* Warna abu-abu muda (gray-200) */
            border-radius: 20px;
            /* Sudut membulat */
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #d1d5db;
            /* Sedikit lebih gelap saat di-hover (gray-300) */
        }

        /* Untuk Firefox */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #e5e7eb transparent;
        }
    </style>

    <!-- Logo (Fixed at Top) -->
    <div class="flex items-center justify-center"> <a href="{{ route('dashboard') }}" class="flex items-center gap-2"> <img
                src="{{ asset('img/logo.png') }}" alt="Logo JAS Farm" class="object-contain"
                style="width: 200px; height: 200px;"> {{-- <span class="text-xl font-bold text-gray-800">JAS Farm</span> --}} </a> </div>

    <!-- Menu Utama (Scrollable Area) -->
    {{-- Tambahkan class 'custom-scrollbar' di sini --}}
    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto custom-scrollbar">

        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                </path>
            </svg>
            {{ __('Dashboard') }}
        </x-nav-link>

        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold tracking-wider text-gray-400 uppercase">Manajemen Ternak</p>
        </div>

        <x-nav-link :href="route('sheep.index')" :active="request()->routeIs('sheep.*')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('sheep.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <span class="mr-3 text-xl">🐑</span>
            {{ __('Data Domba') }}
        </x-nav-link>

        <x-nav-link :href="route('shelters.index')" :active="request()->routeIs('shelters.*')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('shelters.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <span class="mr-3 text-xl">🏠</span>
            {{ __('Data Kandang') }}
        </x-nav-link>

        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold tracking-wider text-gray-400 uppercase">Operasional Harian</p>
        </div>

        <x-nav-link :href="route('feeding-records.index')" :active="request()->routeIs('feeding-records.*')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('feeding-records.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <span class="mr-3 text-xl">🍽️</span>
            {{ __('Pakan Harian') }}
        </x-nav-link>

        <x-nav-link :href="route('profit-loss.index')" :active="request()->routeIs('profit-loss.*')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('profit-loss.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <span class="mr-3 text-xl">💰</span>
            {{ __('Keuangan') }}
        </x-nav-link>

        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold tracking-wider text-gray-400 uppercase">Data Master (Kategori)</p>
        </div>

        <x-nav-link :href="route('symptoms.index')" :active="request()->routeIs('symptoms.*')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('symptoms.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <span class="mr-3 text-xl">🩺</span>
            {{ __('Data Gejala') }}
        </x-nav-link>

        <x-nav-link :href="route('feed-types.index')" :active="request()->routeIs('feed-types.*')"
            class="flex items-center px-4 py-2 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-800 {{ request()->routeIs('feed-types.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
            <span class="mr-3 text-xl">🌾</span>
            {{ __('Jenis Pakan') }}
        </x-nav-link>

    </nav>

    <!-- User Profile & Logout (Fixed at Bottom) -->
    {{-- Hapus 'absolute bottom-0' dan gunakan shrink-0 agar tetap di bawah flow --}}
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
                <p class="w-32 text-xs text-gray-500 truncate">Klik untuk Edit Profil</p>
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
