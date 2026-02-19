{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    Email: Order Confirmation - Sent to Customer
--}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background:#f0f4f8; font-family:'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8; padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:white; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                    
                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding:32px; text-align:center;">
                            <h1 style="color:white; margin:0 0 4px; font-size:26px; font-weight:800; letter-spacing:-0.5px;">BuyNiger</h1>
                            <p style="color:rgba(255,255,255,0.8); margin:0; font-size:13px;">Your Order Receipt</p>
                        </td>
                    </tr>
                    
                    {{-- Success Banner --}}
                    <tr>
                        <td style="padding:32px 40px 16px; text-align:center;">
                            <div style="width:56px; height:56px; background:#10b981; border-radius:50%; margin:0 auto 16px; line-height:56px; text-align:center;">
                                <span style="color:white; font-size:28px;">✓</span>
                            </div>
                            <h2 style="margin:0 0 6px; font-size:22px; color:#1e293b; font-weight:700;">Order Confirmed!</h2>
                            <p style="margin:0; color:#64748b; font-size:14px;">Thank you for shopping with BuyNiger</p>
                        </td>
                    </tr>

                    {{-- Order Info Card --}}
                    <tr>
                        <td style="padding:16px 40px;">
                            <table width="100%" style="background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <span style="color:#64748b; font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">Order Number</span><br>
                                        <strong style="color:#1e40af; font-size:18px; letter-spacing:1px;">{{ $order->order_number }}</strong>
                                    </td>
                                    <td style="padding:16px 20px; text-align:right;">
                                        <span style="color:#64748b; font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">Date</span><br>
                                        <strong style="color:#1e293b; font-size:14px;">{{ $order->created_at->format('M d, Y · h:iA') }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Delivery Method --}}
                    @php $addr = $order->shipping_address ?? []; @endphp
                    <tr>
                        <td style="padding:16px 40px;">
                            <h3 style="margin:0 0 12px; font-size:15px; color:#1e293b; font-weight:700;">
                                📦 Delivery Method: {{ $addr['shipping_method'] ?? 'N/A' }}
                            </h3>

                            @if(str_contains(strtolower($addr['shipping_method'] ?? ''), 'pickup'))
                                {{-- Pickup: Show vendor addresses --}}
                                <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:16px;">
                                    <p style="margin:0 0 8px; font-size:13px; color:#92400e; font-weight:600;">
                                        🏪 Pickup Location(s):
                                    </p>
                                    @foreach($order->items as $item)
                                        @if($item->vendor)
                                            <p style="margin:4px 0; font-size:13px; color:#78350f;">
                                                <strong>{{ $item->vendor->store_name }}:</strong>
                                                {{ $item->vendor->business_address ?? $item->vendor->city ?? 'Contact vendor for address' }}
                                                @if($item->vendor->city), {{ $item->vendor->city }}@endif
                                                @if($item->vendor->state), {{ $item->vendor->state }}@endif
                                                @if($item->vendor->business_phone) · 📞 {{ $item->vendor->business_phone }}@endif
                                            </p>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                {{-- Vendor Shipping: Show delivery address --}}
                                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:16px;">
                                    <p style="margin:0 0 4px; font-size:13px; color:#166534; font-weight:600;">
                                        🚚 Delivering to:
                                    </p>
                                    <p style="margin:0; font-size:13px; color:#15803d; line-height:1.6;">
                                        {{ $addr['name'] ?? 'N/A' }}<br>
                                        {{ $addr['address'] ?? '' }}<br>
                                        {{ $addr['city'] ?? '' }}, {{ $addr['state'] ?? '' }}<br>
                                        📞 {{ $addr['phone'] ?? '' }}
                                    </p>
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- Order Items (Receipt) --}}
                    <tr>
                        <td style="padding:16px 40px;">
                            <h3 style="margin:0 0 12px; font-size:15px; color:#1e293b; font-weight:700;">🧾 Order Items</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
                                <tr style="background:#f8fafc;">
                                    <td style="padding:10px 16px; font-size:12px; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Item</td>
                                    <td style="padding:10px 16px; font-size:12px; color:#64748b; font-weight:600; text-align:center; text-transform:uppercase; letter-spacing:0.5px;">Qty</td>
                                    <td style="padding:10px 16px; font-size:12px; color:#64748b; font-weight:600; text-align:right; text-transform:uppercase; letter-spacing:0.5px;">Price</td>
                                </tr>
                                @foreach($order->items as $item)
                                <tr>
                                    <td style="padding:12px 16px; border-top:1px solid #f1f5f9;">
                                        <strong style="color:#1e293b; font-size:14px;">{{ $item->product_name }}</strong>
                                        @if($item->vendor)
                                            <br><span style="color:#94a3b8; font-size:11px;">by {{ $item->vendor->store_name }}</span>
                                        @endif
                                    </td>
                                    <td style="padding:12px 16px; border-top:1px solid #f1f5f9; text-align:center; color:#475569; font-size:14px;">{{ $item->quantity }}</td>
                                    <td style="padding:12px 16px; border-top:1px solid #f1f5f9; text-align:right; font-weight:600; color:#1e293b; font-size:14px;">₦{{ number_format($item->subtotal) }}</td>
                                </tr>
                                @endforeach

                                {{-- Totals --}}
                                <tr style="background:#f8fafc;">
                                    <td colspan="2" style="padding:10px 16px; border-top:1px solid #e2e8f0; color:#64748b; font-size:13px;">Subtotal</td>
                                    <td style="padding:10px 16px; border-top:1px solid #e2e8f0; text-align:right; color:#475569; font-size:13px;">₦{{ number_format($order->subtotal) }}</td>
                                </tr>
                                <tr style="background:#f8fafc;">
                                    <td colspan="2" style="padding:6px 16px; color:#64748b; font-size:13px;">Shipping</td>
                                    <td style="padding:6px 16px; text-align:right; color:#475569; font-size:13px;">
                                        @if($order->shipping_cost > 0) ₦{{ number_format($order->shipping_cost) }} @else <span style="color:#10b981;">FREE</span> @endif
                                    </td>
                                </tr>
                                @if($order->discount > 0)
                                <tr style="background:#f8fafc;">
                                    <td colspan="2" style="padding:6px 16px; color:#10b981; font-size:13px;">
                                        🏷️ Coupon ({{ $order->coupon_code }})
                                    </td>
                                    <td style="padding:6px 16px; text-align:right; color:#10b981; font-size:13px;">-₦{{ number_format($order->discount) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="2" style="padding:14px 16px; border-top:2px solid #1e40af; font-weight:700; font-size:16px; color:#1e293b;">Total</td>
                                    <td style="padding:14px 16px; border-top:2px solid #1e40af; text-align:right; font-weight:800; font-size:18px; color:#1e40af;">₦{{ number_format($order->total) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Tracking --}}
                    @if(isset($addr['tracking_id']))
                    <tr>
                        <td style="padding:16px 40px;">
                            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:16px; text-align:center;">
                                <p style="margin:0 0 4px; font-size:12px; color:#1e40af; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Tracking ID</p>
                                <p style="margin:0; font-size:18px; color:#1e40af; font-weight:700; letter-spacing:2px;">{{ $addr['tracking_id'] }}</p>
                            </div>
                        </td>
                    </tr>
                    @endif

                    {{-- CTA --}}
                    <tr>
                        <td style="padding:16px 40px 32px; text-align:center;">
                            <a href="{{ url('/orders/' . $order->order_number) }}" style="display:inline-block; background:linear-gradient(135deg, #1e40af, #3b82f6); color:white; padding:14px 40px; border-radius:10px; text-decoration:none; font-weight:700; font-size:15px;">
                                Track Your Order
                            </a>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8fafc; padding:24px 40px; text-align:center; border-top:1px solid #e2e8f0;">
                            <p style="margin:0 0 4px; color:#94a3b8; font-size:12px;">
                                © {{ date('Y') }} BuyNiger. All rights reserved.
                            </p>
                            <p style="margin:0; color:#cbd5e1; font-size:11px;">
                                Built by Shuaibu Abdulmumin · P3 Consulting Limited
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
