<x-mail::message>
# Réservation Confirmée !

Hola **{{ $reservation->user->name }}**,

Votre réservation pour **{{ $reservation->product->name }}** a bien été prise en compte.

Voici votre référence unique à présenter au stand boutique :

<x-mail::panel>
# {{ $reservation->reference }}
</x-mail::panel>

**Détails de la réservation :**
- **Produit :** {{ $reservation->product->name }}
- **Prix :** {{ number_format($reservation->product->price, 2) }}€
- **Date :** {{ $reservation->created_at->format('d/m/Y à H:i') }}

*Le paiement se fera directement sur place lors du retrait.*

Merci de votre soutien pour la Festa Major !

<x-mail::button :url="route('dashboard')">
Voir ma réservation
</x-mail::button>

À très vite,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
