<div class="max-w-7xl mx-auto px-6 py-8">
    {{-- 📋 Header Section --}}
    <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-8 border border-gray-100 dark:border-gray-800 mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Item Requests</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage and track all your item requests</p>
                </div>
            </div>
            <button wire:click="create"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition-all duration-200 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create New Request
            </button>
        </div>
    </div>

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

    {{-- 📊 Requests Grid --}}
    <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl border border-gray-100 dark:border-gray-800">
        {{-- Table Header --}}
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-12 gap-4 text-sm font-medium text-gray-500 dark:text-gray-400">
                <div class="col-span-1">ID</div>
                <div class="col-span-2">Farm</div>
                <div class="col-span-2">Item</div>
                <div class="col-span-2">Quantity</div>
                <div class="col-span-2">Status</div>
                <div class="col-span-2">Requested</div>
                <div class="col-span-1">Actions</div>
            </div>
        </div>

        {{-- Table Body --}}
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse (\App\Models\ItemRequest::whereIn('farm_id', \App\Models\Farm::whereHas('users', fn($q) => $q->where('user_id', Auth::id())->where('is_visible', true))->pluck('id'))->latest()->paginate(10) as $request)
                <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <div class="grid grid-cols-12 gap-4 items-center">
                        {{-- ID --}}
                        <div class="col-span-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                #{{ $request->id }}
                            </span>
                        </div>

                        {{-- Farm --}}
                        <div class="col-span-2">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-white">{{ Str::limit($request->farm->name, 20) }}</span>
                            </div>
                        </div>

                        {{-- Item --}}
                        <div class="col-span-2">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-white">{{ Str::limit($request->inventoryItem->name, 20) }}</span>
                            </div>
                        </div>

                        {{-- Quantity --}}
                        <div class="col-span-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                                {{ $request->quantity }}
                            </span>
                        </div>

                        {{-- Status --}}
                        <div class="col-span-2">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($request->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                                @elseif($request->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                @elseif($request->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300
                                @endif">
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>

                        {{-- Requested At --}}
                        <div class="col-span-2">
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $request->requested_at->format('M j, Y') }}
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="col-span-1">
                            <div class="flex items-center gap-2">
                                <button wire:click="show({{ $request->id }})"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm transition-colors">
                                    View
                                </button>
                                @if ($request->status === 'pending')
                                    <span class="text-gray-300 dark:text-gray-600">•</span>
                                    <button wire:click="edit({{ $request->id }})"
                                        class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 font-medium text-sm transition-colors">
                                        Edit
                                    </button>
                                    <span class="text-gray-300 dark:text-gray-600">•</span>
                                    <button data-modal-target="delete-modal-{{ $request->id }}" data-modal-toggle="delete-modal-{{ $request->id }}"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm transition-colors">
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 🗑️ Delete Confirmation Modal --}}
                <div id="delete-modal-{{ $request->id }}" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 w-full max-w-md max-h-full">
                        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.366-.756 1.42-.756 1.786 0l6.518 13.47A1 1 0 0115.518 18H4.482a1 1 0 01-.894-1.431l6.518-13.47zM11 14a1 1 0 11-2 0 1 1 0 012 0zm-.25-7.25a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5z" clip-rule="evenodd" />
                                    </svg>
                                    Confirm Deletion
                                </h3>
                                <button type="button" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" data-modal-hide="delete-modal-{{ $request->id }}">
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
                                    <button data-modal-hide="delete-modal-{{ $request->id }}"
                                        class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors font-medium">
                                        Cancel
                                    </button>
                                    <button wire:click="destroy({{ $request->id }})" data-modal-hide="delete-modal-{{ $request->id }}"
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
            @empty
                {{-- 📭 Empty State --}}
                <div class="p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No item requests found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Get started by creating your first item request.</p>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition-all duration-200 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Your First Request
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 📄 Pagination --}}
    @if (\App\Models\ItemRequest::whereIn('farm_id', \App\Models\Farm::whereHas('users', fn($q) => $q->where('user_id', Auth::id())->where('is_visible', true))->pluck('id'))->latest()->paginate(10)->hasPages())
        <div class="mt-8 bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-6 border border-gray-100 dark:border-gray-800">
            {{ \App\Models\ItemRequest::whereIn('farm_id', \App\Models\Farm::whereHas('users', fn($q) => $q->where('user_id', Auth::id())->where('is_visible', true))->pluck('id'))->latest()->paginate(10)->links() }}
        </div>
    @endif
</div>