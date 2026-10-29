<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Penjualan {{ $startDate }} s/d {{ $endDate }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #d1d5db; padding: 6px 10px; }
        th { background: #f3f4f6; text-align: left; font-size: 12px; text-transform: uppercase; }
        td { font-size: 13px; }
        .text-right { text-align: right; }
    </style>
</head>
<body class="bg-white p-8 max-w-4xl mx-auto">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-xl font-bold uppercase">Rekap Penjualan</h1>
            <p class="text-sm text-gray-600">{{ config('app.name') }}</p>
        </div>
        <div class="text-right text-sm">
            <p class="font-medium">Periode</p>
            <p>{{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d F Y') }}</p>
        </div>
    </div>

    <table class="mb-6">
        <tr>
            <th>Total Omzet</th><td class="text-right">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</td>
            <th>Laba Kotor</th><td class="text-right">Rp {{ number_format($labaKotor, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Jumlah Transaksi</th><td class="text-right">{{ number_format($jumlahTransaksi, 0, ',', '.') }}</td>
            <th>Rata-rata / Hari</th><td class="text-right">Rp {{ number_format($rataHarian, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h2 class="font-bold mt-6 mb-2">Rekap Per Hari</h2>
    <table class="mb-6">
        <thead>
            <tr><th>Tanggal</th><th class="text-right">Transaksi</th><th class="text-right">Omzet</th></tr>
        </thead>
        <tbody>
            @forelse($perHari as $hari)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($hari->tanggal)->locale('id')->translatedFormat('d/m/Y') }}</td>
                    <td class="text-right">{{ $hari->jumlah_transaksi }}</td>
                    <td class="text-right">Rp {{ number_format($hari->omzet, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center text-gray-500">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="font-bold mt-6 mb-2">Rekap Per Kasir</h2>
    <table class="mb-6">
        <thead>
            <tr><th>Kasir</th><th class="text-right">Transaksi</th><th class="text-right">Omzet</th></tr>
        </thead>
        <tbody>
            @forelse($perKasir as $kasir)
                <tr>
                    <td>{{ $kasir->name }}</td>
                    <td class="text-right">{{ $kasir->jumlah_transaksi }}</td>
                    <td class="text-right">Rp {{ number_format($kasir->omzet, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center text-gray-500">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="font-bold mt-6 mb-2">10 Produk Terlaris</h2>
    <table class="mb-8">
        <thead>
            <tr><th>Kode</th><th>Nama Produk</th><th class="text-right">Terjual</th><th class="text-right">Omzet</th><th class="text-right">Laba</th></tr>
        </thead>
        <tbody>
            @forelse($produkTerjual as $produk)
                <tr>
                    <td>{{ $produk->kode_produk }}</td>
                    <td>{{ $produk->nama_produk }}</td>
                    <td class="text-right">{{ $produk->total_terjual }}</td>
                    <td class="text-right">Rp {{ number_format($produk->omzet, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($produk->laba, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-gray-500">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="flex justify-between text-sm mt-16">
        <div class="text-center">
            <p>Dicetak oleh,</p>
            <div class="h-14"></div>
            <p class="underline">{{ auth()->user()->name }}</p>
        </div>
        <div class="text-center">
            <p>Mengetahui,</p>
            <div class="h-14"></div>
            <p class="underline">(............................)</p>
        </div>
    </div>

    <div class="no-print fixed bottom-6 right-6 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg shadow-lg hover:bg-blue-700 font-medium">
            <i class="fas fa-print mr-2"></i>Cetak
        </button>
        <a href="{{ route('rekap.index', request()->only(['start_date', 'end_date'])) }}"
            class="bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg shadow hover:bg-gray-300 font-medium">Kembali</a>
    </div>

    <script>window.addEventListener('load', () => setTimeout(() => window.print(), 500));</script>
</body>
</html>
