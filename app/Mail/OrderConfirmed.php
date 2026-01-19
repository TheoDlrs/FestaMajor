<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Order $order)
    {

        //

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        return new Envelope(

            subject: 'Confirmation de commande - Festa Major',

        );

    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {

        return new Content(

            markdown: 'emails.orders.confirmed',

        );

    }

    /**
     * Get the attachments for the message.

     *

     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {

        $this->order->load(['user', 'reservations.product']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $this->order,
            'qrCode' => null,
        ]);

        return [

            Attachment::fromData(fn () => $pdf->output(), 'facture-'.$this->order->reference.'.pdf')

                ->withMime('application/pdf'),

        ];

    }
}
