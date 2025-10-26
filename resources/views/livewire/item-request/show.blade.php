<div class="min-h-screen bg-white dark:bg-gray-900 py-4 px-3 sm:py-8 sm:px-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                    Item Request Details
                </h1>
            </div>

            {{-- Session Alerts --}}
            <div class="p-4 sm:p-6">
                @if (session()->has('success'))
                    <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Request Details --}}
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 sm:p-4 mb-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        {{-- Farm --}}
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Farm</p>
                            <p class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">{{ $itemRequest->farm->name }}</p>
                        </div>

                        {{-- Item --}}
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Item</p>
                            <p class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">{{ $itemRequest->inventoryItem->name }}</p>
                        </div>

                        {{-- Quantity --}}
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Quantity</p>
                            <p class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">{{ $itemRequest->quantity }}</p>
                        </div>

                        {{-- Status --}}
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</p>
                            <span class="inline-flex px-2 py-1 text-xs sm:text-sm font-medium rounded-full bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white">
                                {{ ucfirst($itemRequest->status) }}
                            </span>
                        </div>

                        {{-- Current Stock --}}
                        @if($itemRequest->inventoryItem)
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Current Stock</p>
                            <input type="text"
                                   value="{{ $itemRequest->inventoryItem->current_stock }} {{ $itemRequest->inventoryItem->unit }}"
                                   class="w-full bg-gray-50 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-900 dark:text-white text-xs sm:text-sm rounded-lg p-2 font-semibold cursor-not-allowed"
                                   disabled
                                   readonly>
                        </div>
                        @endif

                        {{-- Requested At --}}
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Requested At</p>
                            <p class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">{{ $itemRequest->requested_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>

                    {{-- Notes Section --}}
                    @if($itemRequest->notes)
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</p>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $itemRequest->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Request Discussion --}}
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3">Request Discussion</h2>

                    {{-- Message Alerts --}}
                    @if (session()->has('message-success'))
                        <div class="mb-3 p-3 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg text-sm">
                            {{ session('message-success') }}
                        </div>
                    @endif

                    @if (session()->has('message-error'))
                        <div class="mb-3 p-3 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg text-sm">
                            {{ session('message-error') }}
                        </div>
                    @endif

                    {{-- Messages Container --}}
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 space-y-3" wire:poll.5s="refreshMessages">
                        @if(count($messages) > 0)
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @foreach(array_reverse($messages) as $message)
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border @if($message['is_admin_message']) border-l-4 @endif border-gray-200 dark:border-gray-600">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="font-semibold text-gray-900 dark:text-white text-xs sm:text-sm">
                                                        {{ $message['user']['name'] }}
                                                    </span>
                                                    @if($message['is_admin_message'])
                                                        <span class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded-full bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-white">
                                                            Admin
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-gray-700 dark:text-gray-300 text-xs sm:text-sm leading-relaxed whitespace-pre-wrap break-words">{{ $message['message'] }}</p>
                                            </div>
                                            <div class="flex-shrink-0 text-xs text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($message['created_at'])->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400 text-sm">No messages yet. Start the conversation!</p>
                            </div>
                        @endif

                        {{-- Message Input Form --}}
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
                            <form wire:submit.prevent="sendMessage" class="space-y-3">
                                <div>
                                    <label for="newMessage" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Your Message
                                    </label>
                                    <textarea
                                        wire:model="newMessage"
                                        id="newMessage"
                                        rows="3"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-600 dark:text-white resize-none"
                                        placeholder="Type your message here..."
                                    ></textarea>
                                    @error('newMessage')
                                        <p class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="bg-gray-900 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg shadow transition flex items-center gap-2 text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        Send
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Status History --}}
                @if(count($itemRequest->statuses ?? []) > 0)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3">Status History</h2>
                        <div class="space-y-2">
                            @foreach ($itemRequest->statuses as $status)
                                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-white">
                                                {{ ucfirst($status->status) }}
                                            </span>
                                            <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                                {{ $status->changedBy->name }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $status->created_at->format('M j, Y') }}
                                        </span>
                                    </div>
                                    @if($status->notes)
                                        <p class="mt-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400 italic">
                                            "{{ $status->notes }}"
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Action Buttons --}}
                @if ($itemRequest->status === 'pending')
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button wire:click="edit({{ $itemRequest->id }})"
                                class="flex-1 bg-gray-900 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-semibold px-4 py-3 rounded-lg shadow transition flex items-center justify-center gap-2 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Request
                            </button>
                            <button data-modal-target="delete-modal" data-modal-toggle="delete-modal"
                                class="flex-1 bg-gray-600 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-white font-semibold px-4 py-3 rounded-lg shadow transition flex items-center justify-center gap-2 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Request
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="delete-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                        Confirm Deletion
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-gray-900 dark:hover:text-white" data-modal-hide="delete-modal">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Are you sure you want to delete this item request? This action cannot be undone.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button data-modal-hide="delete-modal"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium">
                            Cancel
                        </button>
                        <button wire:click="destroy({{ $itemRequest->id }})" data-modal-hide="delete-modal"
                            class="bg-gray-900 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg shadow transition text-sm">
                            Delete Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
