<?php

use Livewire\Volt\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public function getNotificationsProperty()
    {
        return Auth::user()->notifications()->latest()->take(5)->get();
    }

    public function getUnreadCountProperty()
    {
        return Auth::user()->notifications()->whereNull('read_at')->count();
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->update(['read_at' => now()]);
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
    }
}; ?>

<div x-data="{ open: false }" class="relative">
    <!-- Notification Bell -->
    <button @click="open = !open" class="relative p-2 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition group">
        <flux:icon.bell class="size-6 text-zinc-400 group-hover:text-festa-gold transition" />
        @if($this->unreadCount > 0)
            <span class="absolute top-0 right-0 size-4 bg-festa-red text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-zinc-950 animate-bounce">
                {{ $this->unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition
         class="absolute right-0 mt-4 w-80 bg-zinc-900 border border-white/10 rounded-2xl shadow-2xl z-50 overflow-hidden"
         style="display: none;">
        
        <div class="px-4 py-3 border-b border-white/5 flex justify-between items-center bg-white/5">
            <h4 class="text-xs font-black uppercase tracking-widest text-white">Notifications</h4>
            @if($this->unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[10px] text-festa-gold hover:underline">Tout lire</button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto custom-scrollbar">
            @forelse($this->notifications as $notif)
                <div class="p-4 border-b border-white/5 hover:bg-white/5 transition relative group {{ !$notif->read_at ? 'bg-festa-gold/5' : '' }}">
                    <div class="flex gap-3">
                        <div class="shrink-0 pt-1">
                            @if($notif->type === 'success')
                                <flux:icon.check-circle class="size-4 text-green-500" />
                            @elseif($notif->type === 'warning')
                                <flux:icon.exclamation-triangle class="size-4 text-amber-500" />
                            @else
                                <flux:icon.information-circle class="size-4 text-blue-500" />
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-white mb-1">{{ $notif->title }}</p>
                            <p class="text-[11px] text-zinc-400 leading-relaxed">{{ $notif->message }}</p>
                            <p class="text-[9px] text-zinc-600 mt-2">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                        @if(!$notif->read_at)
                            <button wire:click="markAsRead({{ $notif->id }})" class="shrink-0 opacity-0 group-hover:opacity-100 transition">
                                <flux:icon.x-mark class="size-3 text-zinc-500 hover:text-white" />
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <flux:icon.bell-slash class="size-8 text-zinc-700 mx-auto mb-3" />
                    <p class="text-xs text-zinc-500">Aucune notification pour le moment.</p>
                </div>
            @endforelse
        </div>

        <div class="p-3 bg-black/20 text-center">
            <p class="text-[9px] text-zinc-600 uppercase font-bold tracking-widest">Festa Major 2026</p>
        </div>
    </div>
</div>
