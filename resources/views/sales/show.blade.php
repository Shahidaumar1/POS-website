<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Receipt #{{ $sale->id }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; font-family: 'Courier New', monospace; min-height: 100vh; }
        .receipt { max-width: 450px; margin: 20px auto; background: #fef9f3; padding: 25px; border-radius: 10px; box-shadow: 0 8px 20px rgba(0,0,0,0.3); font-size: 13px; border: 2px solid #333; }
        .header { text-align: center; border-bottom: 3px solid #333; padding-bottom: 12px; margin-bottom: 15px; }
        .header h1 { font-size: 20px; font-weight: bold; color: #000; }
        .header p { font-size: 12px; color: #333; margin: 4px 0; }
        .barcode-container { text-align: center; margin: 15px 0; padding: 12px; border: 2px solid #333; background: white; border-radius: 5px; }
        .barcode-container svg { max-width: 300px; height: 50px; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; color: #000; border-bottom: 1px dotted #999; }
        .info-label { font-weight: bold; color: #000; }
        .items-table { width: 100%; margin: 15px 0; border-collapse: collapse; }
        .items-table th { border: 1px solid #333; padding: 8px 4px; text-align: left; font-weight: bold; font-size: 12px; background: #e8d7c3; color: #000; }
        .items-table td { padding: 8px 4px; border: 1px solid #ddd; font-size: 12px; color: #000; background: white; }
        .items-table .qty { text-align: center; }
        .items-table .price { text-align: right; }
        .items-table .total { text-align: right; font-weight: bold; }
        .separator { border-bottom: 3px solid #333; margin: 12px 0; }
        .totals { margin: 12px 0; background: #f5f0eb; padding: 10px; border-radius: 5px; }
        .total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; color: #000; }
        .total-row.grand { font-weight: bold; font-size: 18px; border-top: 2px solid #333; padding-top: 10px; margin-top: 8px; color: #000; }
        .footer { text-align: center; font-size: 11px; color: #333; margin-top: 15px; line-height: 1.6; border-top: 1px dotted #999; padding-top: 10px; }
        .buttons { text-align: center; margin-top: 20px; display: flex; gap: 10px; justify-content: center; }
        .btn { padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; transition: all 0.3s; }
        .btn-print { background: #007bff; color: white; }
        .btn-print:hover { background: #0056b3; transform: translateY(-2px); }
        .btn-back { background: #6c757d; color: white; }
        .btn-back:hover { background: #545b62; transform: translateY(-2px); }
        
        @media print {
            body { background: white; }
            .buttons { display: none; }
            .receipt { box-shadow: none; max-width: 100%; border: 1px solid #000; margin: 0; }
        }
    </style>
</head>
<body>
<div class="receipt">
    <!-- Header -->
    <div class="header">
        <h1>📋 POS RECEIPT</h1>
        <p>Sale ID: #{{ $sale->id }}</p>
        <p>{{ $sale->created_at->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Barcode -->
    <div class="barcode-container">
        <svg id="saleBarcodeContainer"></svg>
        <script>
            JsBarcode("#saleBarcodeContainer", "SALE{{ $sale->id }}", {
                format: "CODE128",
                width: 2,
                height: 40,
                displayValue: true,
                fontSize: 10
            });
        </script>
    </div>

    <!-- Customer Info -->
    <div style="margin-bottom: 15px; font-size: 11px;">
        <div class="info-row">
            <span class="info-label">Customer:</span>
            <span>{{ $sale->user->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment:</span>
            <span>
                @if($sale->payment_method === 'cash') Cash
                @elseif($sale->payment_method === 'card') Card
                @elseif($sale->payment_method === 'paypal') PayPal
                @else Stripe @endif
            </span>
        </div>
    </div>

    <div class="separator"></div>

    <!-- Items -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="qty">Qty</th>
                <th class="price">Price</th>
                <th class="total">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td>{{ substr($item->product->name, 0, 15) }}</td>
                    <td class="qty">{{ $item->quantity }}</td>
                    <td class="price">£{{ number_format($item->price, 2) }}</td>
                    <td class="total">£{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="separator"></div>

    <!-- Totals -->
    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>£{{ number_format($sale->total_amount, 2) }}</span>
        </div>
        <div class="total-row">
            <span>Tax:</span>
            <span>£0.00</span>
        </div>
        <div class="total-row grand">
            <span>TOTAL:</span>
            <span>£{{ number_format($sale->total_amount, 2) }}</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>Please keep this receipt for your records.</p>
        <p style="margin-top: 10px; font-size: 10px; color: #999;">
            System: POS Terminal | {{ date('Y-m-d H:i:s') }}
        </p>
    </div>

    <!-- Buttons -->
    <div class="buttons">
        <button class="btn btn-print" onclick="window.print()">🖨️ Print</button>
        <button class="btn btn-back" onclick="window.history.back()">← Back</button>
    </div>
</div>
</body>
</html>
