<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Mail: NewOrderNotification - Sent to vendor when order placed
 */

namespace App\Mail;

use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public Vendor $vendor;
    public $vendorItems;

    public function __construct(Order $order, Vendor $vendor, $vendorItems)
    {
        $this->order = $order;
        $this->vendor = $vendor;
        $this->vendorItems = $vendorItems;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Order Received - ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor-new-order',
        );
    }
}
