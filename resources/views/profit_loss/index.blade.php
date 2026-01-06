<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-800">
                    {{ __('Keuangan & Arus Kas') }}
                </h2>
                <p class="mt-1 text-xs text-gray-500">Pantau kesehatan finansial peternakan Anda.</p>
            </div>
            <a href="{{ route('profit-loss.create') }}">
                <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 shadow-md transition-transform transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    {{ __('Catat Transaksi Baru') }}
                </x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="mx-auto space-y-8 max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="flex items-center p-4 text-sm text-green-800 border border-green-200 rounded-lg bg-green-50" role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <div>
                        <span class="font-medium">Berhasil!</span> {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- 1. RINGKASAN SALDO (CARD MODERN) --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                {{-- Saldo --}}
                <div class="relative p-6 overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
                    <div class="absolute top-0 right-0 w-24 h-24 -mt-4 -mr-4 rounded-full opacity-50 bg-gradient-to-br from-indigo-50 to-indigo-100 blur-xl"></div>
                    <div class="relative z-10">
                        <p class="mb-1 text-sm font-medium tracking-wider text-gray-500 uppercase">Saldo Saat Ini</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">
                            Rp {{ number_format($balance, 0, ',', '.') }}
                        </h3>
                        <div class="flex items-center mt-4 text-xs">
                            @if($balance >= 0)
                                <span class="flex items-center px-2 py-1 font-bold text-green-700 bg-green-100 rounded-md">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    Positif
                                </span>
                                <span class="ml-2 text-gray-400">Keuangan Sehat</span>
                            @else
                                <span class="flex items-center px-2 py-1 font-bold text-red-700 bg-red-100 rounded-md">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                    Negatif
                                </span>
                                <span class="ml-2 text-gray-400">Perhatian Diperlukan</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Pemasukan --}}
                <div class="flex items-center justify-between p-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
                    <div>
                        <p class="mb-1 text-sm font-medium tracking-wider text-gray-500 uppercase">Total Pemasukan</p>
                        <h3 class="text-2xl font-bold text-green-600">
                            + Rp {{ number_format($totalIncome, 0, ',', '.') }}
                        </h3>
                        <p class="mt-2 text-xs text-gray-400">Bulan Ini</p>
                    </div>
                    <div class="p-3 text-green-600 rounded-full bg-green-50">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                    </div>
                </div>

                {{-- Pengeluaran --}}
                <div class="flex items-center justify-between p-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
                    <div>
                        <p class="mb-1 text-sm font-medium tracking-wider text-gray-500 uppercase">Total Pengeluaran</p>
                        <h3 class="text-2xl font-bold text-red-600">
                            - Rp {{ number_format($totalExpense, 0, ',', '.') }}
                        </h3>
                        <p class="mt-2 text-xs text-gray-400">Bulan Ini</p>
                    </div>
                    <div class="p-3 text-red-600 rounded-full bg-red-50">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                    </div>
                </div>
            </div>

            {{-- 2. FILTER BAR (MINIMALIS) --}}
            <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl">
                <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-sm font-bold tracking-wide text-gray-700 uppercase">Filter Transaksi</h3>
                    @if(request()->hasAny(['start_date', 'end_date', 'type']))
                        <a href="{{ route('profit-loss.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                            Reset Filter
                        </a>
                    @endif
                </div>
                <div class="p-4">
                    <form method="GET" action="{{ route('profit-loss.index') }}">
                        <div class="grid items-end grid-cols-1 gap-4 md:grid-cols-12">
                            <div class="md:col-span-3">
                                <label class="block mb-1 text-xs font-medium text-gray-500">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block mb-1 text-xs font-medium text-gray-500">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block mb-1 text-xs font-medium text-gray-500">Jenis Transaksi</label>
                                <select name="type" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Semua</option>
                                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan (Income)</option>
                                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran (Expense)</option>
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white transition-colors bg-gray-800 rounded-lg shadow-md hover:bg-gray-900">
                                    Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 3. DAFTAR TRANSAKSI (STYLE REKENING KORAN) --}}
            <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Riwayat Transaksi</h3>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse ($records as $record)
                        <div class="flex flex-col justify-between gap-4 p-6 transition-colors hover:bg-gray-50 md:flex-row md:items-center group">

                            {{-- BAGIAN KIRI: Tanggal & Ikon --}}
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $record->type === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                        @if($record->type === 'income')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="text-sm font-bold text-gray-900">{{ $record->category }}</h4>
                                        <span class="text-xs text-gray-400">• {{ $record->date->format('d M Y') }}</span>
                                    </div>
                                    <p class="mb-2 text-sm text-gray-600">{{ $record->description ?? 'Tidak ada catatan.' }}</p>

                                    {{-- Tag Relasi (Domba/Kandang) --}}
                                    <div class="flex gap-2">
                                        @if($record->sheep)
                                            <a href="{{ route('sheep.show', $record->sheep) }}" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                                                🐑 {{ $record->sheep->tag_number }}
                                            </a>
                                        @endif
                                        @if($record->shelter)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                🏠 {{ $record->shelter->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- BAGIAN KANAN: Nominal & Aksi --}}
                            <div class="flex items-center justify-between w-full gap-6 mt-2 md:justify-end md:w-auto md:mt-0 pl-14 md:pl-0">
                                <div class="text-right">
                                    <span class="block text-lg font-bold {{ $record->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $record->type === 'income' ? '+' : '-' }} Rp {{ number_format($record->amount, 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-gray-400 capitalize">{{ $record->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}</span>
                                </div>

                                {{-- Tombol Hapus (Muncul saat hover di Desktop, selalu muncul di Mobile) --}}
                                <div class="transition-opacity opacity-100 md:opacity-0 md:group-hover:opacity-100">
                                    <form action="{{ route('profit-loss.destroy', $record) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini? Saldo akan dihitung ulang.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 transition-colors rounded-full hover:text-red-600 hover:bg-red-50" title="Hapus Transaksi">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 text-gray-400 bg-gray-100 rounded-full">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Belum ada transaksi</h3>
                            <p class="mt-1 text-gray-500">Mulai catat pemasukan dan pengeluaran Anda.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($records->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
