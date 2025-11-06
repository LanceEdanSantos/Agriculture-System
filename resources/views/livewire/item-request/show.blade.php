<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Item Request Details</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">View and manage request information</p>
                </div>
                <div>
                    <button wire:click="$set('mode', 'index')" 
                        class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </button>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 rounded-lg text-sm {{ session('message.type') === 'success' ? 'bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800' }}">
                <div class="flex items-center">
                    <svg class="h-5 w-5 {{ session('message.type') === 'success' ? 'text-green-500' : 'text-red-500' }} mr-2" fill="currentColor" viewBox="0 0 20 20">
                        @if(session('message.type') === 'success')
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        @else
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        @endif
                    </svg>
                    <p class="{{ session('message.type') === 'success' ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200' }}">
                        {{ session('message.text') }}
                    </p>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

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

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                    <!-- Left Column - Request Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Request Details Card -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request Information</h2>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Farm -->
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Farm</p>
                                        <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $itemRequest->farm->name }}</p>
                                    </div>

                                    <!-- Item -->
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Item</p>
                                        <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $itemRequest->inventoryItem->name }}</p>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Quantity</p>
                                        <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $itemRequest->quantity }} {{ $itemRequest->inventoryItem->unit ?? '' }}</p>
                                    </div>

                                    <!-- Status -->
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
                                                'approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200',
                                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200',
                                                'fulfilled' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
                                                'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                                            ];
                                            $statusColor = $statusColors[$itemRequest->status] ?? $statusColors['default'];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                            {{ ucfirst($itemRequest->status) }}
                                        </span>
                                    </div>

                                    <!-- Current Stock -->
                                    @if($itemRequest->inventoryItem)
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Stock</p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $itemRequest->inventoryItem->current_stock }} {{ $itemRequest->inventoryItem->unit }}
                                            </span>
                                            @php
                                                $stockPercentage = $itemRequest->inventoryItem->current_stock > 0 
                                                    ? min(100, ($itemRequest->inventoryItem->current_stock / ($itemRequest->inventoryItem->current_stock + $itemRequest->quantity)) * 100) 
                                                    : 0;
                                                $stockColor = $stockPercentage > 50 ? 'bg-green-500' : ($stockPercentage > 20 ? 'bg-yellow-500' : 'bg-red-500');
                                            @endphp
                                            <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full {{ $stockColor }} rounded-full" style="width: {{ $stockPercentage }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Requested At -->
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Requested At</p>
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            {{ $itemRequest->requested_at->format('M j, Y \a\t g:i A') }}
                                            <span class="text-gray-500 dark:text-gray-400 text-xs">({{ $itemRequest->requested_at->diffForHumans() }})</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Notes Section -->
                                @if($itemRequest->notes)
                                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</p>
                                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $itemRequest->notes }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Status History -->
                        @if(count($itemRequest->statuses ?? []) > 0)
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Status History</h2>
                                </div>
                                <div class="p-6">
                                    <div class="flow-root">
                                        <ul class="-mb-8">
                                            @foreach($itemRequest->statuses->sortBy('created_at') as $status)
                                                @php
                                                    $statusIcons = [
                                                        'pending' => 'clock',
                                                        'approved' => 'check-circle',
                                                        'rejected' => 'x-circle',
                                                        'fulfilled' => 'check-circle',
                                                        'default' => 'info'
                                                    ];
                                                    $icon = $statusIcons[$status->status] ?? $statusIcons['default'];
                                                    $statusColors = [
                                                        'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-200',
                                                        'approved' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200',
                                                        'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200',
                                                        'fulfilled' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-200',
                                                        'default' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200'
                                                    ];
                                                    $statusColor = $statusColors[$status->status] ?? $statusColors['default'];
                                                @endphp
                                                <li>
                                                    <div class="relative pb-8">
                                                        @if(!$loop->last)
                                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>
                                                        @endif
                                                        <div class="relative flex space-x-3">
                                                            <div>
                                                                <span class="h-8 w-8 rounded-full {{ $statusColor }} flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                                <div>
                                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                        Status changed to 
                                                                        <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($status->status) }}</span>
                                                                    </p>
                                                                    @if($status->notes)
                                                                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-2 rounded">
                                                                            {{ $status->notes }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                                    <time datetime="{{ $status->created_at->toIso8601String() }}">
                                                                        {{ $status->created_at->diffForHumans() }}
                                                                    </time>
                                                                    <div class="text-xs">
                                                                        by {{ $status->changedBy->name }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Right Column - Messages -->
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden h-full flex flex-col">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Discussion</h2>
                            </div>
                            
                            <!-- Messages Container -->
                            <div class="flex-1 overflow-y-auto p-4 space-y-4" wire:poll.5s="refreshMessages">
                                @if(count($messages) > 0)
                                    @foreach($messages as $message)
                                        <div class="flex {{ $message['is_admin_message'] ? 'justify-end' : 'justify-start' }} mb-4">
                                            <div class="flex max-w-xs lg:max-w-md">
                                                @if(!$message['is_admin_message'])
                                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 text-sm font-medium mr-2">
                                                        {{ substr($message['user']['fname'], 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="{{ $message['is_admin_message'] ? 'bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }} p-3 rounded-lg">
                                                        <p class="text-sm">{{ $message['message'] }}</p>
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ $message['is_admin_message'] ? 'text-right' : 'text-left' }}">
                                                        {{ \Carbon\Carbon::parse($message['created_at'])->diffForHumans() }}
                                                        @if($message['is_admin_message'])
                                                            <span class="ml-1">• Admin</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No messages</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start the conversation!</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Message Input -->
                            <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                                <form wire:submit.prevent="sendMessage" class="space-y-3">
                                    <div>
                                        <label for="newMessage" class="sr-only">Message</label>
                                        <div class="mt-1">
                                            <textarea
                                                wire:model="newMessage"
                                                id="newMessage"
                                                rows="3"
                                                class="shadow-sm block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                                placeholder="Type your message..."
                                            ></textarea>
                                        </div>
                                        @error('newMessage')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex justify-end">
                                        <button
                                            type="submit"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                            </svg>
                                            Send
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Action Buttons -->
                @if ($itemRequest->status === 'pending')
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 px-6 pb-6">
                        <div class="flex flex-col sm:flex-row justify-end gap-3">
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
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md">
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
