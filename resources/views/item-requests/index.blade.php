<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Item Requests</h1>
        <button wire:click="create"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
            Create New Request
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Farm</th>
                    <th scope="col" class="px-6 py-3">Item</th>
                    <th scope="col" class="px-6 py-3">Quantity</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Requested At</th>
                    <th scope="col" class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach (\App\Models\ItemRequest::whereIn('farm_id', \App\Models\Farm::whereHas('users', fn($q) => $q->where('user_id', Auth::id())->where('is_visible', true))->pluck('id'))->latest()->paginate(10) as $request)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">{{ $request->id }}</td>
                        <td class="px-6 py-4">{{ $request->farm->name }}</td>
                        <td class="px-6 py-4">{{ $request->inventoryItem->name }}</td>
                        <td class="px-6 py-4">{{ $request->quantity }}</td>
                        <td class="px-6 py-4">{{ ucfirst($request->status) }}</td>
                        <td class="px-6 py-4">{{ $request->requested_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 flex space-x-2">
                            <button wire:click="show({{ $request->id }})"
                                class="text-blue-600 hover:underline">View</button>
                            @if ($request->status === 'pending')
                                <button wire:click="edit({{ $request->id }})"
                                    class="text-green-600 hover:underline">Edit</button>
                                <button data-modal-target="delete-modal-{{ $request->id }}"
                                    data-modal-toggle="delete-modal-{{ $request->id }}"
                                    class="text-red-600 hover:underline">Delete</button>
                            @endif
                        </td>
                    </tr>
                    <!-- Delete Confirmation Modal -->
                    <div id="delete-modal-{{ $request->id }}" tabindex="-1"
                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-md max-h-full">
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <div class="flex items-center justify-between p-4 border-b dark:border-gray-600">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Deletion
                                    </h3>
                                    <button type="button"
                                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white"
                                        data-modal-hide="delete-modal-{{ $request->id }}">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-500 dark:text-gray-400">Are you sure you want to delete this
                                        item request?</p>
                                    <div class="flex justify-end mt-4 space-x-2">
                                        <button wire:click="destroy({{ $request->id }})"
                                            data-modal-hide="delete-modal-{{ $request->id }}"
                                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete</button>
                                        <button data-modal-hide="delete-modal-{{ $request->id }}"
                                            class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ \App\Models\ItemRequest::whereIn('farm_id', \App\Models\Farm::whereHas('users', fn($q) => $q->where('user_id', Auth::id())->where('is_visible', true))->pluck('id'))->latest()->paginate(10)->links() }}
    </div>
</div>
