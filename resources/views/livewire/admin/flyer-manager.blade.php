<?php

use Livewire\Volt\Component;
use App\Models\Flyer;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $flyerId = null;
    public $title = '';
    public $image_url = '';
    public $subtitle = '';
    public $headline = '';
    public $description = '';
    public $quote_text = '';
    public $quote_author = '';
    public $is_active = true;
    
    public $showModal = false;
    public $showActiveModal = false;
    public $selectedActiveFlyers = [];

    public function with(): array
    {
        return [
            'flyers' => Flyer::latest()->paginate(12),
            'allFlyers' => Flyer::orderBy('created_at', 'desc')->get(), 
        ];
    }

    public function create()
    {
        $this->reset(['flyerId', 'title', 'image_url', 'subtitle', 'headline', 'description', 'quote_text', 'quote_author', 'is_active']);
        $this->showModal = true;
    }

    public function edit(Flyer $flyer)
    {
        $this->flyerId = $flyer->id;
        $this->title = $flyer->title;
        $this->image_url = $flyer->image_url;
        $this->subtitle = $flyer->subtitle;
        $this->headline = $flyer->headline;
        $this->description = $flyer->description;
        $this->quote_text = $flyer->quote_text;
        $this->quote_author = $flyer->quote_author;
        $this->is_active = $flyer->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'image_url' => 'required|url',
            'subtitle' => 'nullable|string|max:255',
            'headline' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'quote_text' => 'nullable|string|max:255',
            'quote_author' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Flyer::updateOrCreate(['id' => $this->flyerId], [
            'title' => $this->title,
            'image_url' => $this->image_url,
            'subtitle' => $this->subtitle,
            'headline' => $this->headline,
            'description' => $this->description,
            'quote_text' => $this->quote_text,
            'quote_author' => $this->quote_author,
            'is_active' => $this->is_active,
        ]);

        $this->showModal = false;
        $this->dispatch('flyer-saved', message: 'Flyer enregistré avec succès.');
    }

    public function delete(Flyer $flyer)
    {
        $flyer->delete();
        $this->dispatch('flyer-saved', message: 'Flyer supprimé.');
    }

    public function toggleActive(Flyer $flyer)
    {
        $flyer->update(['is_active' => !$flyer->is_active]);
        $this->dispatch('flyer-saved', message: 'Statut du flyer mis à jour.');
    }

    public function openActiveManager()
    {
        $this->selectedActiveFlyers = Flyer::where('is_active', true)->pluck('id')->toArray();
        $this->showActiveModal = true;
    }

    public function toggleSelection($id)
    {
        if (in_array($id, $this->selectedActiveFlyers)) {
            $this->selectedActiveFlyers = array_diff($this->selectedActiveFlyers, [$id]);
        } else {
            $this->selectedActiveFlyers[] = $id;
        }
    }

    public function saveActiveSelection()
    {
        Flyer::query()->update(['is_active' => false]);
        
        if (!empty($this->selectedActiveFlyers)) {
            Flyer::whereIn('id', $this->selectedActiveFlyers)->update(['is_active' => true]);
        }

        $this->showActiveModal = false;
        $this->dispatch('flyer-saved', message: 'Sélection active mise à jour.');
    }
}; ?>

<div class="space-y-6">
    <!-- Notification -->
    <div 
        x-data="{ show: false, message: '' }"
        x-on:flyer-saved.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition
        class="fixed top-24 right-6 z-[100] bg-festa-gold text-festa-red-dark px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 border border-festa-red/20"
        style="display: none;"
    >
        <flux:icon.check-circle class="size-6" />
        <p class="text-sm font-bold uppercase tracking-wide" x-text="message"></p>
    </div>

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Gestion des Flyers</h1>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1">
                Gérez le visuel, le texte principal et la devise ("Citation").
            </p>
        </div>
        <div class="flex gap-3">
            <flux:button icon="eye" wire:click="openActiveManager" class="bg-white dark:bg-zinc-800 border dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 shadow-sm">
                Gérer la diffusion
            </flux:button>
            <flux:button variant="primary" icon="plus" wire:click="create" class="bg-festa-gold text-festa-red-dark font-bold border-none hover:bg-festa-gold-light transition shadow-lg shadow-festa-gold/20">
                Ajouter un flyer
            </flux:button>
        </div>
    </div>

    <!-- Flyers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($flyers as $flyer)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-sm group hover:border-festa-gold/50 transition duration-300 flex flex-col">
                
                <!-- Image Preview -->
                <div class="aspect-[4/5] relative overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    <img src="{{ $flyer->image_url }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                    
                    <!-- Overlay Actions -->
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-3">
                        <flux:button size="sm" icon="pencil-square" wire:click="edit({{ $flyer->id }})" class="bg-white text-black border-none hover:bg-zinc-200" />
                        <flux:button size="sm" variant="danger" icon="trash" wire:click="delete({{ $flyer->id }})" wire:confirm="Supprimer ce flyer ?" />
                    </div>

                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        <button wire:click="toggleActive({{ $flyer->id }})" 
                                class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg transition transform hover:scale-105 
                                {{ $flyer->is_active ? 'bg-green-500 text-white' : 'bg-zinc-500/80 text-white backdrop-blur-md hover:bg-zinc-600' }}">
                            {{ $flyer->is_active ? 'Actif' : 'Inactif' }}
                        </button>
                    </div>
                </div>

                <!-- Info -->
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-bold text-zinc-900 dark:text-white truncate text-lg">{{ $flyer->title }}</h3>
                    <p class="text-xs text-zinc-500 mt-1 truncate">{{ $flyer->headline ?: 'Sans titre' }}</p>
                    
                    @if($flyer->quote_text)
                        <div class="mt-3 p-2 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-100 dark:border-zinc-700">
                            <p class="text-[10px] italic text-zinc-600 dark:text-zinc-400 truncate">"{{ $flyer->quote_text }}"</p>
                        </div>
                    @endif

                    <div class="mt-auto pt-4 flex justify-between items-center border-t border-zinc-100 dark:border-zinc-800">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">{{ $flyer->created_at->format('d/m/Y') }}</span>
                        @if($loop->first && $flyer->is_active && $flyers->currentPage() === 1)
                            <span class="text-[10px] text-festa-gold font-bold uppercase tracking-wider flex items-center gap-1">
                                <flux:icon.eye class="size-3" /> En ligne
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if($flyers->isEmpty())
        <div class="bg-zinc-50 dark:bg-zinc-900/50 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-3xl p-12 text-center">
            <div class="bg-white dark:bg-zinc-800 p-4 rounded-full inline-block mb-4 shadow-sm">
                <flux:icon.document class="size-8 text-zinc-400" />
            </div>
            <h4 class="text-zinc-900 dark:text-white font-bold text-lg">Aucun flyer</h4>
            <p class="text-zinc-500 text-sm mt-1 mb-6">Commencez par ajouter votre premier visuel promotionnel.</p>
            <flux:button variant="ghost" wire:click="create">Créer un flyer</flux:button>
        </div>
    @endif

    <div class="mt-4">{{ $flyers->links() }}</div>

    <!-- Modal Form (Create/Edit) -->
    <flux:modal wire:model="showModal" class="min-w-[22rem] max-w-lg bg-white dark:bg-zinc-900">
        <form wire:submit="save" class="space-y-6">
            <div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ $flyerId ? 'Modifier' : 'Ajouter' }} un flyer</h3>
                <p class="text-sm text-zinc-500">Configurez l'image, les textes et la citation.</p>
            </div>
            
            <div class="space-y-5 max-h-[70vh] overflow-y-auto pr-2 custom-scrollbar">
                <flux:input label="Titre interne" placeholder="Ex: Affiche 2026 - Version 1" wire:model="title" required />
                <flux:input label="URL de l'image" placeholder="https://..." wire:model.live="image_url" required />
                
                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="Sous-titre (Or)" placeholder="Ex: L'Héritage" wire:model="subtitle" />
                    <flux:input label="Grand Titre" placeholder="Ex: Terre de Feu & d'Or" wire:model="headline" />
                </div>
                
                <flux:textarea label="Description" placeholder="Texte de présentation..." rows="4" wire:model="description" />

                <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-xl border border-zinc-100 dark:border-zinc-700">
                    <p class="text-xs font-bold text-zinc-500 mb-3 uppercase tracking-widest">Citation (Encadré blanc)</p>
                    <div class="space-y-3">
                        <flux:input label="Citation" placeholder="Ex: La force de l'unité." wire:model="quote_text" />
                        <flux:input label="Auteur / Légende" placeholder="Ex: Devise Castellers" wire:model="quote_author" />
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-xl border border-zinc-100 dark:border-zinc-700">
                    <flux:switch wire:model="is_active" />
                    <div>
                        <span class="block text-sm font-bold text-zinc-900 dark:text-white">Activer immédiatement</span>
                        <span class="text-xs text-zinc-500">Ce contenu remplacera l'actuel sur la page d'accueil.</span>
                    </div>
                </div>

                @if($image_url)
                    <div class="mt-4 animate-reveal">
                        <p class="text-xs font-bold text-zinc-500 mb-2 uppercase tracking-widest">Aperçu Visuel</p>
                        <div class="aspect-[4/5] rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 shadow-lg relative">
                            <img src="{{ $image_url }}" class="w-full h-full object-cover">
                            @if($quote_text)
                                <div class="absolute bottom-4 right-4 bg-white text-zinc-900 p-3 rounded-lg shadow-xl max-w-[150px]">
                                    <p class="font-bold text-xs mb-0.5">"{{ $quote_text }}"</p>
                                    <p class="text-[8px] text-zinc-500 uppercase tracking-widest">{{ $quote_author }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <flux:button variant="ghost" class="flex-1" wire:click="$set('showModal', false)">Annuler</flux:button>
                <flux:button variant="primary" type="submit" class="flex-1 bg-festa-gold text-festa-red-dark font-bold border-none hover:bg-festa-gold-light">
                    {{ $flyerId ? 'Mettre à jour' : 'Enregistrer' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Active Management Modal (REDESIGNED) -->
    <flux:modal wire:model="showActiveModal" class="min-w-[22rem] max-w-2xl bg-white dark:bg-zinc-900">
        <form wire:submit="saveActiveSelection" class="h-[80vh] flex flex-col">
            <div class="pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <h3 class="text-xl font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    <flux:icon.eye class="size-6 text-festa-gold" />
                    Gérer la diffusion
                </h3>
                <p class="text-sm text-zinc-500 mt-1">Sélectionnez les flyers à afficher (Cochez pour activer).</p>
            </div>

            <div class="flex-1 overflow-y-auto py-6 pr-2 custom-scrollbar space-y-3">
                @foreach($allFlyers as $f)
                    <div wire:click="toggleSelection({{ $f->id }})" 
                         class="group relative flex items-center gap-4 p-3 rounded-2xl border-2 cursor-pointer transition-all duration-300
                         {{ in_array($f->id, $selectedActiveFlyers) 
                            ? 'border-festa-gold bg-festa-gold/5 dark:bg-festa-gold/10' 
                            : 'border-zinc-100 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 bg-white dark:bg-zinc-900' 
                         }}">
                        
                        <!-- Checkbox Visual -->
                        <div class="size-6 rounded-full border-2 flex items-center justify-center transition-colors
                            {{ in_array($f->id, $selectedActiveFlyers) ? 'border-festa-gold bg-festa-gold' : 'border-zinc-300 dark:border-zinc-600 group-hover:border-zinc-400' }}">
                            @if(in_array($f->id, $selectedActiveFlyers))
                                <flux:icon.check class="size-4 text-festa-red-dark" />
                            @endif
                        </div>

                        <!-- Image Thumbnail -->
                        <div class="size-16 rounded-xl bg-zinc-100 dark:bg-zinc-800 shrink-0 overflow-hidden shadow-sm border border-zinc-200 dark:border-zinc-700">
                            <img src="{{ $f->image_url }}" class="w-full h-full object-cover">
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-zinc-900 dark:text-white truncate {{ in_array($f->id, $selectedActiveFlyers) ? 'text-festa-gold-dark dark:text-festa-gold' : '' }}">
                                {{ $f->title }}
                            </p>
                            <p class="text-xs text-zinc-500 truncate mt-0.5">{{ $f->headline }}</p>
                            <p class="text-[10px] text-zinc-400 font-mono mt-1">{{ $f->created_at->format('d/m/Y') }}</p>
                        </div>

                        <!-- Status Label -->
                        @if(in_array($f->id, $selectedActiveFlyers))
                            <span class="absolute top-3 right-3 text-[10px] font-black text-festa-gold uppercase tracking-widest bg-festa-gold/10 px-2 py-0.5 rounded-full">
                                Actif
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-white dark:bg-zinc-900">
                <p class="text-[10px] text-zinc-400 italic max-w-xs leading-tight">
                    * Le flyer actif le plus récent sera celui visible sur la page d'accueil.
                </p>
                <div class="flex gap-3">
                    <flux:button variant="ghost" wire:click="$set('showActiveModal', false)">Annuler</flux:button>
                    <flux:button variant="primary" type="submit" class="bg-festa-gold text-festa-red-dark font-bold border-none hover:bg-festa-gold-light shadow-lg">
                        Valider la sélection
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 4px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a1a1aa; }
    </style>
</div>
