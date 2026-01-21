<?php

use Livewire\Volt\Component;
use App\Models\GalleryImage;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

new class extends Component {
    use WithPagination, WithFileUploads;

    public $imageId = null;
    public $image_url = '';
    public $photo;
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
        $this->reset(['imageId', 'image_url', 'photo', 'is_featured', 'order']);
        $this->showModal = true;
    }

    public function edit(GalleryImage $image)
    {
        $this->imageId = $image->id;
        $this->image_url = $image->image_url;
        $this->photo = null;
        $this->is_featured = $image->is_featured;
        $this->order = $image->order;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'image_url' => 'required_without:photo',
            'photo' => 'nullable|image|max:10240', // 10MB max
            'is_featured' => 'boolean',
            'order' => 'integer',
        ]);

        if ($this->photo) {
            $path = $this->photo->store('gallery', 'public');
            $this->image_url = Storage::url($path);
        }

        GalleryImage::updateOrCreate(['id' => $this->imageId], [
            'image_url' => $this->image_url,
            'is_featured' => $this->is_featured,
            'order' => $this->order,
        ]);

        Cache::forget('gallery_images');

        $this->showModal = false;
        $this->dispatch('image-saved', message: 'Image enregistrée.');
    }

    public function delete(GalleryImage $image)
    {
        $image->delete();
        Cache::forget('gallery_images');
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
                
                <div>
                    <label class="block text-sm font-medium text-zinc-800 dark:text-zinc-200 mb-2">Importer une image (Prioritaire)</label>
                    <input type="file" wire:model.live="photo" accept="image/png, image/jpeg, image/jpg, image/webp" class="block w-full text-sm text-zinc-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-festa-gold/10 file:text-festa-gold
                        hover:file:bg-festa-gold/20
                        mt-2
                    "/>
                    @error('photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="text-center text-xs text-zinc-500 uppercase tracking-widest font-bold">OU</div>

                <flux:input label="URL de l'image (Lien externe)" wire:model="image_url" placeholder="https://..." />
                
                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="Ordre" type="number" wire:model="order" />
                    <div class="flex items-center pt-6">
                        <flux:checkbox label="Format Large (Grid)" wire:model="is_featured" />
                    </div>
                </div>

                @if($photo)
                    <div class="relative">
                        <p class="text-xs text-zinc-500 mb-2">Aperçu du fichier :</p>
                        @if($photo->isPreviewable())
                            <img src="{{ $photo->temporaryUrl() }}" class="h-32 w-full object-contain bg-zinc-100 rounded-lg">
                        @else
                            <div class="h-32 w-full flex items-center justify-center bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-500 text-xs p-4 text-center">
                                Format non supporté pour l'aperçu.
                            </div>
                        @endif
                    </div>
                @elseif($image_url)
                    <div class="relative">
                        <p class="text-xs text-zinc-500 mb-2">Aperçu actuel :</p>
                        <img src="{{ $image_url }}" class="h-32 w-full object-contain bg-zinc-100 rounded-lg">
                    </div>
                @endif
            </div>
            <div class="flex gap-3">
                <flux:button variant="ghost" class="flex-1" wire:click="$set('showModal', false)">Annuler</flux:button>
                <flux:button variant="primary" type="submit" class="flex-1 bg-festa-gold text-black font-bold">Enregistrer</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
