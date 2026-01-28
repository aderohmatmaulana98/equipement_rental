<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $sewa->kode_sewa }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }
        
        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-info h1 {
            font-size: 24px;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .company-info p {
            color: #666;
            font-size: 11px;
        }
        
        .invoice-title {
            text-align: right;
        }
        
        .invoice-title h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
        }
        
        .invoice-title .invoice-number {
            font-size: 14px;
            color: #2563eb;
            font-weight: bold;
        }
        
        .invoice-title .invoice-date {
            font-size: 11px;
            color: #666;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }
        
        .status-lunas {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-dp {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-belum {
            background: #fee2e2;
            color: #991b1b;
        }
        
        /* Info Sections */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .info-box {
            width: 48%;
        }
        
        .info-box h3 {
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        
        .info-box p {
            margin-bottom: 3px;
        }
        
        .info-box .name {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        /* Table */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .invoice-table th {
            background: #2563eb;
            color: white;
            padding: 12px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .invoice-table th:last-child,
        .invoice-table td:last-child {
            text-align: right;
        }
        
        .invoice-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .invoice-table tbody tr:hover {
            background: #f9fafb;
        }
        
        /* Summary */
        .invoice-summary {
            display: flex;
            justify-content: flex-end;
        }
        
        .summary-table {
            width: 350px;
        }
        
        .summary-table tr td {
            padding: 8px 10px;
        }
        
        .summary-table tr td:first-child {
            text-align: left;
            color: #666;
        }
        
        .summary-table tr td:last-child {
            text-align: right;
            font-weight: 500;
        }
        
        .summary-table .subtotal {
            border-top: 1px solid #e5e7eb;
        }
        
        .summary-table .total {
            background: #2563eb;
            color: white;
        }
        
        .summary-table .total td {
            font-size: 14px;
            font-weight: bold;
            padding: 12px 10px;
        }
        
        .summary-table .paid {
            background: #dcfce7;
        }
        
        .summary-table .paid td {
            color: #166534;
            font-weight: bold;
        }
        
        .summary-table .remaining {
            background: #fef3c7;
        }
        
        .summary-table .remaining td {
            color: #92400e;
            font-weight: bold;
        }
        
        /* Footer */
        .invoice-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
        }
        
        .footer-note {
            width: 50%;
            font-size: 10px;
            color: #666;
        }
        
        .footer-note h4 {
            color: #333;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .signature {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
            font-size: 11px;
        }
        
        /* Event Info */
        .event-info {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .event-info h3 {
            font-size: 12px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .event-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        
        .event-info-item {
            font-size: 11px;
        }
        
        .event-info-item span {
            color: #666;
        }
        
        /* Print Styles */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .invoice-container {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
        }
        
        /* Print Button */
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .print-btn:hover {
            background: #1d4ed8;
        }
        
        .back-btn {
            position: fixed;
            top: 20px;
            right: 140px;
            background: #6b7280;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .back-btn:hover {
            background: #4b5563;
            color: white;
        }
    </style>
</head>
<body>
    <a href="{{ route('warehouse.detail', $sewa->id) }}" class="back-btn no-print">← Kembali</a>
    <button onclick="window.print()" class="print-btn no-print">🖨️ Cetak Invoice</button>
    
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>Equipment Rental</h1>
                <p>Jl. Contoh Alamat No. 123</p>
                <p>Telp: (021) 123-4567 | Email: info@equipmentrental.com</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <div class="invoice-number">{{ $sewa->kode_sewa }}</div>
                <div class="invoice-date">Tanggal: {{ \Carbon\Carbon::parse($sewa->created_at)->locale('id')->isoFormat('D MMMM Y') }}</div>
                
                @if($isLunas)
                    <span class="status-badge status-lunas">✓ LUNAS</span>
                @elseif($isDPLunas)
                    <span class="status-badge status-dp">DP LUNAS</span>
                @else
                    <span class="status-badge status-belum">BELUM LUNAS</span>
                @endif
            </div>
        </div>
        
        <!-- Info Section -->
        <div class="info-section">
            <div class="info-box">
                <h3>Ditagihkan Kepada</h3>
                <p class="name">{{ $sewa->user->name ?? '-' }}</p>
                <p>{{ $sewa->user->email ?? '-' }}</p>
                <p>{{ $sewa->user->no_hp ?? '-' }}</p>
                <p>{{ $sewa->alamat_acara }}</p>
            </div>
            <div class="info-box">
                <h3>Informasi Pembayaran</h3>
                <p><span>Tanggal Sewa:</span> {{ \Carbon\Carbon::parse($sewa->tgl_sewa)->locale('id')->isoFormat('D MMMM Y') }}</p>
                <p><span>Batas Pembayaran:</span> {{ \Carbon\Carbon::parse($sewa->batas_waktu_pembayaran)->locale('id')->isoFormat('D MMMM Y HH:mm') }}</p>
                <p><span>Status:</span> <strong>{{ ucfirst($sewa->status) }}</strong></p>
            </div>
        </div>
        
        <!-- Event Info -->
        @php
            $tglAcara = \Carbon\Carbon::parse($sewa->tgl_acara);
            $tglLoadingOut = \Carbon\Carbon::parse($sewa->tgl_loading_out);
            // Hitung inklusif: tanggal sama = 1 hari, 11 ke 12 = 2 hari
            $durasiHari = $tglAcara->diffInDays($tglLoadingOut) + 1;
        @endphp
        <div class="event-info">
            <h3>📅 Informasi Acara</h3>
            <div class="event-info-grid">
                <div class="event-info-item">
                    <span>Tanggal Acara:</span><br>
                    <strong>{{ \Carbon\Carbon::parse($sewa->tgl_acara)->locale('id')->isoFormat('dddd, D MMMM Y') }}</strong>
                </div>
                <div class="event-info-item">
                    <span>Jam Acara:</span><br>
                    <strong>{{ \Carbon\Carbon::parse($sewa->jam_acara)->format('H:i') }} WIB</strong>
                </div>
                <div class="event-info-item">
                    <span>Tanggal Loading:</span><br>
                    <strong>{{ \Carbon\Carbon::parse($sewa->tgl_loading)->locale('id')->isoFormat('D MMMM Y') }} - {{ \Carbon\Carbon::parse($sewa->jam_loading)->format('H:i') }} WIB</strong>
                </div>
                <div class="event-info-item">
                    <span>Tanggal Loading Out:</span><br>
                    <strong>{{ \Carbon\Carbon::parse($sewa->tgl_loading_out)->locale('id')->isoFormat('D MMMM Y') }}</strong>
                </div>
                <div class="event-info-item" style="grid-column: span 2; text-align: center; background: #2563eb; color: white; padding: 10px; border-radius: 5px; margin-top: 10px;">
                    <span style="color: rgba(255,255,255,0.8);">Durasi Sewa:</span><br>
                    <strong style="font-size: 18px;">{{ $durasiHari }} HARI</strong>
                </div>
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Barang</th>
                    <th style="width: 40px;">Qty</th>
                    <th style="width: 100px;">Harga Satuan</th>
                    <th style="width: 50px;">Durasi</th>
                    <th style="width: 130px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detailSewa as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td>{{ $durasiHari }} hari</td>
                    <td>
                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                        <br><small style="color: #666; font-size: 9px;">({{ number_format($detail->harga_satuan, 0, ',', '.') }} × {{ $detail->qty }} × {{ $durasiHari }})</small>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="invoice-summary">
            <table class="summary-table">
                @if($sewa->diskon_nominal && $sewa->diskon_nominal > 0)
                <tr>
                    <td>Subtotal</td>
                    <td>Rp {{ number_format($sewa->total_sebelum_diskon, 0, ',', '.') }}</td>
                </tr>
                <tr style="color: #28a745;">
                    <td>Diskon {{ $sewa->diskon_persen ? '(' . $sewa->diskon_persen . '%)' : '' }}</td>
                    <td>- Rp {{ number_format($sewa->diskon_nominal, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="subtotal">
                    <td>Total Biaya</td>
                    <td>Rp {{ number_format($sewa->total_biaya, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Uang Muka (DP 50%)</td>
                    <td>
                        Rp {{ number_format($sewa->uang_muka, 0, ',', '.') }}
                        @if($isDPLunas || $isLunas) ✓ @endif
                    </td>
                </tr>
                <tr>
                    <td>Pelunasan (50%)</td>
                    <td>
                        Rp {{ number_format($sewa->sisa_pembayaran ?? ($sewa->total_biaya - $sewa->uang_muka), 0, ',', '.') }}
                        @if($isLunas) ✓ @endif
                    </td>
                </tr>
                <tr class="paid">
                    <td>Sudah Dibayar</td>
                    <td>Rp {{ number_format($sudahBayar, 0, ',', '.') }}</td>
                </tr>
                @php
                    $sisaBayar = $sewa->total_biaya - $sudahBayar;
                @endphp
                @if($sisaBayar > 0)
                <tr class="remaining">
                    <td>Sisa yang Harus Dibayar</td>
                    <td>Rp {{ number_format($sisaBayar, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total">
                    <td>GRAND TOTAL</td>
                    <td>Rp {{ number_format($sewa->total_biaya, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="invoice-footer">
            <div class="footer-note">
                <h4>Catatan:</h4>
                <p>• Pembayaran DP minimal 50% dari total biaya.</p>
                <p>• Pelunasan dilakukan sebelum tanggal acara.</p>
                <p>• Invoice ini sah sebagai bukti transaksi.</p>
                <p>• Barang yang disewa menjadi tanggung jawab penyewa.</p>
            </div>
            <div class="signature">
                <p>Hormat Kami,</p>
                <div class="signature-line">
                    Equipment Rental
                </div>
            </div>
        </div>
    </div>
</body>
</html>
