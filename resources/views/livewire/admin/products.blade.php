<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;

new class extends Component {
    use WithPagination;

    public $editing = null;
    public $name = '';
    public $description = '';
    public $price = '';
    public $image_url = '';

    public function with(): array
    {
        return [
            'products' => Product::paginate(10),
        ];
    }

    public function edit(Product $product)
    {
        $this->editing = $product->id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->image_url = $product->image_url;
    }

    public function cancel()
    {
        $this->editing = null;
        $this->reset(['name', 'description', 'price', 'image_url']);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image_url' => 'required|url',
        ]);

        $product = Product::find($this->editing);
        $product->update([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'image_url' => $this->image_url,
        ]);

        $this->editing = null;
        $this->dispatch('product-updated');
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Gestion de la Boutique</h1>
        <p class="text-zinc-500 dark:text-zinc-400">Modifier les produits en vente.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($products as $product)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm flex flex-col">
                <div class="aspect-square relative overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
                
                <div class="p-5 flex-1 flex flex-col">
                    @if ($editing === $product->id)
                        <div class="space-y-4">
                            <flux:input wire:model="name" label="Nom" />
                            <flux:input wire:model="price" label="Prix (€)" type="number" step="0.01" />
                            <flux:textarea wire:model="description" label="Description" rows="2" />
                            <flux:input wire:model="image_url" label="URL Image" />
                            
                            <div class="flex gap-2 pt-2">
                                <flux:button wire:click="save" variant="primary" size="sm" class="flex-1">Sauvegarder</flux:button>
                                <flux:button wire:click="cancel" size="sm">Annuler</flux:button>
                            </div>
                        </div>
                    @else
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-lg text-zinc-900 dark:text-white">{{ $product->name }}</h3>
                            <span class="font-bold text-festa-gold">{{ number_format($product->price, 2) }}€</span>
                        </div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4 flex-1">{{ $product->description }}</p>
                        
                        <flux:button wire:click="edit({{ $product->id }})" icon="pencil-square" size="sm" class="w-full">Modifier</flux:button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>