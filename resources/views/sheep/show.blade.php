<x-app-layout>
    <x-slot name="header">
        {{-- Menghapus dark mode --}}
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Detail Domba: ') }} {{ $sheep->tag_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Menghapus dark mode --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="mb-4 text-2xl font-bold">Eartag: {{ $sheep->tag_number }}</h3>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                        {{-- Kolom Kiri: Gambar --}}
                        <div>
                             <img
                                class="object-cover w-full rounded-lg shadow-md"
                                src="{{ $sheep->photo_path ? asset('storage/' . $sheep->photo_path) : 'https://placehold.co/600x450/e2e8f0/9ca3af?text=' . urlencode($sheep->tag_number) }}"
                                alt="Foto Domba {{ $sheep->tag_number }}"
                                onerror="this.onerror=null; this.src='https://placehold.co/600x450/e2e8f0/9ca3af?text=Image+Not+Found';"
                            >
                        </div>

                        {{-- Kolom Kanan: Info Detail --}}
                        <div class="space-y-3">
                            <p><strong>Kandang:</strong> {{ $sheep->shelter->name ?? 'N/A' }}</p>
                            <p><strong>Jenis Kelamin:</strong> {{ $sheep->gender }}</p>
                            <p><strong>Umur:</strong> {{ $sheep->date_of_birth->diffForHumans(null, true) }} (Lahir: {{ $sheep->date_of_birth->format('d M Y') }})</p>
                            <p><strong>Kategori:</strong> {{ $sheep->category }}</p>
                            <p><strong>Tipe/Ras:</strong> {{ $sheep->type }}</p>
                            {{-- Menghapus dark mode --}}
                            <hr>
                            <p><strong>Induk Betina:</strong> {{ $sheep->mother->tag_number ?? 'Tidak Diketahui' }}</p>
                            <p><strong>Induk Pejantan:</strong> {{ $sheep->father->tag_number ?? 'Tidak Diketahui' }}</p>

                            {{-- Tombol Aksi (Edit/Hapus) --}}
                            <div class="flex mt-6 space-x-3">
                                <a href="{{ route('sheep.edit', $sheep) }}">
                                    <x-secondary-button>Edit</x-secondary-button>
                                </a>
                                {{-- Form Hapus (jika diperlukan) --}}
                                {{-- <form action="#" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button onclick="return confirm('Yakin ingin menghapus domba ini?')">Hapus</x-danger-button>
                                </form> --}}
                            </div>
                        </div>
                    </div>

                    {{-- Di sini Anda bisa menambahkan tab untuk Riwayat Timbangan, Kesehatan, dll --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
