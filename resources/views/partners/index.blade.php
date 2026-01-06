<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Daftar Mitra (Plasma)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($partners as $partner)
                    <a href="{{ route('partners.show', $partner) }}" class="block group">
                        <div class="overflow-hidden transition-shadow bg-white border border-gray-100 shadow-sm sm:rounded-lg hover:shadow-md group-hover:border-indigo-300">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 text-xl font-bold text-indigo-600 bg-indigo-100 rounded-full">
                                        {{ substr($partner->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600">{{ $partner->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $partner->address ?? 'Alamat belum diisi' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <div>
                                        <p class="text-xs tracking-wide text-gray-500 uppercase">Domba Titipan</p>
                                        <p class="text-2xl font-bold text-gray-800">{{ $partner->total_sheep }} <span class="text-sm font-normal text-gray-500">Ekor</span></p>
                                    </div>
                                    <div class="text-indigo-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="py-12 text-center bg-white rounded-lg shadow-sm col-span-full">
                        <p class="text-gray-500">Belum ada mitra yang terdaftar.</p>
                        <a href="{{ route('users.create') }}" class="inline-block mt-2 text-indigo-600 hover:underline">Tambah Mitra Baru</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $partners->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
