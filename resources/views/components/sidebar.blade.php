<aside class="flex flex-col w-64 bg-gray-800 border-r border-gray-700">
    <div class="flex items-center p-4">
        <div class="p-2 mr-2 bg-green-700 rounded-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944c-1.554 0-3.036.315-4.418.914M4.796 5.864a12.022 12.022 0 00-1.042 3.142m-.483 4.22c.162 1.41.678 2.768 1.493 4.027m8.498.471c1.258-.816 2.37-1.93 3.185-3.188m.536-4.577a11.956 11.956 0 00-1.042-3.142M12 21.056c1.554 0 3.036-.315 4.418-.914">
                </path>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-100">OPNsense</h2>
            <p class="text-sm text-gray-400">Network Manager</p>
        </div>
    </div>
    <nav class="flex-1 px-4 py-6">
        <ul class="space-y-1">
            <li>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-2 py-2 rounded-md {{ request()->routeIs('dashboard') ? 'text-white bg-green-700' : 'text-gray-300 hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 mr-2 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('firewall.index') }}"
                    class="flex items-center justify-between px-2 py-2 rounded-md {{ request()->routeIs('firewall.*') ? 'text-white bg-green-700' : 'text-gray-300 hover:bg-gray-700' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 {{ request()->routeIs('firewall.*') ? 'text-white' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944c-1.554 0-3.036.315-4.418.914M4.796 5.864a12.022 12.022 0 00-1.042 3.142m-.483 4.22c.162 1.41.678 2.768 1.493 4.027m8.498.471c1.258-.816 2.37-1.93 3.185-3.188m.536-4.577a11.956 11.956 0 00-1.042-3.142">
                            </path>
                        </svg>
                        <span class="text-sm font-medium">Firewall</span>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('users.index') }}"
                    class="flex items-center justify-between px-2 py-2 rounded-md {{ request()->routeIs('users.*') ? 'text-white bg-green-700' : 'text-gray-300 hover:bg-gray-700' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 {{ request()->routeIs('users.*') ? 'text-white' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-medium">Usuários</span>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('groups.index') }}"
                    class="flex items-center justify-between px-2 py-2 rounded-md {{ request()->routeIs('groups.*') ? 'text-white bg-green-700' : 'text-gray-300 hover:bg-gray-700' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 {{ request()->routeIs('groups.*') ? 'text-white' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">Grupos</span>
                    </div>
                </a>
            </li>
        </ul>
    </nav>
</aside>
