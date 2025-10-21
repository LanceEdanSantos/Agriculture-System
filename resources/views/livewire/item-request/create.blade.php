<div class="max-w-3xl mx-auto px-6 py-8">
    <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-8 border border-gray-100 dark:border-gray-800 transition-all duration-300">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Create Item Request
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

        {{-- ⚠️ No Farms Available --}}
        @if (!$hasFarms)
            <div class="p-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl">
                <h2 class="text-lg font-semibold text-yellow-800 dark:text-yellow-300 mb-2">No Farms Available</h2>
                <p class="text-yellow-700 dark:text-yellow-400 leading-relaxed">
                    You currently don’t have access to any farms. Please contact your administrator to request access before creating item requests.
                </p>
            </div>
        @else
            {{-- 🧾 Item Request Form --}}
            <form wire:submit.prevent="store" class="space-y-6">
                {{-- Farm --}}
                <div>
                    <label for="farm_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-100">Farm</label>

                    @if ($userFarmsCount === 1)
                        <div class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-3 text-gray-900 dark:text-gray-200">
                            {{ $farmNames[$farm_id] ?? 'Selected Farm' }}
                            <span class="text-xs text-gray-500 ml-1">(Auto-selected)</span>
                        </div>
                        <input type="hidden" wire:model="farm_id" value="{{ $farm_id }}">
                    @else
                        <select wire:model="farm_id" id="farm_id"
                            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-3">
                            <option value="">Select a farm</option>
                            @foreach ($farms as $farm)
                                <option value="{{ $farm['id'] }}">{{ $farm['name'] }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('farm_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Inventory Item --}}
                <div>
                    <label for="inventory_item_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-100">Inventory Item</label>
                    <select wire:model="inventory_item_id" id="inventory_item_id"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-3">
                        <option value="">Select an item</option>
                        @foreach ($availableItems as $item)
                            <option value="{{ $item['id'] }}">{{ $item['name'] }} ({{ $item['farm_name'] }})</option>
                        @endforeach
                    </select>
                    @error('inventory_item_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Quantity --}}
                <div>
                    <label for="quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-100">Quantity</label>
                    <input type="number" wire:model="quantity" id="quantity" step="0.01"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-3"
                        placeholder="Enter quantity">
                    @error('quantity') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Notes
                    </label>
                    <textarea wire:model="notes" id="notes" rows="4"
                        class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-3 transition-colors"
                        placeholder="Add any details or remarks..."></textarea>
                    @error('notes') <p class="text-red-600 text-sm mt-1 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $message }}
                    </p> @enderror
                </div>

                {{-- File Attachments --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Attachments
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

                {{-- Submit Button --}}
                <div class="pt-2">
                    <button type="submit"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition-all duration-200 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Submit Request
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
