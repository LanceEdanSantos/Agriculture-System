<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-6 sm:p-8 border border-gray-100 dark:border-gray-800 transition-all duration-300">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-6 sm:mb-8 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 sm:h-8 w-6 sm:w-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Item Request Details
        </h1>

        {{-- ✅ Session Alerts --}}
        @if (session()->has('success'))
            <div class="mb-6 p-4 flex items-center gap-3 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 flex items-center gap-3 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.366-.756 1.42-.756 1.786 0l6.518 13.47A1 1 0 0115.518 18H4.482a1 1 0 01-.894-1.431l6.518-13.47zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-.25-7.25a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- 📋 Request Details Card --}}
        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 sm:p-6 mb-6 sm:mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                {{-- Farm --}}
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Farm
                    </p>
                    <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">{{ $itemRequest->farm->name }}</p>
                </div>

                {{-- Item --}}
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Item
                    </p>
                    <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">{{ $itemRequest->inventoryItem->name }}</p>
                </div>

                {{-- Quantity --}}
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                        </svg>
                        Quantity
                    </p>
                    <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">{{ $itemRequest->quantity }}</p>
                </div>

                {{-- Status --}}
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Status
                    </p>
                    <span class="inline-flex px-2 py-1 text-sm font-medium rounded-full
                        @if($itemRequest->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                        @elseif($itemRequest->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                        @elseif($itemRequest->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300
                        @endif">
                        {{ ucfirst($itemRequest->status) }}
                    </span>
                </div>

                {{-- Current Stock Status --}}
                @if($itemRequest->inventoryItem)
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Current Stock Availability
                    </p>
                    <input type="text"
                           value="{{ $itemRequest->inventoryItem->current_stock }} {{ $itemRequest->inventoryItem->unit }}"
                           class="w-full bg-green-50 dark:bg-green-900/20 border-2 border-green-300 dark:border-green-700 text-green-900 dark:text-green-100 text-sm rounded-lg p-2.5 font-semibold cursor-not-allowed"
                           disabled
                           readonly>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Available in {{ $itemRequest->inventoryItem->name }}</p>
                </div>
                @endif

                {{-- Requested At --}}
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Requested At
                    </p>
                    <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">{{ $itemRequest->requested_at->format('M j, Y \a\t g:i A') }}</p>
                </div>
            </div>

            {{-- Notes Section --}}
            @if($itemRequest->notes)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Notes
                    </p>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $itemRequest->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- 💬 Request Discussion / Chat --}}
        <div class="mt-6 sm:mt-8">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Request Discussion
            </h2>

            {{-- Message Alerts --}}
            @if (session()->has('message-success'))
                <div class="mb-4 p-4 flex items-center gap-3 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('message-success') }}</span>
                </div>
            @endif

            @if (session()->has('message-error'))
                <div class="mb-4 p-4 flex items-center gap-3 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.366-.756 1.42-.756 1.786 0l6.518 13.47A1 1 0 0115.518 18H4.482a1 1 0 01-.894-1.431l6.518-13.47zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-.25-7.25a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('message-error') }}</span>
                </div>
            @endif

            {{-- Messages Container --}}
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 space-y-4" wire:poll.5s="refreshMessages">
                @if(count($messages) > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach(array_reverse($messages) as $message)
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 @if($message['is_admin_message']) border-l-4 border-l-green-500 @else border-l-4 border-l-blue-500 @endif">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div class="flex-shrink-0">
                                            @if($message['is_admin_message'])
                                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                                    {{ $message['user']['name'] }}
                                                </span>
                                                @if($message['is_admin_message'])
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                                                        Admin
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap break-words">{{ $message['message'] }}</p>
                                        </div>
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No messages yet. Start the conversation!</p>
                    </div>
                @endif

                {{-- Message Input Form --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <form wire:submit.prevent="sendMessage" class="space-y-3">
                        <div>
                            <label for="newMessage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Your Message
                            </label>
                            <textarea
                                wire:model="newMessage"
                                id="newMessage"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white resize-none"
                                placeholder="Type your message here..."
                            ></textarea>
                            @error('newMessage')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg shadow-md transition-all duration-200 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 flex items-center gap-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 📊 Status History --}}
        @if(count($itemRequest->statuses ?? []) > 0)
            <div class="mt-6 sm:mt-8">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Status History
                </h2>
                <div class="space-y-3">
                    @foreach ($itemRequest->statuses as $status)
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        @if($status->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                                        @elseif($status->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                        @elseif($status->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300
                                        @endif">
                                        {{ ucfirst($status->status) }}
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        Changed by {{ $status->changedBy->name }}
                                    </span>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $status->created_at->format('M j, Y \a\t g:i A') }}
                                </span>
                            </div>
                            @if($status->notes)
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 italic">
                                    "{{ $status->notes }}"
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ⚡ Action Buttons --}}
        @if ($itemRequest->status === 'pending')
            <div class="mt-6 sm:mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row gap-3">
                    <button wire:click="edit({{ $itemRequest->id }})"
                        class="flex-1 sm:flex-none bg-green-600 hover:bg-green-700 text-white font-semibold px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-md transition-all duration-200 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Request
                    </button>
                    <button data-modal-target="delete-modal" data-modal-toggle="delete-modal"
                        class="flex-1 sm:flex-none bg-red-600 hover:bg-red-700 text-white font-semibold px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-md transition-all duration-200 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-800 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Request
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- 🗑️ Delete Confirmation Modal --}}
    <div id="delete-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.366-.756 1.42-.756 1.786 0l6.518 13.47A1 1 0 0115.518 18H4.482a1 1 0 01-.894-1.431l6.518-13.47zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-.25-7.25a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5z" clip-rule="evenodd" />
                        </svg>
                        Confirm Deletion
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" data-modal-hide="delete-modal">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                        Are you sure you want to delete this item request? This action cannot be undone.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button data-modal-hide="delete-modal"
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors font-medium">
                            Cancel
                        </button>
                        <button wire:click="destroy({{ $itemRequest->id }})" data-modal-hide="delete-modal"
                            class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition-all duration-200 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-800 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>