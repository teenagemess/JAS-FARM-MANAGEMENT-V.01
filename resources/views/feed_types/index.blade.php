<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Jenis Pakan & Harga') }}
            </h2>
            <a href="{{ route('feed-types.create') }}">
                <x-primary-button class="justify-center w-full sm:w-auto">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    {{ __('+ Tambah Pakan') }}
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

            {{-- SEARCH & FILTER BAR --}}
            <div class="p-4 mb-8 bg-white border border-gray-100 shadow-sm rounded-xl">
                <form method="GET" action="{{ route('feed-types.index') }}">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end">

                        {{-- Cari Nama --}}
                        <div class="flex-grow">
                            <x-input-label for="search" :value="__('Cari Jenis Pakan')" />
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <x-text-input id="search" name="search" type="text" class="w-full pl-10" placeholder="Contoh: Rumput Odot..." :value="request('search')" />
                            </div>
                        </div>

                        {{-- Filter Mitra (Hanya Admin) --}}
                        @if(Auth::user()->role !== 'mitra' && isset($partners))
                            <div class="w-full md:w-64">
                                <x-input-label for="partner_id" :value="__('Pilih Penginput Data')" />
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

                        <div>
                            <x-primary-button type="submit" class="justify-center w-full h-[42px]">
                                {{ __('Cari / Filter') }}
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- GRID JENIS PAKAN --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($feedTypes as $feed)
                    <div class="relative flex flex-col overflow-hidden transition-shadow bg-white border border-gray-100 shadow-sm rounded-xl hover:shadow-md">

                        {{-- Header Kartu --}}
                        <div class="flex flex-col px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-gray-800 truncate" title="{{ $feed->name }}">
                                    🌾 {{ $feed->name }}
                                </h3>
                                <span class="px-2 py-1 text-xs font-semibold text-gray-600 bg-gray-100 rounded-md">
                                    {{ $feed->unit }}
                                </span>
                            </div>

                            {{-- BADGE MITRA (Khusus Admin) --}}
                            @if(Auth::user()->role !== 'mitra')
                                <div class="flex items-center mt-1 text-xs font-semibold text-indigo-600">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ $feed->user->name ?? 'Unknown' }}
                                </div>
                            @endif
                        </div>

                        {{-- Body Kartu --}}
                        <div class="flex-grow p-5">
                            <div class="flex items-end justify-between mb-4">
                                <div class="text-sm text-gray-500">Harga per Unit</div>
                                <div class="text-xl font-bold text-green-600">
                                    Rp {{ number_format($feed->price_per_unit, 0, ',', '.') }}
                                </div>
                            </div>

                            {{-- Deskripsi (Opsional) --}}
                            @if($feed->description)
                                <p class="text-sm text-gray-500 line-clamp-2">
                                    {{ $feed->description }}
                                </p>
                            @else
                                <p class="text-sm italic text-gray-400">Tidak ada deskripsi.</p>
                            @endif
                        </div>

                        {{-- Footer Kartu (Aksi) --}}
                        <div class="flex items-center justify-between px-5 py-3 text-xs bg-white border-t border-gray-100">
                            <span class="text-gray-400">ID: {{ $feed->id }}</span>
                            <div class="flex space-x-3">
                                <a href="{{ route('feed-types.edit', $feed) }}" class="font-semibold text-indigo-600 transition-colors hover:text-indigo-800 hover:underline">
                                    Edit
                                </a>
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('feed-types.destroy', $feed) }}" method="POST" onsubmit="return confirm('Hapus jenis pakan {{ $feed->name }}?');" class="inline">
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
                            {{ request('search') || request('partner_id') ? 'Tidak ada data pakan yang cocok dengan filter.' : 'Belum ada jenis pakan yang ditambahkan.' }}
                        </p>
                        <a href="{{ route('feed-types.create') }}" class="inline-block mt-4">
                            <x-primary-button>{{ __('Tambah Pakan Pertama') }}</x-primary-button>
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION LINKS --}}
            <div class="mt-8">
                {{ $feedTypes->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
