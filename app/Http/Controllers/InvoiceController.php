<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Download the invoice for the given order.
     */
    public function download(Order $order)
    {
        // Security check: only the owner or an admin can download
        if (Auth::user()->id !== $order->user_id && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $order->load(['user', 'reservations.product']);

        // Generate QR Code as Base64 for the PDF
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode(route('admin.reservations', ['search' => $order->reference]));

        try {
            $qrCodeBase64 = 'data:image/png;base64,'.base64_encode(file_get_contents($qrCodeUrl));
        } catch (\Exception $e) {
            $qrCodeBase64 = null;
        }

        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $order,
            'qrCode' => $qrCodeBase64,
        ]);

        return $pdf->download('facture-'.$order->reference.'.pdf');
    }
}
