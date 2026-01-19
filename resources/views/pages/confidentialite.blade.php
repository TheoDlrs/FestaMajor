<x-layouts.festa title="{{ __('Politique de Confidentialité') }}">
    <div class="mb-12 text-center">
        <h1 class="font-heading text-4xl md:text-5xl font-bold text-white mb-4">{{ __('Confidentialité') }}</h1>
        <div class="w-24 h-1 bg-festa-red mx-auto"></div>
    </div>

    <div class="space-y-8">
        <div class="glass-panel p-8 rounded-2xl border border-white/10">
            <p class="text-zinc-400 leading-relaxed">
                La Mairie de Saint-Cyprien s'engage à ce que la collecte et le traitement de vos données, effectués à partir du site de la Festa Major, soient conformes au règlement général sur la protection des données (RGPD) et à la loi Informatique et Libertés.
            </p>
        </div>

        <div class="glass-panel p-8 rounded-2xl border border-white/10">
            <h2 class="font-heading text-2xl text-festa-gold mb-4">{{ __('1. Données collectées') }}</h2>
            <p class="text-zinc-400 leading-relaxed mb-4">
                Nous collectons les données suivantes via notre formulaire de contact et lors de votre inscription :
            </p>
            <ul class="list-disc list-inside text-zinc-400 space-y-2 marker:text-festa-red">
                <li>{{ __('Nom et Prénom') }}</li>
                <li>{{ __('Adresse email') }}</li>
                <li>{{ __('Message et objet de la demande') }}</li>
            </ul>
        </div>

        <div class="glass-panel p-8 rounded-2xl border border-white/10">
            <h2 class="font-heading text-2xl text-festa-gold mb-4">{{ __('2. Utilisation des données') }}</h2>
            <p class="text-zinc-400 leading-relaxed">
                Les informations recueillies sont enregistrées dans un fichier informatisé par la Mairie de Saint-Cyprien pour la gestion des demandes des usagers et l'envoi d'informations relatives à la Festa Major (si consenti). Elles sont conservées pendant 3 ans.
            </p>
        </div>

        <div class="glass-panel p-8 rounded-2xl border border-white/10">
            <h2 class="font-heading text-2xl text-festa-gold mb-4">{{ __('3. Vos droits') }}</h2>
            <p class="text-zinc-400 leading-relaxed mb-4">
                Vous pouvez accéder aux données vous concernant, les rectifier, demander leur effacement ou exercer votre droit à la limitation du traitement de vos données.
            </p>
            <p class="text-zinc-400 leading-relaxed">
                Pour exercer ces droits ou pour toute question sur le traitement de vos données, vous pouvez nous contacter à : <strong>dpo@stcyprien.fr</strong>
            </p>
        </div>
    </div>
</x-layouts.festa>
