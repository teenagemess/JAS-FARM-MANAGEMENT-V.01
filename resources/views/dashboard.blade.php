<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard Peternakan') }}
        </h2>
    </x-slot>

    {{-- LOAD CHART.JS DARI CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- BAGIAN 1: KARTU STATISTIK (KPI) --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">

                {{-- Total Domba --}}
                <div class="p-6 overflow-hidden bg-white border-l-4 border-indigo-500 shadow-sm sm:rounded-lg">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-indigo-500 bg-indigo-100 rounded-full">
                            🐑
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Populasi</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalSheep }} <span class="text-xs font-normal text-gray-400">Ekor</span></p>
                        </div>
                    </div>
                </div>

                {{-- Domba Sakit --}}
                <div class="p-6 overflow-hidden bg-white border-l-4 border-red-500 shadow-sm sm:rounded-lg">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-red-500 bg-red-100 rounded-full">
                            🩺
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Sedang Sakit</p>
                            <p class="text-2xl font-bold text-red-600">{{ $activeSicknessCount }} <span class="text-xs font-normal text-gray-400">Ekor</span></p>
                        </div>
                    </div>
                </div>

                {{-- Domba Hamil --}}
                <div class="p-6 overflow-hidden bg-white border-l-4 border-purple-500 shadow-sm sm:rounded-lg">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full">
                            🤰
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Sedang Bunting</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $pregnantCount }} <span class="text-xs font-normal text-gray-400">Indukan</span></p>
                        </div>
                    </div>
                </div>

                {{-- Saldo Bulan Ini --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 {{ $balanceThisMonth >= 0 ? 'border-green-500' : 'border-orange-500' }}">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full {{ $balanceThisMonth >= 0 ? 'bg-green-100 text-green-500' : 'bg-orange-100 text-orange-500' }} mr-4">
                            💰
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Saldo Bulan Ini</p>
                            <p class="text-lg font-bold {{ $balanceThisMonth >= 0 ? 'text-green-600' : 'text-orange-600' }}">
                                Rp {{ number_format($balanceThisMonth, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 2: GRAFIK STATISTIK (BARU) --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- Grafik Keuangan --}}
                <div class="p-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <h3 class="mb-4 font-bold text-gray-700">📊 Arus Kas Tahun Ini</h3>
                    <canvas id="financialChart" height="200"></canvas>
                </div>

                {{-- Grafik Pertumbuhan --}}
                <div class="p-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <h3 class="mb-4 font-bold text-gray-700">📈 Pertumbuhan Populasi (Domba Baru)</h3>
                    <canvas id="growthChart" height="200"></canvas>
                </div>

            </div>

            {{-- BAGIAN 3: PERINGATAN & DAFTAR --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- KOLOM KIRI: PERLU PERHATIAN --}}
                <div class="space-y-6">

                    {{-- Tabel Sakit --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-red-50">
                            <h3 class="font-bold text-red-800">⚠️ Perlu Perawatan (Sakit)</h3>
                            <a href="{{ route('sheep.index', ['filter_health' => 'sick']) }}" class="text-xs text-red-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="p-4">
                            @forelse($sickSheepList as $health)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                    <div class="flex items-center">
                                        <div class="ml-3">
                                            <p class="text-sm font-bold text-gray-900">
                                                <a href="{{ route('sheep.show', $health->sheep_id) }}" class="hover:text-indigo-600">
                                                    {{ $health->sheep->tag_number }}
                                                </a>
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $health->sheep->shelter->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $health->diagnosis }}
                                        </span>
                                        <p class="text-[10px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($health->record_date)->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="py-4 text-sm text-center text-gray-500">Alhamdulillah, tidak ada domba sakit.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Tabel Hamil --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-purple-50">
                            <h3 class="font-bold text-purple-800">🤰 Estimasi Kelahiran Terdekat</h3>
                            <a href="#" class="text-xs text-purple-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="p-4">
                            @forelse($pregnantSheepList as $repro)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">
                                            <a href="{{ route('sheep.show', $repro->female_sheep_id) }}" class="hover:text-indigo-600">
                                                {{ $repro->dam->tag_number }}
                                            </a>
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $repro->dam->shelter->name ?? '-' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-purple-600">
                                            {{ \Carbon\Carbon::parse($repro->expected_delivery_date)->format('d M Y') }}
                                        </p>
                                        <p class="text-[10px] text-gray-500">
                                            {{ \Carbon\Carbon::parse($repro->expected_delivery_date)->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="py-4 text-sm text-center text-gray-500">Tidak ada data kehamilan aktif.</p>
                            @endforelse
                        </div>
                    </div>

                </div>

                {{-- KOLOM KANAN: AKTIVITAS KEUANGAN TERAKHIR --}}
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg h-fit">
                    <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-gray-800">💸 Transaksi Terakhir</h3>
                        <a href="{{ route('profit-loss.index') }}" class="text-xs text-indigo-600 hover:underline">Buka Buku Kas</a>
                    </div>
                    <div class="p-4">
                        @forelse($recentTransactions as $transaction)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->category }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->date->format('d M') }} • {{ Str::limit($transaction->description, 20) }}</p>
                                </div>
                                <span class="text-sm font-bold {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type == 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <p class="py-4 text-sm text-center text-gray-500">Belum ada transaksi.</p>
                        @endforelse

                        <div class="pt-4 mt-4 border-t border-gray-100">
                            <a href="{{ route('profit-loss.create') }}">
                                <x-primary-button class="justify-center w-full text-xs">
                                    {{ __('+ Catat Transaksi Baru') }}
                                </x-primary-button>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- SCRIPT UNTUK CHART.JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Data dari Controller
            const incomeData = @json(array_values($monthlyIncome));
            const expenseData = @json(array_values($monthlyExpense));
            const sheepData = @json(array_values($monthlySheep));
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            // 1. Grafik Keuangan (Bar Chart)
            new Chart(document.getElementById('financialChart'), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: incomeData,
                            backgroundColor: 'rgba(34, 197, 94, 0.6)', // Green
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Pengeluaran',
                            data: expenseData,
                            backgroundColor: 'rgba(239, 68, 68, 0.6)', // Red
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // 2. Grafik Pertumbuhan (Line Chart)
            new Chart(document.getElementById('growthChart'), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Domba Baru (Ekor)',
                        data: sheepData,
                        borderColor: 'rgba(79, 70, 229, 1)', // Indigo
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 } // Angka bulat (tidak ada 1.5 domba)
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
