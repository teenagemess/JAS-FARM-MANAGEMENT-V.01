<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Daftar Domba') }}
            </h2>
            <a href="{{ route('sheep.create') }}">
                <x-primary-button>{{ __('+ Tambah Domba') }}</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Notifikasi Sukses --}}
            @if(session('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- (1) Grid Container untuk Kartu --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                {{-- (2) Loop Data Domba --}}
                @forelse ($sheep as $s)
                    <a href="{{ route('sheep.show', $s) }}" class="block">
                        <div class="flex flex-col h-full overflow-hidden transition-transform transform bg-white shadow-sm sm:rounded-lg hover:scale-105">

                            {{-- Kontainer Gambar --}}
                            <div class="relative">
                                <img
                                    class="object-cover w-full h-56"
                                    {{-- (3) PERBAIKAN: Mengganti '::' menjadi '.' (titik) --}}
                                    src="{{ $s->photo_path ? asset('storage/' . $s->photo_path) : 'https://placehold.co/600x400/e2e8f0/9ca3af?text=' . urlencode($s->tag_number) }}"
                                    alt="Foto Domba {{ $s->tag_number }}"
                                    onerror="this.onerror=null; this.src='httpshttps://placehold.co/600x400/e2e8f0/9ca3af?text=Image+Error';"
                                >

                                {{-- (5) Badge Bibit Unggul (Jika is_pedigree = true) --}}
                                @if($s->is_pedigree)
                                    <span class="absolute px-3 py-1 text-xs font-bold text-yellow-900 bg-yellow-500 rounded-full shadow-md top-2 right-2">
                                        Bibit Unggul
                                    </span>
                                @endif
                            </div>

                            {{-- Konten Teks Kartu --}}
                            <div class="flex-grow p-6 text-gray-900">
                                <h3 class="mb-1 text-lg font-bold">{{ $s->tag_number }}</h3>
                                <p class="mb-2 text-sm text-gray-600">{{ $s->shelter->name ?? 'Belum ada kandang' }}</p>
                                <hr class="my-2">
                                <div class="space-y-1 text-sm text-gray-700">
                                    <p><strong>Umur:</strong> {{ $s->date_of_birth->diffForHumans(null, true) }}</p>
                                    <p><strong>Gender:</strong> {{ $s->gender }}</p>
                                    <p><strong>Tipe:</strong> {{ $s->type }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    {{-- (6) Tampilan jika data kosong --}}
                    <div class="col-span-1 py-10 text-center sm:col-span-2 lg:col-span-3 xl:col-span-4">
                        <p class="text-lg text-gray-500">Belum ada data domba yang diinput.</p>
                        <a href="{{ route('sheep.create') }}" class="inline-block mt-4">
                            <x-primary-button>{{ __('+ Input Domba Pertama Anda') }}</x-primary-button>
                        </a>
                    </div>
                @endforelse

            </div> {{-- Akhir dari Grid --}}

            {{-- (7) Link Paginasi --}}
            <div class="mt-8">
                {{ $sheep->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
