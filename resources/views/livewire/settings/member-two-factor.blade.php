<?php

use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Symfony\Component\HttpFoundation\Response;

new class extends Component {
    #[Locked]
    public bool $twoFactorEnabled;

    #[Locked]
    public bool $requiresConfirmation;

    #[Locked]
    public string $qrCodeSvg = '';

    #[Locked]
    public string $manualSetupKey = '';

    public bool $showModal = false;
    public bool $showVerificationStep = false;

    #[Validate('required|string|size:6', onUpdate: false)]
    public string $code = '';

    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        abort_unless(Features::enabled(Features::twoFactorAuthentication()), Response::HTTP_FORBIDDEN);

        if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
            $disableTwoFactorAuthentication(auth()->user());
        }

        $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        $this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
    }

    public function enable(EnableTwoFactorAuthentication $enableTwoFactorAuthentication): void
    {
        $enableTwoFactorAuthentication(auth()->user());

        if (! $this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }

        $this->loadSetupData();
        $this->showModal = true;
    }

    private function loadSetupData(): void
    {
        $user = auth()->user();
        try {
            $this->qrCodeSvg = $user?->twoFactorQrCodeSvg();
            $this->manualSetupKey = decrypt($user->two_factor_secret);
        } catch (Exception) {
            $this->addError('setupData', 'Failed to fetch setup data.');
            $this->reset('qrCodeSvg', 'manualSetupKey');
        }
    }

    public function showVerificationIfNecessary(): void
    {
        if ($this->requiresConfirmation) {
            $this->showVerificationStep = true;
            $this->resetErrorBag();
            return;
        }
        $this->closeModal();
    }

    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): void
    {
        $this->validate();
        $confirmTwoFactorAuthentication(auth()->user(), $this->code);
        $this->closeModal();
        $this->twoFactorEnabled = true;
    }

    public function resetVerification(): void
    {
        $this->reset('code', 'showVerificationStep');
        $this->resetErrorBag();
    }

    public function disable(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $disableTwoFactorAuthentication(auth()->user());
        $this->twoFactorEnabled = false;
    }

    public function closeModal(): void
    {
        $this->reset('code', 'manualSetupKey', 'qrCodeSvg', 'showModal', 'showVerificationStep');
        $this->resetErrorBag();
        if (! $this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }
    }
}; ?>

<div class="bg-zinc-900/50 border border-white/10 rounded-3xl p-8 relative overflow-hidden backdrop-blur-xl">
    <div class="relative z-10">
        <h3 class="font-heading text-2xl text-white font-bold mb-6 flex items-center gap-3">
            <flux:icon.shield-check class="size-6 text-festa-gold" />
            Double Authentification (2FA)
        </h3>

        <div class="text-sm text-zinc-400 mb-6">
            Ajoutez une couche de sécurité supplémentaire à votre compte en exigeant un code de votre téléphone lors de la connexion.
        </div>

        @if ($twoFactorEnabled)
            <div class="flex items-center justify-between bg-green-500/10 border border-green-500/20 rounded-xl p-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="size-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="font-bold text-green-400">Authentification à deux facteurs activée</span>
                </div>
                <button wire:click="disable" class="px-4 py-2 bg-red-500/10 text-red-400 text-xs font-bold uppercase tracking-wider rounded-lg border border-red-500/20 hover:bg-red-500/20 transition">
                    Désactiver
                </button>
            </div>
            
            <livewire:settings.two-factor.recovery-codes :$requiresConfirmation/>
        @else
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <p class="text-white font-bold">Vous n'avez pas encore activé la 2FA.</p>
                    <p class="text-zinc-500 text-xs mt-1">Protégez votre compte dès maintenant.</p>
                </div>
                <button wire:click="enable" class="px-6 py-2 bg-festa-gold text-festa-red-dark font-bold uppercase tracking-widest text-xs rounded-lg shadow-lg hover:scale-105 transition duration-300">
                    Activer la 2FA
                </button>
            </div>
        @endif

        <!-- Modal -->
        <flux:modal name="two-factor-setup-modal" class="max-w-md bg-zinc-900 border border-white/10 text-white" @close="closeModal" wire:model="showModal">
            <div class="space-y-6">
                <div class="text-center">
                    <h3 class="font-heading text-2xl font-bold mb-2">Configuration 2FA</h3>
                    <p class="text-zinc-400 text-sm">
                        @if ($showVerificationStep)
                            Entrez le code à 6 chiffres de votre application.
                        @else
                            Scannez le QR Code avec votre application d'authentification (Google Authenticator, Authy, etc.).
                        @endif
                    </p>
                </div>

                @if ($showVerificationStep)
                    <div class="flex justify-center">
                        <flux:otp name="code" wire:model="code" length="6" class="mx-auto" />
                    </div>

                    <div class="flex gap-3">
                        <flux:button variant="ghost" class="flex-1" wire:click="resetVerification">Retour</flux:button>
                        <flux:button variant="primary" class="flex-1 bg-festa-gold text-black hover:bg-festa-gold-dark" wire:click="confirmTwoFactor" x-bind:disabled="$wire.code.length < 6">Confirmer</flux:button>
                    </div>
                @else
                    <div class="flex justify-center bg-white p-4 rounded-xl">
                        @if($qrCodeSvg)
                            {!! $qrCodeSvg !!}
                        @else
                            <flux:icon.loading class="text-black" />
                        @endif
                    </div>

                    <div class="space-y-4">
                         <div class="text-center">
                             <p class="text-xs text-zinc-500 mb-2">Ou entrez ce code manuellement :</p>
                             <code class="bg-black/50 px-3 py-1 rounded text-festa-gold font-mono">{{ $manualSetupKey }}</code>
                         </div>

                        <flux:button variant="primary" class="w-full bg-festa-gold text-black hover:bg-festa-gold-dark" wire:click="showVerificationIfNecessary">
                            Continuer
                        </flux:button>
                    </div>
                @endif
            </div>
        </flux:modal>
    </div>
</div>
