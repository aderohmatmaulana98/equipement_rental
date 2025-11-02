<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Sewa</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .sub-title {
            text-align: center;
            font-size: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead tr {
            background: #007bff;
            color: #fff;
        }

        table, th, td {
            border: 1px solid #777;
        }

        th, td {
            padding: 7px 8px;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background: #f4f7fc;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>

</head>
<body>

    <h2><strong>LAPORAN PENYEWAAN BARANG</strong></h2>
    <p class="sub-title">Perusahaan Rental Equipment</p>

    <table>
        <thead>
            <tr>
                <th>Kode Sewa</th>
                <th>Tanggal Sewa</th>
                <th>Barang</th>
                <th>Total Biaya</th>
            </tr>
        </thead>

        <tbody>
            @foreach($sewas as $sewa)
            <tr>
                <td>{{ $sewa->kode_sewa }}</td>
                <td>{{ \Carbon\Carbon::parse($sewa->tgl_sewa)->format('d-m-Y') }}</td>
                <td>
                    @foreach($sewa->detailSewas as $d)
                        • {{ $d->barang->nama_barang }} (x{{ $d->qty }})<br>
                    @endforeach
                </td>
                <td>Rp {{ number_format($sewa->total_biaya, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}
    </p>

</body>
</html>
