<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendapatan Sewa</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; margin: 0; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; color: #2563eb; }
        .header p { margin: 5px 0 0; color: #666; font-size: 12px; }
        
        .meta-info { margin-bottom: 20px; width: 100%; }
        .meta-info td { vertical-align: top; padding: 5px 0; }
        .label { font-weight: bold; width: 120px; display: inline-block; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data th, table.data td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.data th { background-color: #f3f4f6; color: #333; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        table.data tr:nth-child(even) { background-color: #f9fafb; }
        table.data tr.total { background-color: #2563eb; color: white; font-weight: bold; }
        table.data tr.total td { border-color: #2563eb; }

        .footer { margin-top: 50px; width: 100%; text-align: right; }
        .signature { display: inline-block; text-align: center; width: 200px; }
        .signature-line { border-top: 1px solid #333; margin-top: 60px; padding-top: 5px; }
        
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; display: inline-block; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Equipment Rental Corp</h1>
        <p>Jl. Contoh Alamat No. 123, Kota Besar, Indonesia | Telp: (021) 123-4567 | Email: info@equipmentrental.com</p>
    </div>

    <table class="meta-info">
        <tr>
            <td width="60%">
                <div class="label">Laporan:</div> Ringkasan Pendapatan Sewa<br>
                <div class="label">Periode:</div> {{ $periode }}
            </td>
            <td width="40%" style="text-align: right;">
                <div class="label">Tanggal Cetak:</div> {{ now()->format('d M Y, H:i') }}<br>
                <div class="label">Oleh:</div> {{ Auth::user()->name }}
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 100px;">Tanggal</th>
                <th style="width: 120px;">Kode Sewa</th>
                <th style="width: 150px;">Pelanggan</th>
                <th>Rincian Barang</th>
                <th style="width: 100px; text-align: right;">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sewas as $sewa)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($sewa->tgl_sewa)->format('d/m/Y') }}</td>
                <td><strong>{{ $sewa->kode_sewa }}</strong></td>
                <td>{{ $sewa->user->name ?? '-' }}</td>
                <td style="font-size: 10px;">
                    @foreach($sewa->detailSewas as $d)
                        <div>- {{ $d->barang->nama_barang ?? '-' }} ({{ $d->qty }} unit)</div>
                    @endforeach
                </td>
                <td style="text-align: right;">Rp {{ number_format($sewa->total_biaya, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="5" style="text-align: right; text-transform: uppercase;">Total Pendapatan</td>
                <td style="text-align: right; color: white;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Mengetahui,</p>
            <div class="signature-line">
                Pemilik / Direktur
            </div>
        </div>
    </div>

</body>
</html>
