<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Manajemen Kandang') }}
            </h2>
            <a href="{{ route('shelters.create') }}">
                <x-primary-button class="justify-center w-full sm:w-auto">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    {{ __('+ Tambah Kandang') }}
                </x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="flex items-center p-4 mb-6 text-green-700 border-l-4 border-green-500 rounded-md shadow-sm bg-green-50">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center p-4 mb-6 text-red-700 border-l-4 border-red-500 rounded-md shadow-sm bg-red-50">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- STATISTIK RINGKAS --}}
            <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-3">
                <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                    <p class="text-xs font-bold tracking-wider text-gray-400 uppercase">Total Kandang</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $shelters->total() }} <span class="text-sm font-normal text-gray-500">Unit</span></p>
                </div>
                <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                    <p class="text-xs font-bold tracking-wider text-gray-400 uppercase">Total Kapasitas</p>
                    <p class="text-2xl font-bold text-indigo-600">
                        {{ $shelters->sum('capacity') }}+ <span class="text-sm font-normal text-gray-500">Ekor</span>
                    </p>
                </div>
                <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                    <p class="text-xs font-bold tracking-wider text-gray-400 uppercase">Total Terisi (Hal. Ini)</p>
                    @php $totalFilled = $shelters->sum('sheep_count'); @endphp
                    <p class="text-2xl font-bold text-green-600">{{ $totalFilled }} <span class="text-sm font-normal text-gray-500">Ekor</span></p>
                </div>
            </div>

            {{-- SEARCH & FILTER BAR --}}
            <div class="p-4 mb-8 bg-white border border-gray-100 shadow-sm rounded-xl">
                <form method="GET" action="{{ route('shelters.index') }}">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end">

                        {{-- Cari Nama --}}
                        <div class="flex-grow">
                            <x-input-label for="search" :value="__('Cari Nama Kandang')" />
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <x-text-input id="search" name="search" type="text" class="w-full pl-10" placeholder="Kandang A..." :value="request('search')" />
                            </div>
                        </div>

                        {{-- Filter Mitra (Hanya Admin) --}}
                        @if(Auth::user()->role !== 'mitra' && isset($partners))
                            <div class="w-full md:w-64">
                                <x-input-label for="partner_id" :value="__('Pilih Pemilik Kandang')" />
                                <select name="partner_id" id="partner_id" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Data Saya (Admin) --</option>
                                    <option value="all" {{ request('partner_id') == 'all' ? 'selected' : '' }}>-- Semua Data (All) --</option>
                                    @foreach($partners as $p)
                                        <option value="{{ $p->id }}" {{ request('partner_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Sortir --}}
                        <div class="w-full md:w-48">
                            <x-input-label for="sort" :value="__('Urutkan')" />
                            <select name="sort" id="sort" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama (A-Z)</option>
                                <option value="fullest" {{ request('sort') == 'fullest' ? 'selected' : '' }}>Paling Penuh</option>
                                <option value="emptiest" {{ request('sort') == 'emptiest' ? 'selected' : '' }}>Paling Kosong</option>
                                <option value="capacity_high" {{ request('sort') == 'capacity_high' ? 'selected' : '' }}>Kapasitas Terbesar</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru Dibuat</option>
                            </select>
                        </div>

                        <div>
                            <x-primary-button type="submit" class="justify-center w-full h-[42px]">
                                {{ __('Cari / Filter') }}
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- GRID KANDANG --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($shelters as $shelter)
                    @php
                        $currentCount = $shelter->sheep_count;
                        $percentage = ($shelter->capacity > 0) ? ($currentCount / $shelter->capacity) * 100 : 0;

                        $colorClass = 'bg-green-500';
                        $statusText = 'Tersedia';
                        $statusBg = 'bg-green-100 text-green-800';

                        if ($percentage >= 100) {
                            $colorClass = 'bg-red-600';
                            $statusText = 'Penuh';
                            $statusBg = 'bg-red-100 text-red-800';
                        } elseif ($percentage >= 80) {
                            $colorClass = 'bg-yellow-500';
                            $statusText = 'Hampir Penuh';
                            $statusBg = 'bg-yellow-100 text-yellow-800';
                        }
                    @endphp

                    <div class="relative flex flex-col overflow-hidden transition-shadow bg-white border border-gray-100 shadow-sm rounded-xl hover:shadow-md">

                        {{-- Header Kartu --}}
                        <div class="flex flex-col px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-gray-800 truncate" title="{{ $shelter->name }}">
                                    🏠 {{ $shelter->name }}
                                </h3>
                                <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-full {{ $statusBg }}">
                                    {{ $statusText }}
                                </span>
                            </div>

                            {{-- BADGE MITRA (Khusus Admin) --}}
                            @if(Auth::user()->role !== 'mitra')
                                <div class="flex items-center mt-1 text-xs font-semibold text-indigo-600">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ $shelter->user->name ?? 'Unknown' }}
                                </div>
                            @endif
                        </div>

                        {{-- Body Kartu --}}
                        <div class="flex-grow p-5">

                            {{-- Info Kapasitas (Angka Besar) --}}
                            <div class="flex items-end justify-between mb-2">
                                <div>
                                    <span class="text-3xl font-extrabold text-gray-800">{{ $currentCount }}</span>
                                    <span class="text-sm font-medium text-gray-400">/ {{ $shelter->capacity }} Ekor</span>
                                </div>
                                <div class="mb-1 text-xs font-bold text-gray-500">
                                    {{ round($percentage) }}% Terisi
                                </div>
                            </div>

                            {{-- Visual Progress Bar --}}
                            <div class="w-full h-3 mb-4 overflow-hidden bg-gray-200 rounded-full">
                                <div class="h-full {{ $colorClass }} transition-all duration-500 ease-out" style="width: {{ $percentage > 100 ? 100 : $percentage }}%"></div>
                            </div>

                            {{-- Deskripsi --}}
                            <p class="text-sm text-gray-500 line-clamp-2 min-h-[2.5rem]">
                                {{ $shelter->description ?? 'Tidak ada deskripsi tambahan.' }}
                            </p>
                        </div>

                        {{-- Footer Kartu (Aksi) --}}
                        <div class="flex items-center justify-between px-5 py-3 text-xs bg-white border-t border-gray-100">
                            <span class="text-gray-400">ID: {{ $shelter->id }}</span>
                            <div class="flex space-x-3">
                                <a href="{{ route('shelters.edit', $shelter) }}" class="font-semibold text-indigo-600 transition-colors hover:text-indigo-800 hover:underline">
                                    Edit
                                </a>
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('shelters.destroy', $shelter) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Kandang {{ $shelter->name }}? Pastikan tidak ada domba di dalamnya.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-red-500 transition-colors hover:text-red-700 hover:underline">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-1 py-12 text-center border-2 border-gray-300 border-dashed rounded-lg md:col-span-2 lg:col-span-3 bg-gray-50">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <p class="text-lg text-gray-500">
                            {{ request('search') || request('partner_id') ? 'Tidak ada kandang yang cocok dengan filter.' : 'Belum ada kandang yang dibuat.' }}
                        </p>
                        <a href="{{ route('shelters.create') }}" class="inline-block mt-4">
                            <x-primary-button>{{ __('Buat Kandang Pertama') }}</x-primary-button>
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION LINKS --}}
            <div class="mt-8">
                {{ $shelters->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
