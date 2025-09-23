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
                <a href="{{ route('dashboard') }}" class="flex items-center px-2 py-2 text-gray-300 rounded-md hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('firewall.index') }}"
                    class="flex items-center justify-between px-2 py-2 text-gray-300 rounded-md hover:bg-gray-700">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944c-1.554 0-3.036.315-4.418.914M4.796 5.864a12.022 12.022 0 00-1.042 3.142m-.483 4.22c.162 1.41.678 2.768 1.493 4.027m8.498.471c1.258-.816 2.37-1.93 3.185-3.188m.536-4.577a11.956 11.956 0 00-1.042-3.142">
                            </path>
                        </svg>
                        <span class="text-sm font-medium">Firewall</span>
                    </div>
                    <span
                        class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-white bg-green-600 rounded-full">
                        5 regras
                    </span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center px-2 py-2 text-gray-300 rounded-md hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium">Interfaces</span>
                </a>
            </li>
            <li>
                <a href="#"
                    class="flex items-center justify-between px-2 py-2 text-gray-300 rounded-md hover:bg-gray-700">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 20c-4.97 0-9-4.03-9-9s4.03-9 9-9 9 4.03 9 9-4.03 9-9 9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14v4M9 11h6M12 7v4"></path>
                        </svg>
                        <span class="text-sm font-medium">VPN</span>
                    </div>
                    <span
                        class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-white bg-green-600 rounded-full">
                        3 online
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('users.index') }}"
                    class="flex items-center justify-between px-2 py-2 text-white bg-green-700 rounded-md">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-medium">Usuários</span>
                    </div>
                    <span
                        class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-white bg-green-600 rounded-full">
                        5 ativos
                    </span>
                </a>
            </li>
            <li>
                <a href="#"
                    class="flex items-center justify-between px-2 py-2 text-gray-300 rounded-md hover:bg-gray-700">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.593 5.341 6 7.685 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m5 0a2 2 0 11-4 0m0 0H9m5 0v3a2 2 0 01-2 2h0a2 2 0 01-2-2v-3">
                            </path>
                        </svg>
                        <span class="text-sm font-medium">Alertas</span>
                    </div>
                    <span
                        class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-white bg-green-600 rounded-full">
                        2 novos
                    </span>
                </a>
            </li>
            <li>
                <a href="#"
                    class="flex items-center justify-between px-2 py-2 text-gray-300 rounded-md hover:bg-gray-700">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3-7 3V5z"></path>
                        </svg>
                        <span class="text-sm font-medium">Perfis</span>
                    </div>
                    <span
                        class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-white bg-green-600 rounded-full">
                        4 salvos
                    </span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center px-2 py-2 text-gray-300 rounded-md hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19V6l-2 2m0-3l4-4 4 4m-2 2v13"></path>
                    </svg>
                    <span class="text-sm font-medium">Relatórios</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center px-2 py-2 text-gray-300 rounded-md hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium">Histórico</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center px-2 py-2 text-gray-300 rounded-md hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    <span class="text-sm font-medium">Logs</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
