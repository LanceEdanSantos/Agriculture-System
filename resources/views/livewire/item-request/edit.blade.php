<div class="max-w-3xl mx-auto px-6 py-8">
    <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-8 border border-gray-100 dark:border-gray-800 transition-all duration-300">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit Item Request
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

        {{-- 📝 Edit Form --}}
        <form wire:submit.prevent="update" class="space-y-6">
            {{-- Farm Selection --}}
            <div>
                <label for="farm_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Farm
                </label>

                @if ($userFarmsCount === 1)
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-3 text-gray-900 dark:text-gray-200">
                        {{ $farmNames[$farm_id] ?? 'Selected Farm' }}
                        <span class="text-xs text-gray-500 ml-1">(Auto-selected)</span>
                    </div>
                    <input type="hidden" wire:model="farm_id" value="{{ $farm_id }}">
                @else
                    <select wire:model="farm_id" id="farm_id"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-3 transition-colors">
                        <option value="">Select a farm</option>
                        @foreach ($farms as $farm)
                            <option value="{{ $farm['id'] }}" {{ $farm['id'] == $farm_id ? 'selected' : '' }}>{{ $farm['name'] }}</option>
                        @endforeach
                    </select>
                @endif
                @error('farm_id') <p class="text-red-600 text-sm mt-1 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $message }}
                </p> @enderror
            </div>

            {{-- Inventory Item Selection --}}
            <div>
                <label for="inventory_item_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Inventory Item
                </label>
                <select wire:model="inventory_item_id" id="inventory_item_id"
                    class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-3 transition-colors">
                    <option value="">Select an item</option>
                    @foreach ($availableItems as $item)
                        <option value="{{ $item['id'] }}" {{ $item['id'] == $inventory_item_id ? 'selected' : '' }}>
                            {{ $item['name'] }} ({{ $item['farm_name'] }})
                        </option>
                    @endforeach
                </select>
                @error('inventory_item_id') <p class="text-red-600 text-sm mt-1 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $message }}
                </p> @enderror
            </div>

            {{-- Quantity Input --}}
            <div>
                <label for="quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                    </svg>
                    Quantity
                </label>
                <input type="number" wire:model="quantity" id="quantity" step="0.01"
                    class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-3 transition-colors"
                    placeholder="Enter quantity">
                @error('quantity') <p class="text-red-600 text-sm mt-1 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $message }}
                </p> @enderror
            </div>

            {{-- Notes Textarea --}}
            <div>
                <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Notes
                </label>
                <textarea wire:model="notes" id="notes" rows="4"
                    class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-3 transition-colors"
                    placeholder="Add any details or remarks...">{{ $notes }}</textarea>
                @error('notes') <p class="text-red-600 text-sm mt-1 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $message }}
                </p> @enderror
            </div>

            {{-- 📎 Existing Attachments --}}
            @if($existingAttachments->count() > 0)
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        Current Attachments
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($existingAttachments as $attachment)
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    @if($attachment->isImage())
                                        <div class="flex-shrink-0">
                                            <img src="{{ $attachment->url }}" alt="{{ $attachment->original_name }}"
                                                 class="w-10 h-10 object-cover rounded border border-gray-200 dark:border-gray-600">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $attachment->original_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attachment->formatted_file_size }}</p>
                                    </div>
                                    <button wire:click="removeAttachment({{ $attachment->id }})" type="button"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 p-1"
                                        title="Remove attachment">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 📎 Add New Attachments --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    {{ $existingAttachments->count() > 0 ? 'Add More Attachments' : 'Attachments' }}
                </label>
                <div class="space-y-3">
                    <input type="file" wire:model="attachments" multiple
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-3 transition-colors"
                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    @error('attachments.*') <p class="text-red-600 text-sm mt-1 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $message }}
                    </p> @enderror

                    {{-- Show uploading files --}}
                    @if ($attachments)
                        <div class="space-y-2">
                            @foreach ($attachments as $index => $file)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $file->getClientOriginalName() }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($file->getSize() / 1024, 1) }} KB</p>
                                    </div>
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Supported formats: JPG, PNG, PDF, DOC, DOCX (Max: 10MB each)
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit"
                        class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition-all duration-200 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Request
                    </button>
                    <a href="{{ route('item-requests.show', $itemRequest->id) }}"
                        class="flex-1 sm:flex-none bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-semibold px-6 py-3 rounded-lg shadow-md transition-all duration-200 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>