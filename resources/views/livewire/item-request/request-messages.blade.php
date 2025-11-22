<div wire:poll.2s>
    <div class="space-y-3 max-h-80 overflow-y-auto p-3 border rounded-lg bg-gray-50">
        @foreach ($messages as $m)
            <div class="p-2 rounded-lg bg-white shadow-sm">
                <div class="text-sm font-semibold">{{ $m->user->name }}</div>
                <div class="text-sm">{{ $m->message }}</div>
                <div class="text-xs text-gray-500">{{ $m->created_at->diffForHumans() }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-3 flex gap-2">
        <input
            wire:model.defer="message"
            type="text"
            placeholder="Type a message..."
            class="flex-1 rounded-lg border-gray-300"
        />

        <x-filament::button wire:click="send">
            Send
        </x-filament::button>
    </div>
</div>
