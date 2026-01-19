<?php

use Livewire\Volt\Component;
use App\Models\ProgramEvent;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    
    public $eventId = null;
    public $title = '';
    public $time = '';
    public $description = '';
    public $image_url = '';
    public $is_featured = false;
    public $order = 0;
    
    public $showModal = false;

    public function with(): array
    {
        return [
            'events' => ProgramEvent::where('title', 'like', "%{$this->search}%")
                ->orderBy('order', 'asc')
                ->paginate(10),
        ];
    }

    public function create()
    {
        $this->reset(['eventId', 'title', 'time', 'description', 'image_url', 'is_featured', 'order']);
        $this->showModal = true;
    }

    public function edit(ProgramEvent $event)
    {
        $this->eventId = $event->id;
        $this->title = $event->title;
        $this->time = $event->time;
        $this->description = $event->description;
        $this->image_url = $event->image_url;
        $this->is_featured = $event->is_featured;
        $this->order = $event->order;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'time' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'required|url',
            'is_featured' => 'boolean',
            'order' => 'integer',
        ]);

        ProgramEvent::updateOrCreate(['id' => $this->eventId], [
            'title' => $this->title,
            'time' => $this->time,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'is_featured' => $this->is_featured,
            'order' => $this->order,
        ]);

        $this->showModal = false;
        $this->dispatch('event-saved', message: 'Événement enregistré.');
    }

    public function delete(ProgramEvent $event)
    {
        $event->delete();
        $this->dispatch('event-saved', message: 'Événement supprimé.');
    }
}; ?>

<div class="space-y-6">
    <div 
        x-data="{ show: false, message: '' }"
        x-on:event-saved.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition
        class="fixed top-24 right-6 z-50 bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3"
        style="display: none;"
    >
        <flux:icon.check-circle class="size-6 text-white" />
        <p class="text-sm font-bold" x-text="message"></p>
    </div>

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Gestion du Programme</h1>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1">Gérez les temps forts du festival.</p>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create">Ajouter un événement</flux:button>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-500 uppercase tracking-wider">Ordre</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-500 uppercase tracking-wider">Événement</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-500 uppercase tracking-wider">Horaire</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-zinc-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($events as $event)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">#{{ $event->order }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $event->image_url }}" class="size-12 rounded-lg object-cover bg-zinc-100">
                                    <div class="text-sm font-bold">{{ $event->title }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $event->time }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($event->is_featured)
                                    <span class="text-[9px] font-black uppercase bg-festa-red/10 text-festa-red px-2 py-0.5 rounded-full border border-festa-red/20">Événement Phare</span>
                                @else
                                    <span class="text-[9px] font-black uppercase bg-zinc-100 text-zinc-500 px-2 py-0.5 rounded-full border border-zinc-200">Standard</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <flux:dropdown>
                                    <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil-square" wire:click="edit({{ $event->id }})">Modifier</flux:menu.item>
                                        <flux:menu.item icon="trash" variant="danger" wire:click="delete({{ $event->id }})" wire:confirm="Supprimer cet événement ?">Supprimer</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-zinc-500">Aucun événement.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Event Modal -->
    <flux:modal wire:model="showModal" class="min-w-[22rem] max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <h3 class="text-lg font-bold">{{ $eventId ? 'Modifier' : 'Ajouter' }} l'événement</h3>
            <div class="space-y-4">
                <flux:input label="Titre" wire:model="title" required />
                <flux:input label="Horaire / Date (ex: Sam. 13 Sept. — 23h00)" wire:model="time" required />
                <flux:textarea label="Description" wire:model="description" />
                <flux:input label="URL de l'image" wire:model="image_url" required />
                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="Ordre d'affichage" type="number" wire:model="order" />
                    <div class="flex items-center pt-6">
                        <flux:checkbox label="Événement Phare" wire:model="is_featured" />
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <flux:button variant="ghost" class="flex-1" wire:click="$set('showModal', false)">Annuler</flux:button>
                <flux:button variant="primary" type="submit" class="flex-1 bg-festa-gold text-black font-bold">Enregistrer</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
