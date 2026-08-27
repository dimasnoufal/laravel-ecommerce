<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }} - {{ config('app.name', 'E-Commerce') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-body: #f8fafc;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        body {
            background: #e2e8f0;
            color: var(--text-main);
            padding: 2rem 1rem;
            display: flex;
            justify-content: center;
        }
        .invoice-wrapper {
            background: #ffffff;
            width: 100%;
            max-width: 800px;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: relative;
        }
        .no-print-bar {
            position: fixed;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: 0.5rem;
            z-index: 999;
        }
        .btn-print {
            background: var(--primary);
            color: #ffffff;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(37, 99, 235, 0.3);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .btn-close-inv {
            background: #ffffff;
            color: var(--text-main);
            border: 1px solid var(--border-color);
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            text-decoration: none;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }
        .brand-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.02em;
        }
        .brand-sub {
            font-size: 0.8125rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
            line-height: 1.4;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.02em;
        }
        .invoice-number {
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 0.2rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .info-box h4 {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        .info-box p {
            font-size: 0.875rem;
            color: var(--text-main);
            line-height: 1.45;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        .invoice-table th {
            background: var(--bg-body);
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 1px solid var(--border-color);
        }
        .invoice-table td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
            color: var(--text-main);
        }
        .invoice-table td.text-right,
        .invoice-table th.text-right {
            text-align: right;
        }
        .totals-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .stamp-box {
            border: 2px dashed #16a34a;
            color: #16a34a;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            display: inline-block;
            transform: rotate(-5deg);
        }
        .stamp-box.unpaid {
            border-color: #eab308;
            color: #ca8a04;
        }
        .stamp-box.cancelled {
            border-color: #dc2626;
            color: #dc2626;
        }
        .totals-table {
            width: 320px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 0.4rem 0;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        .totals-table td.val {
            text-align: right;
            font-weight: 600;
            color: var(--text-main);
        }
        .totals-table tr.grand-total td {
            padding-top: 0.75rem;
            border-top: 2px solid var(--border-color);
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-main);
        }
        .totals-table tr.grand-total td.val {
            color: var(--primary);
        }
        .invoice-footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
            text-align: center;
            font-size: 0.78125rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .invoice-wrapper {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="javascript:window.close()" class="btn-close-inv">Tutup</a>
        <button type="button" class="btn-print" onclick="window.print()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <div class="invoice-wrapper">
        <!-- Header -->
        <div class="invoice-header">
            <div>
                <div class="brand-title">{{ config('app.name', 'Laravel E-Commerce') }}</div>
                <div class="brand-sub">
                    Pusat Perbelanjaan Online Resmi & Terpercaya<br>
                    support@ecommerce.local • +62 812-3456-7890
                </div>
            </div>
            <div class="invoice-meta">
                <div class="invoice-title">FAKTUR PENJUALAN</div>
                <div class="invoice-number">#{{ $order->order_number }}</div>
                <div style="font-size: 0.78125rem; color: var(--text-muted); margin-top: 0.35rem;">
                    Tanggal: {{ ($order->placed_at ?? $order->created_at)->format('d F Y') }}
                </div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="info-box">
                <h4>Ditagihkan Kepada:</h4>
                <p>
                    <strong>{{ $order->user?->name ?? 'Pelanggan' }}</strong><br>
                    {{ $order->user?->email ?? '-' }}<br>
                    {{ $order->user?->phone ?? '-' }}
                </p>
            </div>
            <div class="info-box">
                <h4>Alamat Pengiriman:</h4>
                @if ($order->address)
                    <p>
                        <strong>{{ $order->address->recipient_name }}</strong> ({{ $order->address->phone }})<br>
                        {{ $order->address->address_line }}<br>
                        {{ $order->address->village_name ? $order->address->village_name . ', ' : '' }}
                        {{ $order->address->district_name ? $order->address->district_name . ', ' : '' }}
                        {{ $order->address->regency_name ? $order->address->regency_name . ', ' : '' }}
                        {{ $order->address->province_name }} {{ $order->address->postal_code }}
                    </p>
                @else
                    <p style="color: var(--text-muted);">-</p>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th>Nama Produk & SKU</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right" style="width: 70px;">Jumlah</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $index => $item)
                    <tr>
                        <td style="text-align: center; color: var(--text-muted);">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->product_name }}</strong><br>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">SKU: {{ $item->sku }}</span>
                        </td>
                        <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right"><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals & Payment Stamp -->
        <div class="totals-section">
            <div>
                @php
                    $isPaid = ($order->payment && in_array($order->payment->status, ['PAID', 'SETTLEMENT', 'CAPTURE']));
                    $isCancelled = ($order->status instanceof \App\Enums\OrderStatus ? $order->status->value : (string) $order->status) === 'CANCELLED';
                @endphp
                @if ($isCancelled)
                    <div class="stamp-box cancelled">DIBATALKAN</div>
                @elseif ($isPaid)
                    <div class="stamp-box">LUNAS / PAID</div>
                @else
                    <div class="stamp-box unpaid">MENUNGGU BAYAR</div>
                @endif

                @if ($order->payment)
                    <div style="font-size: 0.78125rem; color: var(--text-muted); margin-top: 0.85rem; line-height: 1.4;">
                        Metode Pembayaran: <strong>{{ strtoupper($order->payment->provider) }}</strong><br>
                        Ref: <code style="font-family: monospace;">{{ $order->payment->provider_reference ?? '-' }}</code>
                    </div>
                @endif
            </div>

            <table class="totals-table">
                <tr>
                    <td>Subtotal Produk:</td>
                    <td class="val">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Ongkos Kirim:</td>
                    <td class="val">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                </tr>
                @if ($order->discount_amount > 0)
                    <tr>
                        <td>Diskon / Potongan:</td>
                        <td class="val" style="color: #16a34a;">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="grand-total">
                    <td>Total Tagihan:</td>
                    <td class="val">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            Terima kasih telah berbelanja bersama kami.<br>
            Faktur ini sah dan diproses secara otomatis oleh sistem komputer.
        </div>
    </div>

</body>
</html>
