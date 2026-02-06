<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoiceNumber }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000000 !important;
            line-height: 1.6;
            direction: ltr;
            background: #ffffff;
        }
        
        * {
            font-family: Arial, Helvetica, sans-serif !important;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
        }
        
        .header {
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #4f46e5;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .invoice-info-row {
            display: table-row;
        }
        
        .invoice-info-cell {
            display: table-cell;
            padding: 8px 0;
            width: 50%;
        }
        
        .invoice-info-label {
            font-weight: bold;
            color: #666;
        }
        
        .invoice-info-value {
            color: #333;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            background: #f3f4f6;
            padding: 10px 15px;
            font-weight: bold;
            color: #000000 !important;
            border-left: 4px solid #4f46e5;
            margin-bottom: 15px;
        }
        
        .customer-info, .order-info {
            padding: 15px;
            background: #f9fafb;
            border-radius: 5px;
        }
        
        .info-row {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #000000 !important;
            display: inline-block;
            width: 120px;
        }
        
        .info-value {
            color: #000000 !important;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        table thead {
            background: #4f46e5;
            color: #fff;
        }
        
        table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        table tbody tr:hover {
            background: #f9fafb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals {
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 5px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
            color: #000000 !important;
        }
        
        .total-row:last-child {
            border-bottom: none;
            font-size: 16px;
            font-weight: bold;
            color: #000000 !important;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #4f46e5;
        }
        
        .total-label {
            font-weight: bold;
            color: #000000 !important;
        }
        
        .total-row span {
            color: #000000 !important;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 11px;
        }
        
        .options-list {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Order Items -->
        <div class="section">
            <div class="section-title">Order Items</div>
            @foreach($order->items as $index => $item)
            <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #e5e7eb; border-radius: 5px; background: #ffffff;">
                <div style="font-size: 16px; font-weight: bold; margin-bottom: 10px; color: #000000 !important;">
                    {{ $item->design->name ?? 'Design not specified' }}
                </div>
                <div style="font-size: 14px; color: #000000 !important; margin-bottom: 8px;">
                    {{ number_format($item->price, 2) }} SAR
                </div>
                <div style="font-size: 14px; color: #000000 !important; margin-bottom: 8px;">
                    {{ number_format($item->price, 2) }} × {{ $item->quantity }}
                </div>
                
                @php
                    $hasOptions = false;
                    $formattedOptions = $item->formatted_options ?? [];
                    if (is_array($formattedOptions) && !empty($formattedOptions)) {
                        $hasOptions = true;
                    }
                    if ($item->size) {
                        $hasOptions = true;
                    }
                @endphp
                
                @if($hasOptions)
                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
                    <div style="font-size: 12px; font-weight: bold; margin-bottom: 8px; color: #000000 !important;">Selected Options:</div>
                    
                    @if($item->size)
                    <div style="font-size: 12px; margin-bottom: 5px; color: #000000 !important;">
                        <strong style="color: #000000 !important;">Size:</strong> <span style="color: #000000 !important;">{{ $item->size->name ?? 'Not specified' }}@if($item->size->code ?? null) ({{ $item->size->code }})@endif</span>
                    </div>
                    @endif
                    
                    @if(is_array($formattedOptions) && !empty($formattedOptions))
                        @foreach($formattedOptions as $type => $options)
                            @if($type !== 'size' && is_array($options) && count($options) > 0)
                                @php
                                    $optionNames = [];
                                    foreach($options as $option) {
                                        if (is_array($option) && isset($option['name'])) {
                                            $optionNames[] = $option['name'];
                                        } elseif (is_string($option)) {
                                            $optionNames[] = $option;
                                        }
                                    }
                                @endphp
                                @if(!empty($optionNames))
                                <div style="font-size: 12px; margin-bottom: 5px; color: #000000 !important;">
                                    <strong style="color: #000000 !important;">{{ ucfirst(str_replace('_', ' ', $type)) }}:</strong> <span style="color: #000000 !important;">{{ implode(', ', $optionNames) }}</span>
                                </div>
                                @endif
                            @endif
                        @endforeach
                    @endif
                    
                    <div style="font-size: 12px; margin-bottom: 5px; margin-top: 8px; color: #000000 !important;">
                        <strong style="color: #000000 !important;">Price:</strong> <span style="color: #000000 !important;">{{ number_format($item->price, 2) }} SAR</span>
                    </div>
                    <div style="font-size: 12px; color: #000000 !important;">
                        <strong style="color: #000000 !important;">Quantity:</strong> <span style="color: #000000 !important;">{{ $item->quantity }}</span>
                    </div>
                </div>
                @else
                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
                    <div style="font-size: 12px; margin-bottom: 5px; color: #000000 !important;">
                        <strong style="color: #000000 !important;">Price:</strong> <span style="color: #000000 !important;">{{ number_format($item->price, 2) }} SAR</span>
                    </div>
                    <div style="font-size: 12px; color: #000000 !important;">
                        <strong style="color: #000000 !important;">Quantity:</strong> <span style="color: #000000 !important;">{{ $item->quantity }}</span>
                    </div>
                    @if($item->size)
                    <div style="font-size: 12px; margin-top: 5px; color: #000000 !important;">
                        <strong style="color: #000000 !important;">Size:</strong> <span style="color: #000000 !important;">{{ $item->size->name ?? 'Not specified' }}@if($item->size->code ?? null) ({{ $item->size->code }})@endif</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Payment Summary -->
        <div class="section">
            <div class="section-title">Payment Summary</div>
            <div class="totals" style="color: #000000 !important;">
                <div class="total-row" style="color: #000000 !important;">
                    <span class="total-label" style="color: #000000 !important;">Subtotal</span>
                    <span style="color: #000000 !important;">{{ number_format($order->subtotal ?? $order->items->sum('subtotal'), 2) }} SAR</span>
                </div>
                <div class="total-row" style="color: #000000 !important;">
                    <span class="total-label" style="color: #000000 !important;">Shipping</span>
                    <span style="color: #000000 !important;">{{ number_format($order->shipping ?? 0, 2) }} SAR</span>
                </div>
                <div class="total-row" style="color: #000000 !important;">
                    <span class="total-label" style="color: #000000 !important;">Total</span>
                    <span style="color: #000000 !important;">{{ number_format($order->total_amount, 2) }} SAR</span>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="section">
            <div class="section-title">Customer Information</div>
            <div class="customer-info" style="color: #000000 !important;">
                <div class="info-row" style="color: #000000 !important;">
                    <span class="info-label" style="color: #000000 !important;">Name:</span>
                    <span class="info-value" style="color: #000000 !important;">{{ $order->user->name }}</span>
                </div>
                <div class="info-row" style="color: #000000 !important;">
                    <span class="info-label" style="color: #000000 !important;">Customer Since:</span>
                    <span class="info-value" style="color: #000000 !important;">{{ $order->user->created_at->format('Y') }}</span>
                </div>
                <div class="info-row" style="color: #000000 !important;">
                    <span class="info-label" style="color: #000000 !important;">Email:</span>
                    <span class="info-value" style="color: #000000 !important;">{{ $order->user->email }}</span>
                </div>
                @if($order->user->phone)
                <div class="info-row" style="color: #000000 !important;">
                    <span class="info-label" style="color: #000000 !important;">Phone:</span>
                    <span class="info-value" style="color: #000000 !important;">{{ $order->user->phone }}</span>
                </div>
                @endif
                <div class="info-row" style="color: #000000 !important;">
                    <span class="info-label" style="color: #000000 !important;">Total Orders:</span>
                    <span class="info-value" style="color: #000000 !important;">{{ $order->user->orders()->count() }} order</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
