<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-gray-950">
        <flux:header container class="border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm">
            <flux:sidebar.toggle class="lg:hidden text-gray-700 dark:text-gray-200" icon="bars-2" inset="left" />

            <!-- Logo/Brand -->
            <a href="{{ route('item-requests.index') }}" class="flex items-center gap-2 text-gray-900 dark:text-white font-semibold text-lg" wire:navigate>
                <img src="{{ asset('images/PAO.png') }}" alt="Logo" class="h-24 w-auto">
                <span class="max-sm:hidden">{{ config('app.name') }}</span>
            </a>

            <flux:navbar class="-mb-px max-lg:hidden ml-6">
                <flux:navbar.item 
                    :href="route('item-requests.index')" 
                    :current="request()->routeIs('item-requests.index')" 
                    wire:navigate 
                    class="text-gray-700 hover:text-green-600 dark:text-gray-300 dark:hover:text-green-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    {{ __('My Requests') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            {{-- <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('Search')" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('Search')" />
                </flux:tooltip>
                <flux:tooltip :content="__('Repository')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="folder-git-2"
                        href="https://github.com/laravel/livewire-starter-kit"
                        target="_blank"
                        :label="__('Repository')"
                    />
                </flux:tooltip>
                <flux:tooltip :content="__('Documentation')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="book-open-text"
                        href="https://laravel.com/docs/starter-kits#livewire"
                        target="_blank"
                        label="Documentation"
                    />
                </flux:tooltip>
            </flux:navbar> --}}

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="end">
                <flux:profile
                    class="cursor-pointer bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200 border-2 border-transparent hover:border-green-200 dark:hover:border-green-700 transition-colors"
                    :initials="auth()->user()->initials()"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-r border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-800">
                <a href="{{ route('item-requests.index') }}" class="flex items-center gap-2 text-gray-900 dark:text-white font-semibold text-lg" wire:navigate>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Farm Supplies</span>
                </a>
                <flux:sidebar.toggle class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" icon="x-mark" />
            </div>

            <div class="p-2">
                <flux:navlist>
                    <flux:navlist.item 
                        :href="route('item-requests.index')" 
                        :current="request()->routeIs('item-requests.index')" 
                        wire:navigate 
                        icon="inbox-stack"
                        class="text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                        {{ __('My Requests') }}
                    </flux:navlist.item>
                </flux:navlist>
            </div>

            <flux:spacer />

            <!-- Removed external links for farmer-focused UI -->
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
