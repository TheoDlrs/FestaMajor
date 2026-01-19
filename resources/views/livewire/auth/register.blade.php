<x-layouts.auth maxWidth="max-w-5xl">
    <div x-data="{ 
        password: '',
        avatarPreview: null,
        rules: {
            length: false,
            uppercase: false,
            lowercase: false,
            number: false,
            special: false
        },
        checkPassword() {
            this.rules.length = this.password.length >= 8;
            this.rules.uppercase = /[A-Z]/.test(this.password);
            this.rules.lowercase = /[a-z]/.test(this.password);
            this.rules.number = /[0-9]/.test(this.password);
            this.rules.special = /[^A-Za-z0-9]/.test(this.password);
        },
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.avatarPreview = URL.createObjectURL(file);
            }
        }
    }" class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        <!-- Left Side: Form -->
        <div class="lg:col-span-7 flex flex-col gap-4">
            <div class="text-center lg:text-left">
                <h1 class="font-heading text-4xl font-bold text-white mb-2">{{ __('Inscription') }}</h1>
                <p class="text-zinc-400 text-sm">{{ __('Rejoignez la fête dès maintenant') }}</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="text-center" :status="session('status')" />

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="flex flex-col gap-6 mt-4">
                @csrf

                <!-- Avatar Selection (Compact) -->
                <div class="flex items-center gap-6 p-4 bg-white/5 border border-white/10 rounded-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-festa-gold/5 rounded-full blur-2xl pointer-events-none"></div>
                    
                    <div class="shrink-0">
                        <div class="size-20 rounded-2xl overflow-hidden border-2 border-festa-gold/30 bg-black/40 relative shadow-xl">
                            <template x-if="avatarPreview">
                                <img :src="avatarPreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!avatarPreview">
                                <div class="w-full h-full flex items-center justify-center text-zinc-600">
                                    <flux:icon.user class="size-8" />
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <label class="block text-[10px] font-black uppercase text-festa-gold tracking-widest mb-2">Photo de profil</label>
                        <label class="inline-flex px-4 py-2 bg-zinc-800 text-white text-[10px] font-bold uppercase tracking-widest rounded-lg cursor-pointer hover:bg-zinc-700 transition border border-white/5">
                            Choisir une image
                            <input type="file" name="avatar" class="hidden" accept="image/*" @change="previewImage">
                        </label>
                        <p class="text-[9px] text-zinc-500 mt-2 italic">Format carré recommandé (JPG, PNG)</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-5">
                    <!-- Name -->
                    <flux:input
                        name="name"
                        :label="__('Nom complet')"
                        :value="old('name')"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Jean Dupont"
                        class="!bg-white/5 !border-white/10 !text-white focus:!border-festa-gold"
                    />

                    <!-- Email Address -->
                    <flux:input
                        name="email"
                        :label="__('Email')"
                        :value="old('email')"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="votre@email.com"
                        class="!bg-white/5 !border-white/10 !text-white focus:!border-festa-gold"
                    />
                </div>

                <div class="grid md:grid-cols-2 gap-5">
                    <!-- Password -->
                    <flux:input
                        name="password"
                        :label="__('Mot de passe')"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                        viewable
                        x-model="password"
                        @input="checkPassword"
                        class="!bg-white/5 !border-white/10 !text-white focus:!border-festa-gold"
                    />

                    <!-- Confirm Password -->
                    <flux:input
                        name="password_confirmation"
                        :label="__('Confirmer le mot de passe')"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                        viewable
                        class="!bg-white/5 !border-white/10 !text-white focus:!border-festa-gold"
                    />
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-4 bg-festa-red text-white font-black uppercase tracking-[0.2em] text-xs rounded-xl shadow-lg hover:scale-[1.02] transition duration-300 transform border border-transparent hover:border-festa-red">
                        {{ __('Créer mon compte festivalier') }}
                    </button>
                </div>
            </form>

            <div class="text-center lg:text-left text-sm text-zinc-400 mt-4">
                <span>{{ __('Déjà inscrit ?') }}</span>
                <a href="{{ route('login') }}" class="font-bold text-white hover:text-festa-gold transition ml-1" wire:navigate>{{ __('Se connecter') }}</a>
            </div>
        </div>

        <!-- Right Side: Password Strength -->
        <div class="lg:col-span-5 space-y-6 lg:mt-24">
            <div class="bg-black/40 border border-white/10 rounded-3xl p-8 backdrop-blur-xl relative overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 w-32 h-32 bg-festa-gold/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-festa-red/5 rounded-full blur-3xl pointer-events-none"></div>
                
                <h3 class="font-heading text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <flux:icon.shield-check class="size-6 text-festa-gold" />
                    Sécurité du compte
                </h3>

                <ul class="space-y-4">
                    <template x-for="(met, key) in rules">
                        <li class="flex items-center gap-4 transition-all duration-500" :class="met ? 'text-green-400 translate-x-1' : 'text-zinc-500'">
                            <div class="size-6 flex items-center justify-center rounded-full border-2 transition-all duration-500" :class="met ? 'bg-green-500/20 border-green-500/50' : 'bg-white/5 border-white/10'">
                                <flux:icon.check x-show="met" class="size-3.5 text-green-400" />
                                <flux:icon.lock-closed x-show="!met" class="size-3 text-zinc-600" />
                            </div>
                            <span class="text-[11px] font-black uppercase tracking-widest">
                                <span x-show="key === 'length'">Au moins 8 caractères</span>
                                <span x-show="key === 'uppercase'">Une majuscule</span>
                                <span x-show="key === 'lowercase'">Une minuscule</span>
                                <span x-show="key === 'number'">Un chiffre</span>
                                <span x-show="key === 'special'">Un caractère spécial</span>
                            </span>
                        </li>
                    </template>
                </ul>

                <div class="mt-8 pt-8 border-t border-white/10">
                    <div class="flex gap-4 items-start">
                        <div class="shrink-0 p-2 bg-festa-gold/10 rounded-lg">
                            <flux:icon.information-circle class="size-4 text-festa-gold" />
                        </div>
                        <p class="text-[10px] text-zinc-500 italic leading-relaxed">
                            Pour garantir la sécurité de vos réservations au stand, nous vous recommandons d'utiliser un mot de passe unique et une photo de profil reconnaissable.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.auth>
