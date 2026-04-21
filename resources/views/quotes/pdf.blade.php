<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote — {{ $quote->client_name ?? 'Draft' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }

        .page { padding: 40px; }

        /* ── Header ─────────────────────────────────────────── */
        .header {
            border-bottom: 3px solid #4361ee;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .header-top {
            display: table;
            width: 100%;
        }
        .header-brand { display: table-cell; vertical-align: middle; }
        .header-meta  { display: table-cell; vertical-align: middle; text-align: right; }

        .brand-name {
            font-size: 22px;
            font-weight: bold;
            color: #4361ee;
        }
        .brand-sub { color: #888; font-size: 10px; }

        .doc-title {
            font-size: 26px;
            font-weight: bold;
            color: #1a1a2e;
        }
        .doc-ref { color: #888; font-size: 10px; margin-top: 2px; }

        /* ── Client / Info Block ────────────────────────────── */
        .info-table { width: 100%; margin-bottom: 24px; }
        .info-table td { vertical-align: top; padding: 0; width: 50%; }

        .info-block { background: #f8f9fa; border-radius: 4px; padding: 12px 16px; }
        .info-block .label { color: #888; font-size: 9px; text-transform: uppercase; letter-spacing: .5px; }
        .info-block .value { font-size: 12px; font-weight: bold; color: #1a1a2e; margin-top: 1px; }
        .info-block .sub   { color: #666; font-size: 10px; }

        /* ── Section heading ────────────────────────────────── */
        .section-title {
            background: #4361ee;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            padding: 6px 12px;
            margin-bottom: 0;
            border-radius: 3px 3px 0 0;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* ── Line items table ───────────────────────────────── */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .items-table thead th {
            background: #f0f2ff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #555;
            padding: 6px 8px;
            border-bottom: 1px solid #dde0f5;
            text-align: left;
        }
        .items-table thead th.num { text-align: right; }
        .items-table tbody tr { border-bottom: 1px solid #f0f0f0; }
        .items-table tbody tr:last-child { border-bottom: none; }
        .items-table tbody td {
            padding: 7px 8px;
            vertical-align: top;
            font-size: 10.5px;
        }
        .items-table tbody td.num { text-align: right; }
        .items-table .item-name { font-weight: bold; }
        .items-table .item-notes { color: #888; font-size: 9.5px; margin-top: 1px; }
        .items-table .type-badge {
            display: inline-block;
            background: #eef0ff;
            color: #4361ee;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 9px;
        }
        .items-table .plugin-badge {
            display: inline-block;
            background: #e8f8ee;
            color: #198754;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 9px;
        }
        .items-table .phase-subtotal td {
            background: #f8f9fa;
            font-weight: bold;
            font-size: 10px;
            padding: 5px 8px;
            border-top: 1px solid #e0e0e0;
        }

        /* ── Totals block ───────────────────────────────────── */
        .totals-wrapper { margin-top: 6px; }
        .totals-table {
            width: 280px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px 10px;
            font-size: 10.5px;
        }
        .totals-table .label-col { color: #666; }
        .totals-table .value-col { text-align: right; font-weight: bold; color: #1a1a2e; }
        .totals-table .divider td { border-top: 1px solid #ddd; }
        .totals-table .grand-total td {
            background: #4361ee;
            color: #fff;
            font-size: 13px;
            font-weight: bold;
            padding: 8px 10px;
        }
        .totals-table .grand-total .value-col { text-align: right; }

        /* ── Footer ─────────────────────────────────────────── */
        .footer {
            border-top: 1px solid #e0e0e0;
            margin-top: 32px;
            padding-top: 10px;
            text-align: center;
            color: #aaa;
            font-size: 9px;
        }

        /* ── Utility ─────────────────────────────────────────── */
        .spacer { height: 12px; }
        .text-muted { color: #888; }
    </style>
</head>
<body>
<div class="page">

    {{-- ── HEADER ─────────────────────────────────────── --}}
    <div class="header">
        <div class="header-top">
            <div class="header-brand">
                <div class="brand-name">{{ config('app.name', 'Quote Calculator') }}</div>
                <div class="brand-sub">Professional Website Quotation</div>
            </div>
            <div class="header-meta">
                <div class="doc-title">QUOTE</div>
                <div class="doc-ref">
                    Ref: QUO-{{ str_pad($quote->id, 4, '0', STR_PAD_LEFT) }}
                    &nbsp;|&nbsp;
                    {{ $quote->created_at->format('d F Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- ── CLIENT / SALESPERSON INFO ───────────────────── --}}
    <table class="info-table">
        <tr>
            <td style="padding-right:12px;">
                <div class="info-block">
                    <div class="label">Prepared for</div>
                    <div class="value">{{ $quote->client_name ?? '—' }}</div>
                </div>
            </td>
            <td style="padding-left:12px;">
                <div class="info-block">
                    <div class="label">Prepared by</div>
                    <div class="value">{{ $quote->salesperson_name ?? '—' }}</div>
                    @if($quote->salesperson_email)
                        <div class="sub">{{ $quote->salesperson_email }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- ── PHASES ──────────────────────────────────────── --}}
    @foreach($quote->phases as $phase)
        @if($phase->lineItems->isNotEmpty())
            <div class="section-title">
                @if($phase->type === 'design') Design
                @elseif($phase->type === 'development') Development
                @else Plugins &amp; Project Management
                @endif
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:35%;">Item</th>
                        <th style="width:10%;">Type</th>
                        <th class="num" style="width:11%;">Rate</th>
                        <th class="num" style="width:7%;">Qty</th>
                        <th class="num" style="width:8%;">%</th>
                        <th style="width:7%;">Curr</th>
                        <th style="width:7%;">Plugin</th>
                        <th class="num" style="width:15%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($phase->lineItems as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->name }}</div>
                            @if($item->notes)
                                <div class="item-notes">{{ $item->notes }}</div>
                            @endif
                        </td>
                        <td><span class="type-badge">{{ $item->calculation_type }}</span></td>
                        <td class="num">
                            @if($item->rate)
                                {{ format_money($item->rate, $item->currency) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="num">{{ !in_array($item->calculation_type, ['fixed','percentage']) ? $item->quantity : '—' }}</td>
                        <td class="num">{{ $item->percentage_value ? $item->percentage_value . '%' : '—' }}</td>
                        <td>{{ $item->currency ?? 'ZAR' }}</td>
                        <td>{{ $item->is_plugin ? '<span class="plugin-badge">Yes</span>' : '' }}</td>
                        <td class="num"><strong>{{ format_money($item->total) }}</strong></td>
                    </tr>
                    @endforeach
                    <tr class="phase-subtotal">
                        <td colspan="7">Phase Subtotal</td>
                        <td class="num">{{ format_money($phase->lineItems->sum('total')) }}</td>
                    </tr>
                </tbody>
            </table>
        @endif
    @endforeach

    {{-- ── TOTALS ───────────────────────────────────────── --}}
    <div class="totals-wrapper">
        <table class="totals-table">
            <tr>
                <td class="label-col">Subtotal</td>
                <td class="value-col">{{ format_money($quote->subtotal) }}</td>
            </tr>
            <tr>
                <td class="label-col">Main Items Total</td>
                <td class="value-col">{{ format_money($quote->main_total) }}</td>
            </tr>
            <tr>
                <td class="label-col">Plugin Total</td>
                <td class="value-col">{{ format_money($quote->plugin_total) }}</td>
            </tr>
            @if($quote->markup_amount > 0)
            <tr>
                <td class="label-col">Markup ({{ $quote->markup_rate }}%)</td>
                <td class="value-col">{{ format_money($quote->markup_amount) }}</td>
            </tr>
            @endif
            <tr class="divider">
                <td class="label-col">Total ex VAT</td>
                <td class="value-col">{{ format_money($quote->total_ex_vat) }}</td>
            </tr>
            <tr>
                <td class="label-col">VAT (15%)</td>
                <td class="value-col">{{ $quote->apply_vat ? format_money($quote->vat) : 'Excl.' }}</td>
            </tr>
            <tr class="grand-total">
                <td class="label-col">TOTAL {{ $quote->apply_vat ? 'INC VAT' : 'EX VAT' }}</td>
                <td class="value-col">{{ format_money($quote->total_inc_vat) }}</td>
            </tr>
        </table>
    </div>

    {{-- ── FOOTER ──────────────────────────────────────── --}}
    <div class="footer">
        This quote is valid for 30 days from the date of issue.
        &nbsp;|&nbsp;
        Generated {{ now()->format('d M Y H:i') }}
        &nbsp;|&nbsp;
        {{ config('app.name') }}
    </div>

</div>
</body>
</html>
