<x-mail::message>
# Commande Confirmée !

Hola **{{ $order->user->name }}**,

Votre commande a bien été enregistrée.

Voici votre référence unique de retrait pour l'ensemble de votre panier :

<x-mail::panel>
# {{ $order->reference }}
</x-mail::panel>

**Détails de la commande :**

@foreach($order->reservations->groupBy(fn($r) => $r->product_id . '_' . $r->size) as $items)
@php $item = $items->first(); @endphp
- **{{ $items->count() }}x {{ $item->product->name }}** @if($item->size) (Taille: {{ $item->size }}) @endif — {{ number_format($item->product->price * $items->count(), 2) }}€
@endforeach

---
**Total à régler sur place : {{ number_format($order->reservations->sum(fn($r) => $r->product->price), 2) }}€**

*Le paiement se fera directement sur place lors du retrait.*

Merci de votre soutien pour la Festa Major !

<x-mail::button :url="route('dashboard')">
Voir ma commande
</x-mail::button>

À très vite,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
