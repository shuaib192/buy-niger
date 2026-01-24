{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    Email: New Order Notification - Sent to Vendor
--}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background:#f8fafc; font-family:'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:white; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg, #10b981 0%, #059669 100%); padding:32px; text-align:center;">
                            <h1 style="color:white; margin:0; font-size:24px; font-weight:700;">🎉 New Order!</h1>
                        </td>
                    </tr>
                    
                    <!-- Greeting -->
                    <tr>
                        <td style="padding:40px 40px 20px;">
                            <h2 style="margin:0 0 8px; font-size:20px; color:#1e293b;">Hi {{ $vendor->business_name }},</h2>
                            <p style="margin:0; color:#64748b;">You have received a new order on BuyNiger!</p>
                        </td>
                    </tr>

                    <!-- Order Info -->
                    <tr>
                        <td style="padding:20px 40px;">
                            <table width="100%" style="background:#f0fdf4; border-radius:12px; padding:20px; border:1px solid #bbf7d0;">
                                <tr>
                                    <td style="padding:8px 16px;">
                                        <span style="color:#64748b; font-size:13px;">Order Number</span><br>
                                        <strong style="color:#059669; font-size:18px; letter-spacing:1px;">{{ $order->order_number }}</strong>
                                    </td>
                                    <td style="padding:8px 16px; text-align:right;">
                                        <span style="color:#64748b; font-size:13px;">Order Date</span><br>
                                        <strong style="color:#1e293b;">{{ $order->created_at->format('M d, Y h:i A') }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Items for this Vendor -->
                    <tr>
                        <td style="padding:20px 40px;">
                            <h3 style="margin:0 0 16px; font-size:16px; color:#1e293b;">Your Items in This Order</h3>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                @php $vendorTotal = 0; @endphp
                                @foreach($vendorItems as $item)
                                @php $vendorTotal += $item->subtotal; @endphp
                                <tr>
                                    <td style="padding:12px 0; border-bottom:1px solid #e2e8f0;">
                                        <strong style="color:#1e293b;">{{ $item->product_name }}</strong><br>
                                        <span style="color:#64748b; font-size:13px;">Qty: {{ $item->quantity }} × ₦{{ number_format($item->price) }}</span>
                                    </td>
                                    <td style="padding:12px 0; border-bottom:1px solid #e2e8f0; text-align:right;">
                                        <strong style="color:#1e293b;">₦{{ number_format($item->subtotal) }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td style="padding:16px 0; font-weight:700; font-size:16px; color:#1e293b;">Your Total</td>
                                    <td style="padding:16px 0; font-weight:700; font-size:18px; color:#059669; text-align:right;">₦{{ number_format($vendorTotal) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Customer Info -->
                    <tr>
                        <td style="padding:20px 40px;">
                            <h3 style="margin:0 0 16px; font-size:16px; color:#1e293b;">Customer Details</h3>
                            @php $addr = $order->shipping_address ?? []; @endphp
                            <p style="margin:0; color:#475569; line-height:1.6;">
                                <strong>{{ $addr['name'] ?? 'N/A' }}</strong><br>
                                {{ $addr['address'] ?? '' }}<br>
                                {{ $addr['city'] ?? '' }}, {{ $addr['state'] ?? '' }}<br>
                                📞 {{ $addr['phone'] ?? '' }}
                            </p>
                        </td>
                    </tr>

                    <!-- CTA -->
                    <tr>
                        <td style="padding:20px 40px 40px; text-align:center;">
                            <a href="{{ url('/vendor/orders') }}" style="display:inline-block; background:#059669; color:white; padding:14px 32px; border-radius:10px; text-decoration:none; font-weight:600;">View in Dashboard</a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f8fafc; padding:24px 40px; text-align:center; border-top:1px solid #e2e8f0;">
                            <p style="margin:0; color:#94a3b8; font-size:13px;">
                                © {{ date('Y') }} BuyNiger. Built by Shuaibu Abdulmumin<br>
                                P3 Consulting Limited
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
