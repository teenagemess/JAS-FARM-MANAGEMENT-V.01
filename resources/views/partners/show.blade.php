<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Domba Mitra: ') . $partner->name }}
            </h2>
            <a href="{{ route('partners.index') }}">
                <x-secondary-button>Kembali ke Daftar Mitra</x-secondary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- INFO MITRA --}}
            <div class="flex flex-col justify-between p-6 mb-6 bg-white border-l-4 border-indigo-500 rounded-lg shadow-sm md:flex-row md:items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $partner->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $partner->email }} • {{ $partner->phone ?? '-' }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $partner->address ?? '-' }}</p>
                </div>
                <div class="mt-4 text-right md:mt-0">
                    <p class="text-xs tracking-wide text-gray-500 uppercase">Total Aset</p>
                    <p class="text-3xl font-extrabold text-indigo-700">{{ $sheep->total() }} <span class="text-sm font-medium text-gray-500">Ekor</span></p>
                </div>
            </div>

            {{-- LIST DOMBA (Menggunakan Layout Grid yang sama dengan Sheep Index) --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse ($sheep as $s)
                    <a href="{{ route('sheep.show', $s) }}" class="block group">
                        <div class="flex flex-col h-full overflow-hidden transition-transform transform bg-white border border-gray-100 shadow-sm sm:rounded-lg hover:scale-105 group-hover:border-indigo-200">

                            {{-- Gambar --}}
                            <div class="relative">
                                <img
                                    class="object-cover w-full h-48"
                                    src="{{ $s->photo_path ? asset('storage/' . $s->photo_path) : 'https://placehold.co/600x400/e2e8f0/9ca3af?text=' . urlencode($s->tag_number) }}"
                                    alt="Foto Domba"
                                >
                                @if($s->is_pedigree)
                                    <span class="absolute top-2 right-2 bg-yellow-500 text-yellow-900 text-[10px] font-bold px-2 py-1 rounded shadow-md">UNGGUL</span>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="flex-grow p-4">
                                <h3 class="mb-1 text-lg font-bold text-gray-900">{{ $s->tag_number }}</h3>
                                <p class="mb-2 text-xs text-gray-500">
                                    {{ $s->shelter->name ?? 'Lokasi: ' . $partner->name }}
                                </p>
                                <div class="flex justify-between pt-2 mt-2 text-sm text-gray-600 border-t">
                                    <span>{{ $s->gender }}</span>
                                    <span>{{ $s->type }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="py-12 text-center bg-white border-2 border-gray-200 border-dashed rounded-lg col-span-full">
                        <p class="text-gray-500">Mitra ini belum memiliki domba titipan.</p>
                        <a href="{{ route('sheep.create') }}" class="inline-block mt-2 font-bold text-indigo-600 hover:underline">+ Titipkan Domba Baru</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $sheep->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
