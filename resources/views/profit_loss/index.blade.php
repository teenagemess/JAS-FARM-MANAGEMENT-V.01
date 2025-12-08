<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Buku Kas Peternakan') }}
            </h2>
            <a href="{{ route('profit-loss.create') }}">
                <x-primary-button class="justify-center w-full bg-indigo-600 sm:w-auto hover:bg-indigo-700">
                    {{ __('+ Catat Uang Masuk/Keluar') }}
                </x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="p-4 mb-6 text-green-700 border-l-4 border-green-500 rounded-md shadow-sm bg-green-50">
                    {{ session('success') }}
                </div>
            @endif

            {{-- KARTU RINGKASAN --}}
            <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
                {{-- Card Pemasukan --}}
                <div class="p-6 bg-white border-l-4 border-green-500 shadow-sm rounded-xl">
                    <div class="flex items-center text-sm font-medium text-gray-500 uppercase">
                        <span class="p-1 mr-2 text-green-800 bg-green-100 rounded-full">⬇️</span> Total Pemasukan
                    </div>
                    <div class="mt-2 text-2xl font-bold text-green-700">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                </div>

                {{-- Card Pengeluaran --}}
                <div class="p-6 bg-white border-l-4 border-red-500 shadow-sm rounded-xl">
                    <div class="flex items-center text-sm font-medium text-gray-500 uppercase">
                        <span class="p-1 mr-2 text-red-800 bg-red-100 rounded-full">⬆️</span> Total Pengeluaran
                    </div>
                    <div class="mt-2 text-2xl font-bold text-red-700">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                </div>

                {{-- Card Saldo --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 {{ $balance >= 0 ? 'border-blue-500' : 'border-orange-500' }}">
                    <div class="flex items-center text-sm font-medium text-gray-500 uppercase">
                        <span class="p-1 mr-2 text-blue-800 bg-blue-100 rounded-full">💰</span> Sisa Uang (Saldo)
                    </div>
                    <div class="mt-2 text-2xl font-bold {{ $balance >= 0 ? 'text-blue-700' : 'text-orange-700' }}">
                        Rp {{ number_format($balance, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- TABEL TRANSAKSI --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">Riwayat Transaksi Terakhir</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Keterangan</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Jumlah (Rp)</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($records as $record)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                        {{ $record->date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-800">{{ $record->category }}</div>
                                        <div class="text-xs text-gray-500">{{ $record->description }}</div>
                                        @if($record->sheep)
                                            <span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded text-gray-600 mt-1 inline-block">Domba: {{ $record->sheep->tag_number }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold {{ $record->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $record->type === 'income' ? '+' : '-' }} {{ number_format($record->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                        <form action="{{ route('profit-loss.destroy', $record) }}" method="POST" onsubmit="return confirm('Hapus catatan ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-sm text-center text-gray-500">
                                        Belum ada catatan uang masuk/keluar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                    {{ $records->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
