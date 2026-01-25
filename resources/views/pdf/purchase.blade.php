<!DOCTYPE html>
<html>
<head>
    <title>Purchase {{ $purchase_bill->bill_no }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        h2, h3 { margin: 0; }
        .header, .totals { width: 100%; margin-bottom: 20px; }
        .header div { display: inline-block; width: 48%; vertical-align: top; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <h2>Purchase Bill: {{ $purchase_bill->bill_no }}</h2>
            <p><strong>Bill Date:</strong> {{ $purchase_bill->bill_date }}</p>
        </div>
        <div style="text-align: right;">
            <h3>{{ config('app.name') }}</h3>
            <p>{{ $setting->company_name ?? 'Company Name' }}</p>
            <p>{{ $setting->address ?? 'Company Address' }}</p>
            <p>GST/VAT: {{ $setting->gst_number ?? '-' }}</p>
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <p><strong>Vendor:</strong> {{ $purchase_bill->vendor->name }}</p>
        <p><strong>Phone:</strong> {{ $purchase_bill->vendor->phone ?? '-' }}</p>
        <p><strong>Created At:</strong> {{ $purchase_bill->created_at ?? '-' }}</p>
        <p><strong>GST:</strong> {{ $purchase_bill->vendor->gst_number ?? '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Tax</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase_bill->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price,2) }}</td>
                <td>{{ number_format($item->tax_amount,2) }}</td>
                <td>{{ number_format($item->total,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals" style="margin-top: 20px;">
        <p class="text-right"><strong>Subtotal:</strong> {{ number_format($purchase_bill->subtotal,2) }}</p>
        <p class="text-right"><strong>Tax:</strong> {{ number_format($purchase_bill->tax_total,2) }}</p>
        <p class="text-right"><strong>Discount:</strong> {{ number_format($purchase_bill->discount,2) }}</p>
        <p class="text-right" style="font-size: 16px;"><strong>Grand Total:</strong> {{ number_format($purchase_bill->grand_total,2) }}</p>
    </div>

    <div style="margin-top: 40px; text-align: center;">
        <p>Thank you for your business!</p>
    </div>

</body>
</html>
