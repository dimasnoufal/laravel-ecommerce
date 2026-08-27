<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportMeta['name'] }} — Print Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
            --primary: #2563eb;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-main);
            background: #ffffff;
            line-height: 1.5;
            padding: 2.5rem;
        }

        .print-toolbar {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: #0f172a;
            color: #ffffff;
            padding: 0.75rem 1.25rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            z-index: 9999;
        }

        .btn-print {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 0.4rem 1rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.8125rem;
            cursor: pointer;
        }
        .btn-print:hover { background: #1d4ed8; }

        .btn-close {
            background: rgba(255,255,255,0.15);
            color: #ffffff;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            cursor: pointer;
        }

        /* Document Header */
        .doc-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 2px solid var(--text-main);
            padding-bottom: 1.25rem;
            margin-bottom: 2rem;
        }

        .brand-info h1 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-main);
        }
        .brand-info p {
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        .doc-meta {
            text-align: right;
            font-size: 0.8125rem;
            color: var(--text-muted);
        }
        .doc-meta strong {
            color: var(--text-main);
        }

        /* Summary KPIs */
        .summary-box {
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 2rem;
            page-break-inside: avoid;
        }

        .summary-title {
            font-size: 0.875rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
            color: var(--primary);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
        }
        .summary-item-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
        }
        .summary-item-value {
            font-size: 1.125rem;
            font-weight: 800;
            color: var(--text-main);
        }

        /* Detail Table */
        .section-title {
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8125rem;
            margin-bottom: 2rem;
        }

        th {
            background: var(--bg-light);
            border-top: 1px solid var(--border-color);
            border-bottom: 2px solid var(--border-color);
            padding: 0.6rem 0.75rem;
            font-weight: 700;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        td {
            padding: 0.6rem 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }

        tr:nth-child(even) td {
            background: #fafbfc;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Document Footer */
        .doc-footer {
            margin-top: 3rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            color: var(--text-muted);
            page-break-inside: avoid;
        }

        @media print {
            body { padding: 0; }
            .print-toolbar { display: none !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
</head>
<body>

    <!-- Floating Print Controls -->
    <div class="print-toolbar">
        <span style="font-size: 0.8125rem; font-weight: 600;">Dokumen Laporan</span>
        <button class="btn-print" onclick="window.print()">Cetak / Save PDF</button>
        <button class="btn-close" onclick="window.close()">Tutup</button>
    </div>

    <!-- Document Header -->
    <div class="doc-header">
        <div class="brand-info">
            <h1>LARAVEL E-COMMERCE</h1>
            <p>Official Business Analytics & Operational Report</p>
            <div style="margin-top: 0.5rem; font-weight: 700; font-size: 1.125rem; color: var(--primary);">
                {{ $reportMeta['name'] }}
            </div>
        </div>
        <div class="doc-meta">
            <div>Tipe Dokumen: <strong>{{ $reportMeta['type'] }}</strong></div>
            <div>Periode: <strong>{{ $reportMeta['start_date']->format('d M Y') }} s/d {{ $reportMeta['end_date']->format('d M Y') }}</strong></div>
            <div>Di-generate Oleh: <strong>{{ $reportMeta['generator_name'] }}</strong></div>
            <div>Waktu Cetak: <strong>{{ $reportMeta['created_at']->format('d M Y H:i:s') }}</strong></div>
        </div>
    </div>

    <!-- Summary KPIs Box -->
    @if (isset($reportData['kpis']))
        <div class="summary-box">
            <div class="summary-title">Executive Summary & Key Performance Indicators (KPIs)</div>
            <div class="summary-grid">
                @foreach ($reportData['kpis'] as $kpi)
                    <div class="summary-item">
                        <span class="summary-item-label">{{ $kpi['label'] }}</span>
                        <span class="summary-item-value">{{ $kpi['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Detailed Table Section -->
    <div class="section-title">Detail Rincian Transaksi / Rekapitulasi Data</div>
    <table>
        <thead>
            @if ($reportMeta['type'] === 'SALES')
                <tr>
                    <th>Order #</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Produk</th>
                    <th>SKU</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Subtotal</th>
                    <th>Status</th>
                </tr>
            @elseif ($reportMeta['type'] === 'ORDERS')
                <tr>
                    <th>Order #</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th class="text-center">Total Item</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-right">Ongkir</th>
                    <th class="text-right">Diskon</th>
                    <th class="text-right">Total Bayar</th>
                    <th>Status</th>
                </tr>
            @elseif ($reportMeta['type'] === 'INVENTORY')
                <tr>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th class="text-right">Harga Jual</th>
                    <th class="text-center">Stok</th>
                    <th class="text-right">Nilai Aset</th>
                    <th>Status</th>
                    <th>Update Terakhir</th>
                </tr>
            @elseif ($reportMeta['type'] === 'PAYMENTS')
                <tr>
                    <th>ID</th>
                    <th>Order #</th>
                    <th>Gateway Provider</th>
                    <th>Kode Referensi</th>
                    <th class="text-right">Nominal</th>
                    <th>Status</th>
                    <th>Waktu Lunas</th>
                </tr>
            @elseif ($reportMeta['type'] === 'CUSTOMERS')
                <tr>
                    <th>ID</th>
                    <th>Nama Customer</th>
                    <th>Email</th>
                    <th class="text-center">Total Transaksi</th>
                    <th>Terdaftar Sejak</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @if ($reportMeta['type'] === 'SALES')
                @forelse ($detailRecords as $item)
                    <tr>
                        <td><strong>{{ $item->order->order_number ?? 'N/A' }}</strong></td>
                        <td>{{ $item->order->created_at ? $item->order->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $item->order->user->name ?? 'Guest/Deleted' }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td><code>{{ $item->sku }}</code></td>
                        <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right"><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                        <td>{{ $item->order->status ?? 'UNKNOWN' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center">Tidak ada data penjualan pada periode ini.</td></tr>
                @endforelse

            @elseif ($reportMeta['type'] === 'ORDERS')
                @forelse ($detailRecords as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $order->user->name ?? 'Deleted User' }}</td>
                        <td class="text-center">{{ $order->orderItems->sum('quantity') }}</td>
                        <td class="text-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                        <td class="text-right"><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                        <td>{{ $order->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center">Tidak ada order pada periode ini.</td></tr>
                @endforelse

            @elseif ($reportMeta['type'] === 'INVENTORY')
                @forelse ($detailRecords as $var)
                    <tr>
                        <td><code>{{ $var->sku }}</code></td>
                        <td><strong>{{ $var->product->name ?? 'N/A' }}</strong></td>
                        <td class="text-right">Rp {{ number_format($var->price, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $var->stock }}</td>
                        <td class="text-right">Rp {{ number_format($var->stock * $var->price, 0, ',', '.') }}</td>
                        <td>{{ $var->stock == 0 ? 'OUT OF STOCK' : ($var->stock <= 5 ? 'LOW STOCK' : 'IN STOCK') }}</td>
                        <td>{{ $var->updated_at ? $var->updated_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Tidak ada data inventaris.</td></tr>
                @endforelse

            @elseif ($reportMeta['type'] === 'PAYMENTS')
                @forelse ($detailRecords as $pay)
                    <tr>
                        <td>#{{ $pay->id }}</td>
                        <td><strong>{{ $pay->order->order_number ?? 'N/A' }}</strong></td>
                        <td>{{ strtoupper($pay->provider) }}</td>
                        <td><code>{{ $pay->provider_reference }}</code></td>
                        <td class="text-right">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                        <td>{{ $pay->status }}</td>
                        <td>{{ $pay->paid_at ? $pay->paid_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Tidak ada transaksi pembayaran pada periode ini.</td></tr>
                @endforelse

            @elseif ($reportMeta['type'] === 'CUSTOMERS')
                @forelse ($detailRecords as $cust)
                    <tr>
                        <td>#{{ $cust->id }}</td>
                        <td><strong>{{ $cust->name }}</strong></td>
                        <td>{{ $cust->email }}</td>
                        <td class="text-center">{{ $cust->orders_count }} order</td>
                        <td>{{ $cust->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Tidak ada data customer.</td></tr>
                @endforelse
            @endif
        </tbody>
    </table>

    <!-- Document Footer -->
    <div class="doc-footer">
        <div>Dokumen ini di-generate otomatis oleh Sistem Laporan & BI E-Commerce.</div>
        <div>Halaman Cetak — Tanggal {{ date('d F Y') }}</div>
    </div>

</body>
</html>
