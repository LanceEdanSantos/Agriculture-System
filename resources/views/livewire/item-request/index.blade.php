<div class="min-h-screen bg-white dark:bg-gray-900 py-4 px-3 sm:py-8 sm:px-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-1">Item Requests</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage and track your item requests</p>
                </div>
                <button wire:click="create" 
                    class="inline-flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-semibold px-4 py-3 rounded-lg shadow transition text-sm sm:text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">Create New Request</span>
                    <span class="sm:hidden">Create</span>
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-800 border-l-4 border-gray-900 dark:border-gray-600 rounded-lg text-sm">
                <p class="text-gray-900 dark:text-white font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-800 border-l-4 border-gray-900 dark:border-gray-600 rounded-lg text-sm">
                <p class="text-gray-900 dark:text-white font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Requests List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">ID</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Farm</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Item</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Qty</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Status</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Date</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @php
                            $requests = \App\Models\ItemRequest::where('user_id', Auth::id())->with(['farm', 'inventoryItem'])->latest()->paginate(10);
                        @endphp
                        @forelse ($requests as $request)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3">
                                    <span class="text-xs font-semibold text-gray-900 dark:text-white">#{{ $request->id }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $request->farm->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $request->inventoryItem->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $request->quantity }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-700 dark:text-gray-300">
                                    {{ $request->requested_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <button wire:click="show({{ $request->id }})" 
                                            class="px-2 py-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-900 dark:text-white text-xs font-medium rounded transition">
                                            View
                                        </button>
                                        @if ($request->status === 'pending')
                                            <button wire:click="edit({{ $request->id }})" 
                                                class="px-2 py-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-900 dark:text-white text-xs font-medium rounded transition">
                                                Edit
                                            </button>
                                            <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" 
                                                wire:click="destroy({{ $request->id }})" 
                                                class="px-2 py-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-900 dark:text-white text-xs font-medium rounded transition">
                                                Del
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <p class="text-gray-900 dark:text-white font-semibold mb-2">No requests found</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Create your first item request</p>
                                    <button wire:click="create" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-medium rounded-lg transition text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Create Request
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @php
                    $requests = $requests ?? \App\Models\ItemRequest::where('user_id', Auth::id())->with(['farm', 'inventoryItem'])->latest()->paginate(10);
                @endphp
                @forelse ($requests as $request)
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">#{{ $request->id }}</span>
                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $request->inventoryItem->name }}</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $request->farm->name }}</p>
                            </div>
                            <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                                <div>Qty: {{ $request->quantity }}</div>
                                <div>{{ $request->requested_at->format('M d') }}</div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="show({{ $request->id }})" 
                                class="flex-1 px-3 py-2 bg-gray-900 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white text-xs font-medium rounded transition">
                                View
                            </button>
                            @if ($request->status === 'pending')
                                <button wire:click="edit({{ $request->id }})" 
                                    class="px-3 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-900 dark:text-white text-xs font-medium rounded transition">
                                    Edit
                                </button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" 
                                    wire:click="destroy({{ $request->id }})" 
                                    class="px-3 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-900 dark:text-white text-xs font-medium rounded transition">
                                    Del
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-gray-900 dark:text-white font-semibold mb-2">No requests found</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Create your first item request</p>
                        <button wire:click="create" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-medium rounded-lg transition text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Request
                        </button>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($requests->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
