<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Item Request Details</h1>

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

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Farm</p>
                <p class="text-lg text-gray-900 dark:text-white">{{ $itemRequest->farm->name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Item</p>
                <p class="text-lg text-gray-900 dark:text-white">{{ $itemRequest->inventoryItem->name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Quantity</p>
                <p class="text-lg text-gray-900 dark:text-white">{{ $itemRequest->quantity }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                <p class="text-lg text-gray-900 dark:text-white">{{ ucfirst($itemRequest->status) }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Requested By</p>
                <p class="text-lg text-gray-900 dark:text-white">{{ $itemRequest->user->name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Requested At</p>
                <p class="text-lg text-gray-900 dark:text-white">{{ $itemRequest->requested_at->format('Y-m-d H:i') }}</p>
            </div>
            @if ($itemRequest->feedback)
                <div class="col-span-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Feedback</p>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $itemRequest->feedback->content }}</p>
                </div>
            @endif
            <div class="col-span-2">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes</p>
                <p class="text-lg text-gray-900 dark:text-white">{{ $itemRequest->notes ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="mt-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Status History</h2>
            <ul class="mt-2 space-y-2">
                @foreach ($itemRequest->statuses as $status)
                    <li class="text-gray-500 dark:text-gray-400">
                        {{ ucfirst($status->status) }} - Changed by {{ $status->changedBy->name }} on {{ $status->created_at->format('Y-m-d H:i') }}: {{ $status->notes }}
                    </li>
                @endforeach
            </ul>
        </div>
        @if ($itemRequest->status === 'pending')
            <div class="mt-6 flex space-x-4">
                <button wire:click="edit({{ $itemRequest->id }})" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300">Edit</button>
                <button data-modal-target="delete-modal" data-modal-toggle="delete-modal" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300">Delete</button>
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 border-b dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Deletion</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-900 dark:hover:text-white" data-modal-hide="delete-modal">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path></svg>
                    </button>
                </div>
                <div class="p-4">
                    <p class="text-gray-500 dark:text-gray-400">Are you sure you want to delete this item request?</p>
                    <div class="flex justify-end mt-4 space-x-2">
                        <button wire:click="destroy({{ $itemRequest->id }})" data-modal-hide="delete-modal" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete</button>
                        <button data-modal-hide="delete-modal" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>