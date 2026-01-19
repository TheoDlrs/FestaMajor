<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

new class extends Component {
    use WithPagination;

    // Filters
    public $search = '';
    public $role = '';
    public $dateFrom = '';
    public $dateTo = '';

    // Sort
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Edit User
    public $editingUser = null;
    public $editName = '';
    public $editEmail = '';
    public $editRole = '';
    public $editPassword = '';
    public $showEditModal = false;

    public function with(): array
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->role, fn($q) => $q->where('role', $this->role))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return [
            'users' => $users,
        ];
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function editUser(User $user)
    {
        $this->editingUser = $user;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editRole = $user->role;
        $this->editPassword = '';
        $this->showEditModal = true;
    }

    public function updateUser()
    {
        $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editEmail' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingUser->id)],
            'editRole' => ['required', 'in:admin,member'],
            'editPassword' => ['nullable', 'string', 'min:8'],
        ]);

        $this->editingUser->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
            'role' => $this->editRole,
        ]);

        if ($this->editPassword) {
            $this->editingUser->update([
                'password' => Hash::make($this->editPassword),
            ]);
        }

        $this->showEditModal = false;
        $this->dispatch('user-updated', message: "Membre mis à jour avec succès.");
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return;
        }
        $user->delete();
        $this->dispatch('user-updated', message: "Membre supprimé.");
    }
}; ?>

<div class="space-y-6">
    <!-- Notifications -->
    <div 
        x-data="{ show: false, message: '' }"
        x-on:user-updated.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition
        class="fixed top-24 right-6 z-50 bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3"
        style="display: none;"
    >
        <flux:icon.check-circle class="size-6 text-white" />
        <p class="text-sm font-bold" x-text="message"></p>
    </div>

    <!-- Header & Filters -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Gestion des Membres</h1>
                <p class="text-zinc-500 dark:text-zinc-400 mt-1">Gérez les comptes et les permissions.</p>
            </div>
            
            <div class="flex flex-wrap gap-4 w-full lg:w-auto">
                <div class="flex-1 min-w-[200px]">
                    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Nom ou Email..." />
                </div>
                
                <div class="w-40">
                    <select wire:model.live="role" class="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-700 dark:text-zinc-300 outline-none focus:ring-2 focus:ring-festa-gold/50">
                        <option value="">Tous les rôles</option>
                        <option value="member">Membres</option>
                        <option value="admin">Admins</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <input type="date" wire:model.live="dateFrom" class="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-700 dark:text-zinc-300 outline-none">
                    <span class="text-zinc-400">au</span>
                    <input type="date" wire:model.live="dateTo" class="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-700 dark:text-zinc-300 outline-none">
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="overflow-hidden border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-zinc-500 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-300" wire:click="sortBy('name')">
                            Nom
                            @if($sortField === 'name') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-zinc-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-zinc-500 uppercase tracking-wider">Rôle</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-zinc-500 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-300" wire:click="sortBy('created_at')">
                            Inscrit le
                            @if($sortField === 'created_at') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-zinc-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($users as $user)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="size-10 rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 mr-3 shrink-0">
                                        @if($user->avatar_path)
                                            <img src="{{ asset('storage/' . $user->avatar_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <flux:avatar :name="$user->name" class="w-full h-full" />
                                        @endif
                                    </div>
                                    <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2.5 py-0.5 inline-flex text-[10px] font-black uppercase tracking-widest rounded-full border {{ $user->role === 'admin' ? 'bg-red-500/10 text-red-500 border-red-500/20' : 'bg-festa-gold/10 text-festa-gold-dark dark:text-festa-gold border-festa-gold/20' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <flux:dropdown>
                                    <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil-square" wire:click="editUser({{ $user->id }})">Modifier</flux:menu.item>
                                        <flux:menu.item icon="envelope" href="mailto:{{ $user->email }}">Contacter</flux:menu.item>
                                        @if($user->id !== auth()->id())
                                            <flux:menu.item icon="trash" variant="danger" wire:click="deleteUser({{ $user->id }})" wire:confirm="ATTENTION : Cette action est irréversible. Êtes-vous certain de vouloir supprimer définitivement ce compte membre ?">Supprimer</flux:menu.item>
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-zinc-500">
                                Aucun membre trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    <!-- Edit User Modal -->
    <flux:modal wire:model="showEditModal" class="min-w-[22rem] max-w-lg bg-white dark:bg-zinc-900">
        <form wire:submit="updateUser" class="space-y-6">
            <div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Modifier le Membre</h3>
                <p class="text-sm text-zinc-500">Mettez à jour les informations du compte.</p>
            </div>

            <div class="space-y-4">
                <flux:input label="Nom complet" wire:model="editName" required />
                <flux:input label="Adresse Email" type="email" wire:model="editEmail" required />
                
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Rôle</label>
                    <select wire:model="editRole" class="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm text-zinc-700 dark:text-zinc-300 outline-none focus:ring-2 focus:ring-festa-gold/50">
                        <option value="member">Membre</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>

                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:input label="Nouveau mot de passe" type="password" wire:model="editPassword" placeholder="Laisser vide pour ne pas changer" />
                    <p class="text-[10px] text-zinc-500 mt-1">Minimum 8 caractères.</p>
                </div>
            </div>

            <div class="flex gap-3">
                <flux:button variant="ghost" class="flex-1" wire:click="$set('showEditModal', false)">Annuler</flux:button>
                <flux:button variant="primary" type="submit" class="flex-1 bg-festa-gold text-festa-red-dark font-bold uppercase tracking-wider">
                    Enregistrer
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
