<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan iStore</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #18181b;
            line-height: 1.45;
            margin: 24px;
        }
        .header {
            border-bottom: 1.5px solid #27272a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .brand {
            font-size: 18px;
            font-weight: bold;
            color: #09090b;
        }
        .subtitle {
            font-size: 11px;
            color: #52525b;
            margin-top: 2px;
        }
        .meta {
            margin-top: 8px;
            font-size: 10px;
            color: #3f3f46;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .summary-table td {
            width: 50%;
            border: 1px solid #d4d4d8;
            background-color: #fafafa;
            padding: 10px 12px;
            vertical-align: top;
        }
        .summary-label {
            font-size: 10px;
            color: #52525b;
        }
        .summary-value {
            font-size: 15px;
            font-weight: bold;
            color: #09090b;
            margin-top: 4px;
        }
        .note {
            border: 1px solid #d4d4d8;
            background-color: #f4f4f5;
            padding: 8px 12px;
            font-size: 10px;
            color: #27272a;
            margin-bottom: 16px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th {
            background-color: #18181b;
            color: #ffffff;
            font-size: 10px;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #18181b;
        }
        table.data-table td {
            padding: 7px 10px;
            border: 1px solid #d4d4d8;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            border-top: 1px solid #d4d4d8;
            padding-top: 10px;
            font-size: 9px;
            color: #71717a;
        }
    </style>
</head>
<body>
    @php
        $startDate = $startDate ?? ($start_date ?? request('start_date'));
        $endDate = $endDate ?? ($end_date ?? request('end_date'));
        $verifiedRevenue = $verifiedRevenue ?? ($total_revenue ?? ($summary['verified_revenue'] ?? 0));
        $paidOrdersCount = $paidOrdersCount ?? ($total_orders ?? ($summary['paid_orders_count'] ?? 0));
        $items = $transactions ?? ($orders ?? ($reports ?? collect()));
    @endphp

    <div class="header">
        <div class="brand">iStore — Laporan Penjualan Terverifikasi</div>
        <div class="subtitle">Ringkasan transaksi dengan status pembayaran Paid</div>
        <div class="meta">
            Periode:
            {{ !empty($startDate) ? \Illuminate\Support\Carbon::parse($startDate)->format('d M Y') : 'Awal' }}
            s.d.
            {{ !empty($endDate) ? \Illuminate\Support\Carbon::parse($endDate)->format('d M Y') : 'Sekarang' }}
            &nbsp;|&nbsp;
            Dicetak pada: {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-label">Omzet Terverifikasi (Paid)</div>
                <div class="summary-value">Rp {{ number_format((float) $verifiedRevenue, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="summary-label">Jumlah Transaksi Paid</div>
                <div class="summary-value">{{ number_format((int) $paidOrdersCount, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <div class="note">
        <strong>Catatan:</strong> Nilai omzet pada laporan ini hanya menghitung transaksi dengan status pembayaran <strong>Paid</strong> sesuai data layanan laporan sistem.
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nomor Pesanan</th>
                <th>Tanggal</th>
                <th>Pembeli</th>
                <th>Status Pembayaran</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $row)
                @php
                    $orderCode = $row->order_code ?? ($row->order_number ?? ($row->code ?? ('#' . $row->id)));
                    $txDate = $row->payment?->paid_at ?? ($row->paid_at ?? ($row->created_at ?? null));
                    $buyerName = $row->recipient_name ?? ($row->buyer_name ?? ($row->user->name ?? ($row->customer_name ?? 'Pembeli')));
                    $amount = $row->total_amount ?? ($row->amount ?? ($row->total ?? 0));
                    $rawStatus = $row->payment_status ?? ($row->payment?->status ?? ($row->status ?? 'Paid'));
                    $statusLabel = $rawStatus instanceof \BackedEnum ? $rawStatus->value : (string) $rawStatus;
                @endphp
                <tr>
                    <td>{{ $orderCode }}</td>
                    <td>{{ $txDate ? \Illuminate\Support\Carbon::parse($txDate)->format('d M Y H:i') : '-' }}</td>
                    <td>{{ $buyerName }}</td>
                    <td>{{ strtoupper($statusLabel) }}</td>
                    <td class="text-right">Rp {{ number_format((float) $amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #71717a; padding: 14px;">
                        Tidak ada transaksi Paid pada periode laporan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        iStore merupakan peritel independen dan tidak berafiliasi secara resmi dengan Apple Inc.
    </div>
</body>
</html>