<x-mail::message>
# Nouveau Message de {{ $data['name'] }}

**Sujet :** {{ $data['subject'] }}  
**Email :** {{ $data['email'] }}

## Message :
{{ $data['message'] }}

<x-mail::button :url="config('app.url')">
Visiter le site
</x-mail::button>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
