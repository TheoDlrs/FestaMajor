<x-layouts.festa title="{{ __('Mentions Légales') }}">
    <div class="mb-12 text-center">
        <h1 class="font-heading text-4xl md:text-5xl font-bold text-white mb-4">{{ __('Mentions Légales') }}</h1>
        <div class="w-24 h-1 bg-festa-gold mx-auto"></div>
    </div>

    <div class="space-y-8">
        <!-- Editeur -->
        <div class="glass-panel p-8 rounded-2xl border border-white/10">
            <h2 class="font-heading text-2xl text-festa-gold mb-4">{{ __('1. Éditeur du site') }}</h2>
            <p class="text-zinc-400 leading-relaxed">
                Le site internet <strong>Festa Major Saint-Cyprien</strong> est édité par la Mairie de Saint-Cyprien.<br><br>
                <strong>Adresse :</strong> Place de la République, 66750 Saint-Cyprien<br>
                <strong>Téléphone :</strong> +33 4 68 21 01 33<br>
                <strong>Email :</strong> contact@stcyprien.fr<br>
                <strong>SIRET :</strong> 216 601 716 00019
            </p>
        </div>

        <!-- Directeur de la publication -->
        <div class="glass-panel p-8 rounded-2xl border border-white/10">
            <h2 class="font-heading text-2xl text-festa-gold mb-4">{{ __('2. Directeur de la publication') }}</h2>
            <p class="text-zinc-400 leading-relaxed">
                Monsieur le Maire de Saint-Cyprien.
            </p>
        </div>

        <!-- Hébergement -->
        <div class="glass-panel p-8 rounded-2xl border border-white/10">
            <h2 class="font-heading text-2xl text-festa-gold mb-4">{{ __('3. Hébergement') }}</h2>
            <p class="text-zinc-400 leading-relaxed">
                Ce site est hébergé par <strong>OVH</strong> .<br>
                Adresse : A venir...
            </p>
        </div>

        <!-- Propriété Intellectuelle -->
        <div class="glass-panel p-8 rounded-2xl border border-white/10">
            <h2 class="font-heading text-2xl text-festa-gold mb-4">{{ __('4. Propriété intellectuelle') }}</h2>
            <p class="text-zinc-400 leading-relaxed">
                L'ensemble de ce site relève de la législation française et internationale sur le droit d'auteur et la propriété intellectuelle. Tous les droits de reproduction sont réservés, y compris pour les documents téléchargeables et les représentations iconographiques et photographiques.
            </p>
        </div>
    </div>
</x-layouts.festa>
