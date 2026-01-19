<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

new class extends Component {
    use WithPagination, WithFileUploads;

    public $search = '';
    
    // Create/Edit Product
    public $productId = null;
    public $name = '';
    public $description = '';
    public $price = '';
    public $stock = 0;
    public $image_url = '';
    public $image_file = null;
    public $has_sizes = false;
    
    // Size-specific stocks
    public array $sizeStocks = [
        'S' => 0,
        'M' => 0,
        'L' => 0,
        'XL' => 0,
        'XXL' => 0,
    ];
    
    public $showModal = false;

    public function with(): array
    {
        return [
            'products' => Product::with('variants')->where('name', 'like', "%{$this->search}%")
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ];
    }

    public function create()
    {
        $this->reset(['productId', 'name', 'description', 'price', 'stock', 'image_url', 'has_sizes', 'image_file']);
        $this->sizeStocks = ['S' => 0, 'M' => 0, 'L' => 0, 'XL' => 0, 'XXL' => 0];
        $this->showModal = true;
    }

    public function edit(Product $product)
    {
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->image_url = $product->image_url;
        $this->has_sizes = $product->has_sizes;
        $this->image_file = null;
        
        // Load size stocks
        $this->sizeStocks = ['S' => 0, 'M' => 0, 'L' => 0, 'XL' => 0, 'XXL' => 0];
        foreach ($product->variants as $variant) {
            $this->sizeStocks[$variant->size] = $variant->stock;
        }
        
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_url' => 'required_without:image_file|nullable|string',
            'image_file' => 'nullable|image|max:2048', // 2MB Max
            'has_sizes' => 'boolean',
            'sizeStocks.*' => 'integer|min:0',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'has_sizes' => $this->has_sizes,
        ];

        if ($this->image_file) {
            $data['image_url'] = $this->image_file->store('products', 'public');
        } else {
            $data['image_url'] = $this->image_url;
        }

        $product = Product::updateOrCreate(['id' => $this->productId], $data);

        // Manage variants
        if ($this->has_sizes) {
            foreach ($this->sizeStocks as $size => $qty) {
                ProductVariant::updateOrCreate(
                    ['product_id' => $product->id, 'size' => $size],
                    ['stock' => $qty]
                );
            }
        } else {
            // If sizes disabled, optionally clear variants? 
            // Better to keep them but they won't be used.
        }

        $this->showModal = false;
        $this->dispatch('product-saved', message: 'Produit enregistré avec succès.');
    }

    public function delete(Product $product)
    {
        $product->delete();
        $this->dispatch('product-saved', message: 'Produit supprimé.');
    }
}; ?>

<div class="space-y-6">
    <!-- Notifications -->
    <div 
        x-data="{ show: false, message: '' }"
        x-on:product-saved.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
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
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Gestion Boutique</h1>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1">Gérez les stocks globaux et par taille.</p>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create">Ajouter un produit</flux:button>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-500 uppercase tracking-wider">Prix</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-500 uppercase tracking-wider">Disponibilité</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-zinc-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($products as $product)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $product->image_url }}" class="size-12 rounded-lg object-cover bg-zinc-100 border border-white/10">
                                    <div>
                                        <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ $product->name }}</div>
                                        <div class="text-xs text-zinc-500 truncate max-w-xs">{{ $product->description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-festa-gold">
                                {{ number_format($product->price, 2) }}€
                            </td>
                            <td class="px-6 py-4">
                                @if($product->has_sizes)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($product->variants as $v)
                                            <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded border 
                                                {{ $v->stock > 5 ? 'bg-zinc-100 text-zinc-600 border-zinc-200' : ($v->stock > 0 ? 'bg-amber-500/10 text-amber-500 border-amber-500/20' : 'bg-red-500/10 text-red-500 border-red-500/20') }}">
                                                {{ $v->size }}: {{ $v->stock }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $product->stock > 10 ? 'bg-green-500/10 text-green-500 border-green-500/20' : ($product->stock > 0 ? 'bg-amber-500/10 text-amber-500 border-amber-500/20' : 'bg-red-500/10 text-red-500 border-red-500/20') }}">
                                        {{ $product->stock }} en stock
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <flux:dropdown>
                                    <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil-square" wire:click="edit({{ $product->id }})">Modifier</flux:menu.item>
                                        <flux:menu.item icon="trash" variant="danger" wire:click="delete({{ $product->id }})" wire:confirm="Supprimer ce produit ?">Supprimer</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-zinc-500">Aucun produit en boutique.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Product Modal -->
    <flux:modal wire:model="showModal" class="min-w-[22rem] max-w-lg bg-white dark:bg-zinc-900">
        <form wire:submit="save" class="space-y-6">
            <div>
                <h3 class="text-lg font-bold">{{ $productId ? 'Modifier le produit' : 'Ajouter un produit' }}</h3>
                <p class="text-sm text-zinc-500">Configurez les informations et les stocks.</p>
            </div>

            <div class="space-y-4">
                <flux:input label="Nom du produit" wire:model="name" required />
                <flux:textarea label="Description" wire:model="description" rows="2" />
                
                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="Prix (€)" type="number" step="0.01" wire:model="price" required />
                    <div class="flex items-center h-full pt-6">
                        <flux:checkbox label="Gérer les tailles (S, M, L...)" wire:model.live="has_sizes" />
                    </div>
                </div>

                @if($has_sizes)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <p class="text-[10px] font-black uppercase text-zinc-500 mb-4 tracking-widest text-center">Stock par taille</p>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $s)
                                <div class="space-y-1">
                                    <label class="block text-center text-[10px] font-bold">{{ $s }}</label>
                                    <input type="number" wire:model="sizeStocks.{{ $s }}" class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg px-1 py-2 text-center text-sm outline-none focus:border-festa-gold transition">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <flux:input label="Stock global" type="number" wire:model="stock" required />
                @endif

                <div class="space-y-3">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Image du produit</label>
                    <div class="grid grid-cols-1 gap-4">
                        <flux:input label="URL externe (optionnel)" wire:model.live="image_url" placeholder="https://..." />
                        
                        <div class="relative group">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-xl cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <flux:icon.cloud-arrow-up class="size-8 text-zinc-400 mb-2" />
                                    <p class="text-xs text-zinc-500"><span class="font-bold text-festa-gold">Cliquez pour uploader</span> ou glisser-déposer</p>
                                    <p class="text-[10px] text-zinc-400 mt-1">PNG, JPG ou WEBP (Max. 2Mo)</p>
                                </div>
                                <input type="file" wire:model="image_file" class="hidden" accept="image/*" />
                            </label>
                        </div>
                    </div>
                </div>
                
                @if($image_file || $image_url)
                    <div class="mt-2 p-2 border border-zinc-100 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-900/50">
                        <p class="text-[10px] font-bold text-zinc-500 uppercase mb-2 tracking-widest">Aperçu de l'image sélectionnée :</p>
                        <div class="h-40 w-full overflow-hidden rounded-lg border border-white/5 bg-black/20">
                            @if($image_file)
                                <img src="{{ $image_file->temporaryUrl() }}" class="w-full h-full object-contain">
                            @else
                                <img src="{{ $image_url }}" class="w-full h-full object-contain">
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex gap-3">
                <flux:button variant="ghost" class="flex-1" wire:click="$set('showModal', false)">Annuler</flux:button>
                <flux:button variant="primary" type="submit" class="flex-1 bg-festa-gold text-festa-red-dark font-bold uppercase tracking-wider">
                    Enregistrer
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
