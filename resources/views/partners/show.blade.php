<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Profil Mitra') }}
            </h2>
            <a href="{{ route('partners.index') }}">
                <x-secondary-button>Kembali ke Daftar</x-secondary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- 1. HEADER PROFIL & KONTAK --}}
            <div class="overflow-hidden bg-white border border-gray-100 shadow-sm sm:rounded-lg">
                <div class="p-6 md:flex md:items-center md:justify-between">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-20 h-20 text-3xl font-bold text-white bg-indigo-600 rounded-full shadow-md">
                            {{ substr($partner->name, 0, 1) }}
                        </div>
                        <div class="ml-6">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $partner->name }}</h3>
                            <div class="flex flex-col mt-1 text-sm text-gray-500 gap-y-1">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    {{ $partner->email }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $partner->address ?? 'Alamat belum diisi' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex mt-6 space-x-3 md:mt-0">
                        @if($partner->phone)
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $partner->phone)) }}" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-bold text-white transition-colors bg-green-500 rounded-lg shadow-sm hover:bg-green-600">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                WhatsApp
                            </a>
                        @else
                            <button disabled class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                No HP Tidak Tersedia
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. STATISTIK RINGKAS (GRID) --}}
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                {{-- Total --}}
                <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Total Populasi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }} <span class="text-sm font-normal">Ekor</span></p>
                </div>

                {{-- Gender Split --}}
                <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Jantan / Betina</p>
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-bold text-blue-600">{{ $stats['male'] }} <span class="text-xs text-gray-400">J</span></span>
                        <span class="text-gray-300">|</span>
                        <span class="text-lg font-bold text-pink-600">{{ $stats['female'] }} <span class="text-xs text-gray-400">B</span></span>
                    </div>
                </div>

                {{-- Kesehatan --}}
                <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Status Kesehatan</p>
                    @if($stats['sick'] > 0)
                        <p class="text-2xl font-bold text-red-600">{{ $stats['sick'] }} <span class="text-sm font-normal text-red-400">Sakit</span></p>
                    @else
                        <p class="text-lg font-bold text-green-600">Semua Sehat</p>
                    @endif
                </div>

                {{-- Kapasitas Kandang --}}
                <div class="p-4 bg-white border border-gray-100 rounded-lg shadow-sm">
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Okupansi Kandang</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-gray-800">{{ $stats['capacity_used'] }}</span>
                        <span class="text-sm text-gray-400">/ {{ $stats['total_capacity'] }}</span>
                    </div>
                    {{-- Progress Bar Kecil --}}
                    <div class="w-full h-1.5 mt-2 bg-gray-200 rounded-full overflow-hidden">
                        @php $percent = $stats['total_capacity'] > 0 ? ($stats['capacity_used'] / $stats['total_capacity']) * 100 : 0; @endphp
                        <div class="h-full {{ $percent > 90 ? 'bg-red-500' : 'bg-green-500' }}" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- KOLOM KIRI: DAFTAR KANDANG (1/3 Lebar) --}}
                <div class="space-y-6 lg:col-span-1">
                    <div class="overflow-hidden bg-white border border-gray-100 shadow-sm sm:rounded-lg">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-sm font-bold text-gray-700 uppercase">Daftar Kandang Mitra</h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @forelse($shelters as $shelter)
                                <div class="flex items-center justify-between p-4">
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $shelter->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $shelter->location ?? 'Lokasi -' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold {{ $shelter->sheep_count >= $shelter->capacity ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $shelter->sheep_count }} / {{ $shelter->capacity }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="p-4 text-sm italic text-center text-gray-500">Belum ada data kandang.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: DAFTAR DOMBA (2/3 Lebar) --}}
                <div class="lg:col-span-2">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Inventaris Domba</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($sheep as $s)
                            <a href="{{ route('sheep.show', $s) }}" class="block group">
                                <div class="flex flex-col h-full overflow-hidden transition-all bg-white border border-gray-100 shadow-sm sm:rounded-lg hover:shadow-md group-hover:border-indigo-200">
                                    {{-- Gambar Kecil --}}
                                    <div class="relative h-32 bg-gray-100">
                                        <img
                                            class="object-cover w-full h-full"
                                            src="{{ $s->photo_path ? asset('storage/' . $s->photo_path) : 'https://placehold.co/400x300/e2e8f0/9ca3af?text=' . urlencode($s->tag_number) }}"
                                            alt="Foto Domba"
                                        >
                                        @if($s->is_pedigree)
                                            <span class="absolute top-1 right-1 bg-yellow-400 text-yellow-900 text-[9px] font-bold px-1.5 py-0.5 rounded shadow">UNGGUL</span>
                                        @endif
                                    </div>

                                    <div class="p-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-bold text-gray-900 group-hover:text-indigo-600">{{ $s->tag_number }}</h4>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded font-bold {{ $s->gender == 'Jantan' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                                {{ substr($s->gender, 0, 1) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ $s->shelter->name ?? 'Tanpa Kandang' }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="py-10 text-center border-2 border-gray-200 border-dashed rounded-lg col-span-full">
                                <p class="text-gray-500">Mitra ini belum memiliki domba.</p>
                                <a href="{{ route('sheep.create') }}" class="inline-block mt-2 text-sm font-bold text-indigo-600 hover:underline">+ Tambah Domba</a>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $sheep->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
