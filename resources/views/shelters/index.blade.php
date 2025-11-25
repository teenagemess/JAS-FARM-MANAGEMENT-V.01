<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Manajemen Kandang') }}
            </h2>
            <a href="{{ route('shelters.create') }}">
                <x-primary-button>{{ __('+ Tambah Kandang') }}</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
             @if(session('error'))
                <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Tabel Kandang --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Nama Kandang
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Kapasitas
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Domba Saat Ini
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Deskripsi
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($shelters as $shelter)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            {{ $shelter->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $shelter->capacity }} Ekor
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            {{-- Memanggil accessor current_count --}}
                                            <span class="font-bold {{ $shelter->current_count >= $shelter->capacity ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $shelter->current_count }}
                                            </span>
                                            / {{ $shelter->capacity }} Ekor
                                        </td>
                                        <td class="max-w-xs px-6 py-4 text-sm text-gray-500 truncate whitespace-nowrap">
                                            {{ $shelter->description ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('shelters.edit', $shelter) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>

                                                {{-- Tombol Hapus --}}
                                                <form action="{{ route('shelters.destroy', $shelter) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Kandang {{ $shelter->name }}? Pastikan tidak ada domba di dalamnya.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="ml-2 text-red-600 hover:text-red-900">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-sm text-center text-gray-500">
                                            Belum ada data kandang yang diinput.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div> {{-- Akhir Overflow --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
