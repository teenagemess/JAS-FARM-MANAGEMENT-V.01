<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Manajemen Kemitraan (Plasma)') }}
            </h2>
            <a href="{{ route('users.create') }}">
                <x-primary-button>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    {{ __('Daftarkan Mitra Baru') }}
                </x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- 1. STATISTIK RINGKAS --}}
            <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="p-6 bg-white border border-indigo-100 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-indigo-600 bg-indigo-100 rounded-full">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Total Mitra Aktif</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalPartners }} <span class="text-xs font-normal text-gray-400">Orang</span></p>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border border-green-100 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-green-600 bg-green-100 rounded-full">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Populasi Plasma</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalPlasmaSheep }} <span class="text-xs font-normal text-gray-400">Ekor</span></p>
                        </div>
                    </div>
                </div>

                {{-- Slot Statistik Tambahan (Misal: Rata-rata domba per mitra) --}}
                <div class="p-6 bg-white border border-blue-100 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-blue-600 bg-blue-100 rounded-full">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Rata-rata Kepemilikan</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $totalPartners > 0 ? round($totalPlasmaSheep / $totalPartners) : 0 }} <span class="text-xs font-normal text-gray-400">Ekor/Mitra</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. SEARCH BAR --}}
            <div class="p-4 mb-8 bg-white border border-gray-100 shadow-sm rounded-xl">
                <form method="GET" action="{{ route('partners.index') }}">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center">
                        <div class="flex-grow">
                            <x-input-label for="search" :value="__('Cari Mitra')" class="sr-only" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" name="search" id="search"
                                    class="block w-full pl-10 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Cari nama, email, atau lokasi..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div>
                            <x-primary-button type="submit" class="justify-center w-full md:w-auto h-[42px]">
                                {{ __('Cari') }}
                            </x-primary-button>
                            @if(request('search'))
                                <a href="{{ route('partners.index') }}" class="inline-flex items-center justify-center px-4 py-2 ml-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 h-[42px]">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- 3. GRID DAFTAR MITRA --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($partners as $partner)
                    <div class="flex flex-col overflow-hidden transition-all duration-200 bg-white border border-gray-100 shadow-sm sm:rounded-lg hover:shadow-md hover:border-indigo-200 group">

                        {{-- Header Kartu --}}
                        <div class="flex items-start justify-between p-5 border-b border-gray-50 bg-gray-50/50">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 text-lg font-bold text-white bg-indigo-500 rounded-full shadow-sm">
                                    {{ substr($partner->name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-bold text-gray-900 line-clamp-1 group-hover:text-indigo-600">
                                        <a href="{{ route('partners.show', $partner) }}">{{ $partner->name }}</a>
                                    </h3>
                                    <p class="text-xs text-gray-500">Bergabung: {{ $partner->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            {{-- Dropdown Menu (Optional) --}}
                            {{-- Bisa ditambahkan titik tiga untuk edit/hapus cepat --}}
                        </div>

                        {{-- Body Kartu --}}
                        <div class="flex-grow p-5 space-y-3">
                            <div class="flex items-start text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="line-clamp-2">{{ $partner->address ?? 'Alamat belum diisi' }}</span>
                            </div>

                            <div class="flex items-center justify-between p-3 mt-2 rounded-lg bg-indigo-50">
                                <span class="text-xs font-semibold tracking-wide text-indigo-700 uppercase">Total Aset</span>
                                <span class="text-xl font-extrabold text-indigo-800">{{ $partner->total_sheep }} <span class="text-xs font-medium">Ekor</span></span>
                            </div>
                        </div>

                        {{-- Footer Kartu --}}
                        <div class="flex border-t border-gray-100 divide-x divide-gray-100">
                            @if($partner->phone)
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $partner->phone)) }}" target="_blank" class="flex items-center justify-center flex-1 py-3 text-sm font-medium text-green-600 transition hover:bg-green-50">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    WhatsApp
                                </a>
                            @else
                                <span class="flex items-center justify-center flex-1 py-3 text-sm text-gray-400 cursor-not-allowed bg-gray-50">
                                    No Contact
                                </span>
                            @endif

                            <a href="{{ route('partners.show', $partner) }}" class="flex items-center justify-center flex-1 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50 hover:text-indigo-600">
                                Lihat Detail
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center bg-white border-2 border-gray-300 border-dashed rounded-lg col-span-full">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p class="text-lg font-medium text-gray-500">
                            {{ request('search') ? 'Tidak ada mitra yang cocok dengan pencarian.' : 'Belum ada mitra yang terdaftar.' }}
                        </p>
                        <a href="{{ route('users.create') }}" class="inline-block mt-3 font-semibold text-indigo-600 hover:underline">
                            + Tambah Mitra Baru
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $partners->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
