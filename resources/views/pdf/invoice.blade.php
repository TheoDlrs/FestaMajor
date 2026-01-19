<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $order->reference }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; }
        .header { border-bottom: 2px solid #CA8A04; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #CA8A04; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-info { float: right; text-align: right; }
        .client-info { margin-bottom: 40px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th { background: #f9f9f9; padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 12px; text-transform: uppercase; color: #666; }
        .table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .total { text-align: right; font-size: 18px; font-weight: bold; color: #CA8A04; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; background: #eee; }
        .paid { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    <div class="header">
        <div class="invoice-info">
            <h1 style="margin: 0; font-size: 20px;">RÉCAPITULATIF DE COMMANDE</h1>
            <p style="margin: 5px 0;">Réf: <strong>{{ $order->reference }}</strong></p>
            <p style="margin: 0;">Date: {{ $order->created_at->format('d/m/Y') }}</p>
            
            @if($qrCode)
                <div style="margin-top: 15px;">
                    <img src="{{ $qrCode }}" width="100" height="100">
                </div>
            @endif
        </div>
        <div class="logo">FESTA MAJOR</div>
        <p style="margin: 5px 0; font-size: 12px;">Mairie de Saint-Cyprien</p>
    </div>

    <div class="client-info">
        <p style="margin: 0; color: #666; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Destinataire</p>
        <h2 style="margin: 5px 0; font-size: 18px;">{{ $order->user->name }}</h2>
        <p style="margin: 0;">{{ $order->user->email }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Taille</th>
                <th style="text-align: center;">Qté</th>
                <th style="text-align: right;">Prix Unitaire</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->reservations->groupBy(fn($r) => $r->product_id . '_' . $r->size) as $items)
                @php $it = $items->first(); @endphp
                <tr>
                    <td>{{ $it->product->name }}</td>
                    <td>{{ $it->size ?? '-' }}</td>
                    <td style="text-align: center;">{{ $items->count() }}</td>
                    <td style="text-align: right;">{{ number_format($it->product->price, 2) }}€</td>
                    <td style="text-align: right;">{{ number_format($it->product->price * $items->count(), 2) }}€</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        TOTAL À RÉGLER SUR PLACE : {{ number_format($order->reservations->sum(fn($r) => $r->product->price), 2) }}€
    </div>

    <div style="margin-top: 30px;">
        <p style="font-size: 12px; color: #666;">
            <strong>Information :</strong> Ce document est un bon de réservation. Le règlement effectif et la facturation finale seront établis lors de votre passage au stand boutique de la Festa Major (Place de la République).
        </p>
    </div>

    <div class="footer">
        Festa Major Saint-Cyprien 2026 &bull; Mairie de Saint-Cyprien &bull; contact@stcyprien.fr
    </div>
</body>
</html>
