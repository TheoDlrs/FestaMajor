<?php

use Livewire\Volt\Component;
use App\Models\GalleryImage;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $imageId = null;
    public $image_url = '';
    public $is_featured = false;
    public $order = 0;
    public $showModal = false;

    public function with(): array
    {
        return [
            'images' => GalleryImage::orderBy('order', 'asc')->paginate(12),
        ];
    }

    public function create()
    {
        $this->reset(['imageId', 'image_url', 'is_featured', 'order']);
        $this->showModal = true;
    }

    public function edit(GalleryImage $image)
    {
        $this->imageId = $image->id;
        $this->image_url = $image->image_url;
        $this->is_featured = $image->is_featured;
        $this->order = $image->order;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'image_url' => 'required|url',
            'is_featured' => 'boolean',
            'order' => 'integer',
        ]);

        GalleryImage::updateOrCreate(['id' => $this->imageId], [
            'image_url' => $this->image_url,
            'is_featured' => $this->is_featured,
            'order' => $this->order,
        ]);

        $this->showModal = false;
        $this->dispatch('image-saved', message: 'Image enregistrée.');
    }

    public function delete(GalleryImage $image)
    {
        $image->delete();
        $this->dispatch('image-saved', message: 'Image supprimée.');
    }
}; ?>

<div class="space-y-6">
    <div 
        x-data="{ show: false, message: '' }"
        x-on:image-saved.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
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
            <h1 class="text-2xl font-bold">Album Photo</h1>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1">Gérez la galerie souvenirs.</p>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create">Ajouter une photo</flux:button>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($images as $img)
            <div class="group relative aspect-square bg-zinc-900 rounded-xl overflow-hidden border border-white/5">
                <img src="{{ $img->image_url }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                    <flux:button size="sm" icon="pencil-square" wire:click="edit({{ $img->id }})" />
                    <flux:button size="sm" variant="danger" icon="trash" wire:click="delete({{ $img->id }})" />
                </div>
                @if($img->is_featured)
                    <div class="absolute top-2 left-2 px-2 py-0.5 bg-festa-gold text-festa-red-dark text-[8px] font-black uppercase rounded">Large</div>
                @endif
                <div class="absolute bottom-2 right-2 px-2 py-0.5 bg-black/50 text-white text-[8px] font-mono rounded">#{{ $img->order }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $images->links() }}</div>

    <flux:modal wire:model="showModal" class="min-w-[22rem] max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <h3 class="text-lg font-bold">{{ $imageId ? 'Modifier' : 'Ajouter' }} une photo</h3>
            <div class="space-y-4">
                <flux:input label="URL de l'image" wire:model.live="image_url" required />
                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="Ordre" type="number" wire:model="order" />
                    <div class="flex items-center pt-6">
                        <flux:checkbox label="Format Large (Grid)" wire:model="is_featured" />
                    </div>
                </div>
                @if($image_url)
                    <img src="{{ $image_url }}" class="h-32 w-full object-contain bg-zinc-100 rounded-lg">
                @endif
            </div>
            <div class="flex gap-3">
                <flux:button variant="ghost" class="flex-1" wire:click="$set('showModal', false)">Annuler</flux:button>
                <flux:button variant="primary" type="submit" class="flex-1 bg-festa-gold text-black font-bold">Enregistrer</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
