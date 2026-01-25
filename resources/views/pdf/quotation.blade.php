<!DOCTYPE html>
<html>
<head>
    <title>Quotation {{ $quotation->quotation_no }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: center; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Quotation: {{ $quotation->quotation_no }}</h2>
    <p>Customer: {{ $quotation->customer->name }}</p>
    <p>Date: {{ $quotation->quotation_date }}</p>

    <table>
        <thead>
            <tr>
                <th>Product</th><th>Qty</th><th>Price</th><th>Tax</th><th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->tax_amount }}</td>
                <td>{{ $item->total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p>Subtotal: {{ $quotation->subtotal }}</p>
    <p>Tax: {{ $quotation->tax_total }}</p>
    <p>Discount: {{ $quotation->discount }}</p>
    <p>Grand Total: {{ $quotation->grand_total }}</p>
</body>
</html>
