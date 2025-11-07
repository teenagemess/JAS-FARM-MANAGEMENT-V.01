<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Tambah Data Domba Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Menampilkan error validasi --}}
                    @if ($errors->any())
                        <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-md">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('sheep.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- (2) Baris 1: Eartag, Kategori, Tipe --}}
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                            {{-- (PERUBAHAN: Input Eartag dengan Prefix Addon) --}}
                            <div>
                                <x-input-label for="tag_number" :value="__('Eartag (Wajib)')" />
                                <div class="flex mt-1 rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 text-gray-500 border border-r-0 border-gray-300 rounded-l-md bg-gray-50 sm:text-sm">
                                        JAS-
                                    </span>
                                    <x-text-input id="tag_number" class="flex-1 block w-full rounded-none rounded-r-md" type="text" name="tag_number" :value="old('tag_number')" required autofocus placeholder="001" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="category" :value="__('Kategori (Wajib)')" />
                                <x-text-input id="category" class="block w-full mt-1" type="text" name="category" :value="old('category')" required placeholder="Contoh: Pedaging, Indukan" />
                            </div>
                            <div>
                                <x-input-label for="type" :value="__('Tipe/Ras (Wajib)')" />
                                <x-text-input id="type" class="block w-full mt-1" type="text" name="type" :value="old('type')" required placeholder="Contoh: Garut, Texel" />
                            </div>
                        </div>

                        {{-- (3) Baris 2: Kandang, Gender, Tgl Lahir, Bobot Lahir --}}
                        <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-4">
                            <div>
                                <x-input-label for="shelter_id" :value="__('Kandang (Wajib)')" />
                                <select id="shelter_id" name="shelter_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih Kandang</option>
                                    @foreach($shelters as $shelter)
                                        <option value="{{ $shelter->id }}" {{ old('shelter_id') == $shelter->id ? 'selected' : '' }}>
                                            {{ $shelter->name }} (Kapasitas: {{ $shelter->capacity }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="gender" :value="__('Jenis Kelamin (Wajib)')" />
                                <select id="gender" name="gender" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Jantan" {{ old('gender') == 'Jantan' ? 'selected' : '' }}>Jantan</option>
                                    <option value="Betina" {{ old('gender') == 'Betina' ? 'selected' : '' }}>Betina</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="date_of_birth" :value="__('Tgl Lahir (Wajib)')" />
                                <x-text-input id="date_of_birth" class="block w-full mt-1" type="date" name="date_of_birth" :value="old('date_of_birth')" required />
                            </div>
                            <div>
                                <x-input-label for="birth_weight" :value="__('Bobot Lahir (KG)')" />
                                <x-text-input id="birth_weight" class="block w-full mt-1" type="number" step="0.01" name="birth_weight" :value="old('birth_weight')" />
                            </div>
                        </div>

                        {{-- (4) Baris 3: Silsilah (Induk/Pejantan) --}}
                        <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="mother_id" :value="__('Induk Betina (Opsional)')" />
                                <select id="mother_id" name="mother_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih Induk (Jika diketahui)</option>
                                    @foreach($potential_dams as $dam)
                                        <option value="{{ $dam->id }}" {{ old('mother_id') == $dam->id ? 'selected' : '' }}>
                                            Eartag: {{ $dam->tag_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="father_id" :value="__('Induk Pejantan (Opsional)')" />
                                <select id="father_id" name="father_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih Pejantan (Jika diketahui)</option>
                                    @foreach($potential_sires as $sire)
                                        <option value="{{ $sire->id }}" {{ old('father_id') == $sire->id ? 'selected' : '' }}>
                                            Eartag: {{ $sire->tag_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- (5) Baris 4: Harga Beli & Bibit Unggul --}}
                        <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="purchase_price" :value="__('Harga Beli (Opsional)')" />
                                <x-text-input id="purchase_price" class="block w-full mt-1" type="number" name="purchase_price" :value="old('purchase_price')" />
                            </div>
                            <div class="flex items-center mt-6">
                                <input type="hidden" name="is_pedigree" value="0">
                                <input id="is_pedigree" name="is_pedigree" type="checkbox" value="1"
                                       {{ old('is_pedigree') == 1 ? 'checked' : '' }}
                                       class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500">
                                <label for="is_pedigree" class="ml-2 text-sm text-gray-600">Bibit Unggul (Pedigree)</label>
                            </div>
                        </div>

                        {{-- (6) Baris 5: Upload Foto --}}
                        <div class="mt-4">
                            <x-input-label for="photo" :value="__('Foto Domba (Opsional)')" />
                            <x-text-input id="photo" class="block w-full mt-1" type="file" name="photo" />
                        </div>

                        {{-- (7) Baris 6: Deskripsi --}}
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Deskripsi/Catatan (Opsional)')" />
                            <textarea id="description" name="description" rows="4"
                                      class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        </div>

                        {{-- (8) Tombol Submit --}}
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('sheep.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Domba') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
