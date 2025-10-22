<x-layouts.app>
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Dashboard Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Item Requests Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Manage and track all your inventory requests
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <button
                        wire:click="create"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        New Request
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        @php
            $stats = [
                'total' => \App\Models\ItemRequest::whereIn('farm_id', \App\Models\Farm::whereHas('users', fn($q) => $q->where('user_id', Auth::id())->where('is_visible', true))->pluck('id'))->count(),
                'pending' => \App\Models\ItemRequest::where('status', 'pending')->whereIn('farm_id', \App\Models\Farm::whereHas('users', fn($q) => $q->where('user_id', Auth::id())->where('is_visible', true))->pluck('id'))->count(),
                'approved' => \App\Models\ItemRequest::where('status', 'approved')->whereIn('farm_id', \App\Models\Farm::whereHas('users', fn($q) => $q->where('user_id', Auth::id())->where('is_visible', true))->pluck('id'))->count(),
                'rejected' => \App\Models\ItemRequest::where('status', 'rejected')->whereIn('farm_id', \App\Models\Farm::whereHas('users', fn($q) => $q->where('user_id', Auth::id())->where('is_visible', true))->pluck('id'))->count(),
            ];
        @endphp

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Total Requests -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Requests</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $stats['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $stats['pending'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved Requests -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Approved</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $stats['approved'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rejected Requests -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Rejected</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $stats['rejected'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Requests Grid -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Requests</h2>
                    <div class="mt-3 sm:mt-0 sm:flex sm:space-x-3">
                        <div class="relative">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Search requests..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        <select
                            wire:model.live="statusFilter"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg"
                        >
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @php
                    $requests = \App\Models\ItemRequest::whereIn('farm_id', \App\Models\Farm::whereHas('users', fn($q) => $q->where('user_id', Auth::id())->where('is_visible', true))->pluck('id'))
                        ->when($search ?? null, function($query, $search) {
                            return $query->where(function($q) use ($search) {
                                $q->where('notes', 'like', '%' . $search . '%')
                                  ->orWhereHas('inventoryItem', function($itemQuery) use ($search) {
                                      $itemQuery->where('name', 'like', '%' . $search . '%');
                                  })
                                  ->orWhereHas('farm', function($farmQuery) use ($search) {
                                      $farmQuery->where('name', 'like', '%' . $search . '%');
                                  });
                            });
                        })
                        ->when($statusFilter ?? null, function($query, $status) {
                            return $query->where('status', $status);
                        })
                        ->latest()
                        ->paginate(12);
                @endphp

                @if($requests->count() > 0)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($requests as $request)
                            <div class="bg-gray-50 rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                <div class="p-6">
                                    <!-- Header -->
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">#{{ $request->id }}</p>
                                                <p class="text-xs text-gray-500">{{ $request->requested_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($request->status === 'approved') bg-green-100 text-green-800
                                            @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </div>

                                    <!-- Content -->
                                    <div class="space-y-3 mb-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $request->inventoryItem->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $request->farm->name }}</p>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div class="text-sm">
                                                <span class="text-gray-500">Quantity:</span>
                                                <span class="font-medium text-gray-900 ml-1">
                                                    {{ number_format($request->quantity, 2) }} {{ $request->inventoryItem->unit }}
                                                </span>
                                            </div>
                                        </div>

                                        @if($request->notes)
                                            <div class="text-xs text-gray-600 bg-gray-100 rounded px-2 py-1">
                                                {{ Str::limit($request->notes, 60) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex space-x-2">
                                        <button
                                            wire:click="show({{ $request->id }})"
                                            class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </button>

                                        @if ($request->status === 'pending')
                                            <button
                                                wire:click="edit({{ $request->id }})"
                                                class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8 flex justify-center">
                        {{ $requests->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No requests found</h3>
                        <p class="text-gray-500 mb-6">Get started by creating your first item request</p>
                        <button
                            wire:click="create"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create Your First Request
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-layouts.app>