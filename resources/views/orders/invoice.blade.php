<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $transaction->id }} - IndoOngkir</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 40px;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 30px;
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: -0.5px;
        }
        
        .logo span {
            color: #0f172a;
        }
        
        .invoice-title {
            text-align: right;
        }
        
        .invoice-title h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        
        .invoice-title p {
            margin: 5px 0 0 0;
            color: #64748b;
            font-weight: 500;
        }
        
        .details-grid {
            display: grid;
            grid-template-cols: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .details-block h3 {
            margin: 0 0 12px 0;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
        }
        
        .details-block p {
            margin: 0 0 6px 0;
            color: #334155;
        }
        
        .details-block p strong {
            color: #0f172a;
        }
        
        .address-box {
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 15px;
            font-size: 13px;
            color: #475569;
            line-height: 1.6;
            margin-top: 8px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-bottom: 40px;
        }
        
        .items-table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }
        
        .items-table td {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        
        .items-table td.qty {
            text-align: center;
        }
        
        .items-table td.price, .items-table td.total {
            text-align: right;
        }
        
        .summary-box {
            display: flex;
            justify-content: flex-end;
        }
        
        .summary-table {
            width: 320px;
            border-collapse: collapse;
        }
        
        .summary-table td {
            padding: 10px 16px;
            color: #475569;
        }
        
        .summary-table td.amount {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }
        
        .summary-table tr.grand-total td {
            border-top: 2px solid #e2e8f0;
            padding-top: 15px;
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }
        
        .summary-table tr.grand-total td.amount {
            font-size: 18px;
            color: #4f46e5;
        }
        
        .print-btn-container {
            max-width: 800px;
            margin: 20px auto 0 auto;
            text-align: right;
        }
        
        .print-btn {
            background-color: #4f46e5;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        
        .print-btn:hover {
            background-color: #4338ca;
        }

        /* Printable styles */
        @media print {
            body {
                padding: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
            }
            .print-btn-container {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-container">
        <button onclick="window.print()" class="print-btn">Cetak / Simpan PDF</button>
    </div>

    <div class="invoice-box">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                Indo<span>Ongkir</span>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <p>#{{ $transaction->id }}</p>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="details-grid">
            <div class="details-block">
                <h3>Penerima / Alamat</h3>
                <p><strong>{{ $transaction->user->name }}</strong></p>
                <p>{{ $transaction->user->email }}</p>
                <div class="address-box">
                    {{ $transaction->address_details }}<br>
                    <strong>{{ $transaction->city }}, {{ $transaction->province }}</strong>
                </div>
            </div>
            
            <div class="details-block" style="text-align: right;">
                <h3>Detail Transaksi</h3>
                <p>Tanggal: <strong>{{ $transaction->created_at->format('d/m/Y H:i') }}</strong></p>
                <p>Status: <strong style="text-transform: uppercase;">{{ $transaction->status == 'belum_bayar' ? 'Belum Bayar' : ($transaction->status == 'dikirim' ? 'Dikirim' : 'Selesai') }}</strong></p>
                <p>Kurir: <strong>{{ $transaction->courier }} ({{ $transaction->service }})</strong></p>
                @if($transaction->tracking_number)
                    <p>Nomor Resi: <strong style="font-family: monospace;">{{ $transaction->tracking_number }}</strong></p>
                @endif
                <p>Total Berat: <strong>{{ $transaction->weight >= 1000 ? ($transaction->weight / 1000) . ' kg' : $transaction->weight . ' gram' }}</strong></p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Deskripsi</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name }}</strong><br>
                            <span style="font-size: 11px; color: #94a3b8;">Berat: {{ $item->weight }} gr</span>
                        </td>
                        <td class="price">{{ $item->formatted_price }}</td>
                        <td class="qty">{{ $item->quantity }}</td>
                        <td class="total">{{ $item->formatted_subtotal }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-box">
            <table class="summary-table">
                <tr>
                    <td>Subtotal Belanja</td>
                    <td class="amount">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Biaya Pengiriman</td>
                    <td class="amount">{{ $transaction->formatted_shipping_cost }}</td>
                </tr>
                <tr class="grand-total">
                    <td>Total Bayar</td>
                    <td class="amount">{{ $transaction->formatted_total }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Print immediately on load -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Auto open print dialog, with a tiny delay to allow font/rendering loading
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>