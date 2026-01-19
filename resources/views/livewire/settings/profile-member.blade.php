<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $avatar = null;
    public $avatarPath = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->avatarPath = $user->avatar_path;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
            'avatar' => ['nullable', 'image', 'max:1024'], // 1MB Max
        ]);

        if ($this->avatar) {
            // Delete old avatar if exists
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $validated['avatar_path'] = $this->avatar->store('avatars', 'public');
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        $this->avatarPath = $user->avatar_path;
        $this->avatar = null;

        $this->dispatch('profile-updated', name: $user->name);
    }
};
?>

<div class="w-full space-y-8">
    <!-- Header -->
    <div class="text-center md:text-left">
        <h3 class="font-heading text-3xl text-white font-bold mb-2">{{ __('Mon Profil Festivalier') }}</h3>
        <p class="text-zinc-400">{{ __('Modifiez vos informations personnelles pour l\'événement.') }}</p>
    </div>

    <div class="bg-zinc-900/50 border border-white/10 rounded-3xl p-8 md:p-12 relative overflow-hidden backdrop-blur-xl">
        <!-- Decoration -->
        <div class="absolute top-0 left-0 w-32 h-32 bg-festa-red/10 rounded-full blur-[60px] pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-32 h-32 bg-festa-gold/10 rounded-full blur-[60px] pointer-events-none"></div>

        <form wire:submit="updateProfileInformation" class="relative z-10 space-y-10">
            
            <!-- Centered Avatar Upload -->
            <div class="flex flex-col items-center space-y-4">
                <div class="relative group">
                    <div class="size-32 md:size-40 rounded-3xl overflow-hidden border-4 border-white/10 bg-black/40 shadow-2xl transition duration-500 group-hover:border-festa-gold/50">
                        @if ($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover scale-105">
                        @elseif ($avatarPath)
                            <img src="{{ asset('storage/' . $avatarPath) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-zinc-800 to-zinc-900">
                                <flux:avatar :name="$name" class="size-20 text-4xl" />
                            </div>
                        @endif
                    </div>
                    
                    <label class="absolute inset-0 flex flex-col items-center justify-center bg-black/60 opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer rounded-3xl backdrop-blur-sm">
                        <flux:icon.camera class="size-10 text-white mb-2 transform translate-y-2 group-hover:translate-y-0 transition duration-300" />
                        <span class="text-[10px] font-black uppercase text-white tracking-widest">{{ __('Modifier la photo') }}</span>
                        <input type="file" wire:model="avatar" class="hidden" accept="image/*">
                    </label>
                </div>
                <div class="text-center">
                    <p class="text-[10px] font-black uppercase text-festa-gold tracking-[0.3em]">{{ __('Photo de profil') }}</p>
                    @error('avatar') <span class="text-red-500 text-[10px] block mt-2 font-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Stacked Inputs -->
            <div class="max-w-2xl mx-auto space-y-8 w-full">
                <div class="space-y-3">
                    <label class="block text-xs font-black text-white uppercase tracking-[0.2em] ml-1">{{ __('Votre Nom Public') }}</label>
                    <div class="relative group">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-festa-red/20 to-festa-gold/20 rounded-2xl blur opacity-0 group-focus-within:opacity-100 transition duration-500"></div>
                        <input type="text" wire:model="name" class="relative w-full bg-black/60 border border-white/10 rounded-2xl px-6 py-5 text-white placeholder-zinc-600 focus:border-festa-gold outline-none transition duration-300 text-lg font-medium" placeholder="{{ __('Votre nom complet') }}">
                    </div>
                    @error('name') <span class="text-red-500 text-xs font-bold ml-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-3">
                    <label class="block text-xs font-black text-white uppercase tracking-[0.2em] ml-1">{{ __('Adresse Email') }}</label>
                    <div class="relative group">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-festa-red/20 to-festa-gold/20 rounded-2xl blur opacity-0 group-focus-within:opacity-100 transition duration-500"></div>
                        <input type="email" wire:model="email" class="relative w-full bg-black/60 border border-white/10 rounded-2xl px-6 py-5 text-white placeholder-zinc-600 focus:border-festa-gold outline-none transition duration-300 text-lg font-medium" placeholder="votre@email.com">
                    </div>
                    @error('email') <span class="text-red-500 text-xs font-bold ml-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-6 border-t border-white/5 max-w-2xl mx-auto">
                <x-action-message class="text-green-400 font-bold" on="profile-updated">
                    {{ __('Modifications enregistrées !') }}
                </x-action-message>

                <button type="submit" wire:loading.attr="disabled" class="px-8 py-3 bg-festa-gold text-festa-red-dark font-black uppercase tracking-widest text-sm rounded-xl shadow-[0_0_20px_rgba(234,179,8,0.2)] hover:shadow-[0_0_30px_rgba(234,179,8,0.4)] hover:scale-105 transition duration-300 flex items-center gap-2">
                    <span wire:loading.remove>{{ __('Enregistrer') }}</span>
                    <span wire:loading>{{ __('Enregistrement...') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
