<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">


        <title>@if (!empty($title)) {{ $title }} — @endif{{ config('app.name', 'Laravel') }}</title>

        <script
            src="https://browser.sentry-cdn.com/5.26.0/bundle.tracing.min.js"
            integrity="sha384-o3PmxWd0Sgy+qiulNfK/K+YxK4Neya0uoBhAdI1YCdS6yuHZM7vN8v9r0cBDmQ9K"
            crossorigin="anonymous"
        ></script>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-black">
            <nav x-data="{ open: false }" class="bg-black border-b border-gray-700">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16">
                        <div class="flex flex-grow">
                            {{-- <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <a href="/dashboard">
                                    <x-jet-application-mark class="block h-9 w-auto" />
                                </a>
                            </div> --}}

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px {{--sm:ml-10--}} sm:flex">
                                <x-jet-nav-link class="text-gray-50" href="/dashboard" :active="request()->routeIs('dashboard')">
                                    {{ __('Dashboard') }}
                                </x-jet-nav-link>
                                <x-jet-nav-link class="text-gray-50" href="{{ route('activities.index') }}" :active="request()->routeIs('activities.index')">
                                    {{ __('Activities') }}
                                </x-jet-nav-link>
                                <x-jet-nav-link class="text-gray-50" href="{{ route('segments.index') }}" :active="request()->routeIs('segments.index')">
                                    {{ __('Segments') }}
                                </x-jet-nav-link>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            @livewire('activities.upload')
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6 relative">
                            @livewire('notifications.icon')
                        </div>

                        <!-- Teams Dropdown -->
                        @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                            <div class="hidden sm:flex sm:items-center sm:ml-6">
                                <x-jet-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                                            <svg class="h-6 w-6 text-gray-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <!-- Team Switcher -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Toggle Team
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            @if (!Auth::user()->personalTeam()->is($team))
                                                <x-jet-switchable-team :team="$team" />
                                            @endif
                                        @endforeach

                                        <div class="border-t border-gray-100"></div>

                                        <!-- Team Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Teams
                                        </div>

                                        <!-- Team Settings -->
                                        @if (!Auth::user()->currentTeam->is(Auth::user()->personalTeam()))
                                            <x-jet-dropdown-link href="/teams/{{ Auth::user()->currentTeam->id }}">
                                                Team Settings
                                            </x-jet-dropdown-link>
                                        @endif

                                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                            <x-jet-dropdown-link href="/teams/create">
                                                Create New Team
                                            </x-jet-dropdown-link>
                                        @endcan

                                    </x-slot>
                                </x-jet-dropdown>
                            </div>
                        @endif

                        <!-- Settings Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <x-jet-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                                        <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="" />
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <!-- Account Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Manage Account
                                    </div>

                                    <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                        {{ __('Profile') }}
                                    </x-jet-dropdown-link>
                                    <x-jet-dropdown-link href="{{ route('user.metrics') }}">
                                        {{ __('Metrics') }}
                                    </x-jet-dropdown-link>
                                    <x-jet-dropdown-link href="{{ route('user.integrations') }}">
                                        {{ __('Integrations') }}
                                    </x-jet-dropdown-link>
                                    <x-jet-dropdown-link href="{{ route('user.followers') }}">
                                        {{ __('Followers') }}
                                    </x-jet-dropdown-link>

                                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                        <x-jet-dropdown-link href="/user/api-tokens">
                                            API Tokens
                                        </x-jet-dropdown-link>
                                    @endif

                                    <div class="border-t border-gray-100"></div>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-jet-dropdown-link href="{{ route('logout') }}"
                                                            onclick="event.preventDefault();
                                                                     this.closest('form').submit();">
                                            Logout
                                        </x-jet-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-jet-dropdown>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <x-jet-responsive-nav-link href="/dashboard" :active="request()->routeIs('dashboard')">
                            Dashboard
                        </x-jet-responsive-nav-link>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="" />
                            </div>

                            <div class="ml-3">
                                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <!-- Account Management -->
                            <x-jet-responsive-nav-link href="/user/profile" :active="request()->routeIs('profile.show')">
                                Profile
                            </x-jet-responsive-nav-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-jet-responsive-nav-link href="/user/api-tokens" :active="request()->routeIs('api-tokens.index')">
                                    API Tokens
                                </x-jet-responsive-nav-link>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-jet-responsive-nav-link href="{{ route('logout') }}"
                                                onclick="event.preventDefault();
                                                         this.closest('form').submit();">
                                    Logout
                                </x-jet-responsive-nav-link>
                            </form>

                            <!-- Team Management -->
                            @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                                <div class="border-t border-gray-200"></div>

                                <!-- Team Switcher -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Switch Teams
                                </div>

                                @foreach (Auth::user()->allTeams() as $team)
                                    <x-jet-switchable-team :team="$team" component="jet-responsive-nav-link" />
                                @endforeach

                                <div class="border-t border-gray-200"></div>

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Teams
                                </div>

                                <!-- Team Settings -->
                                <x-jet-responsive-nav-link href="/teams/{{ Auth::user()->currentTeam->id }}" :active="request()->routeIs('teams.show')">
                                    Team Settings
                                </x-jet-responsive-nav-link>

                                <x-jet-responsive-nav-link href="/teams/create" :active="request()->routeIs('teams.create')">
                                    Create New Team
                                </x-jet-responsive-nav-link>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="h-24 bg-black border-b border-gray-700">
                <div class="flex max-w-7xl h-24 mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
        <script src="{{ url(mix('js/app.js')) }}"></script>
        @stack('modals')
        @stack('scripts')
    </body>
</html>
