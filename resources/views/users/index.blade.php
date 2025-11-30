@extends('layouts.header')

@section('main')
    <div class="p-8 bg-gray-100 min-h-screen">
        {{-- Cabeçalho da Página --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 text-blue-600">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h1 class="text-3xl font-bold text-gray-900">Gerenciamento de Usuários</h1>
                    <p class="text-gray-600">Controle de acesso e permissões do sistema</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('users.import') }}">
                    <button class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Importar Usuários
                    </button>
                </a>
                <a href="{{ route('users.create') }}">
                    <button class="flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Adicionar Usuário
                    </button>
                </a>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="stats-container">
            <div class="bg-white p-5 rounded-lg shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Total de Usuários</h3>
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-semibold text-gray-900 mb-1" id="total-users">0</p>
                    <p class="text-sm text-gray-500">Usuários criados</p>
                </div>
            </div>

            <!-- Outras estatísticas -->
            <div class="bg-white p-5 rounded-lg shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Administradores</h3>
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-semibold text-gray-900 mb-1" id="admin-users">0</p>
                    <p class="text-sm text-gray-500">Usuários com privilégios totais</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-lg shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Usuários Ativos</h3>
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-semibold text-gray-900 mb-1" id="active-users">0</p>
                    <p class="text-sm text-gray-500">Usuários ativos no sistema</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-lg shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Usuários Inativos</h3>
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-semibold text-gray-900 mb-1" id="inactive-users">0</p>
                    <p class="text-sm text-gray-500">Usuários desativados</p>
                </div>
            </div>
        </div>

        {{-- Tabela de Usuários --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 flex items-center justify-between border-b">
                <div class="flex items-center space-x-4">
                    <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900">Lista de Usuários</h2>
                </div>

                {{-- Filtros --}}
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <select id="filter-access-level" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Todos os Níveis</option>
                            <option value="admin">Administradores</option>
                            <option value="operator">Operadores</option>
                            <option value="viewer">Visualizadores</option>
                        </select>
                    </div>

                    <div class="relative">
                        <select id="filter-group" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Todos os Grupos</option>
                        </select>
                    </div>

                    <div class="relative">
                        <select id="filter-status" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Todos os Status</option>
                            <option value="active">Ativos</option>
                            <option value="inactive">Inativos</option>
                        </select>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" placeholder="Buscar usuários..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md sm:text-sm" id="search-input">
                    </div>

                    <!-- Ações em massa -->
                    <div class="flex items-center gap-2">
                        <select id="users-bulk-action" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Ação em massa</option>
                            <option value="delete">Excluir selecionados</option>
                        </select>
                        <button id="users-bulk-apply" class="px-3 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">Aplicar</button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="users-select-all">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nível de Acesso</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último Login</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Controles</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="users-table-body">
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center">
                                <div class="flex justify-center items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Carregando usuários...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="px-6 py-4 border-t flex flex-col md:flex-row md:items-center md:justify-between gap-3" id="users-pagination" style="display:none">
                <div class="flex items-center gap-3">
                    <div class="text-sm text-gray-600" id="users-pagination-info"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Por página:</span>
                        <select id="users-page-size" class="border rounded px-2 py-1 text-sm">
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2 md:justify-end">
                    <button id="users-prev" class="px-3 py-1.5 rounded border text-sm hover:bg-gray-50">Anterior</button>
                    <div id="users-page-numbers" class="flex items-center gap-1"></div>
                    <span id="users-page-indicator" class="text-sm text-gray-700"></span>
                    <button id="users-next" class="px-3 py-1.5 rounded border text-sm hover:bg-gray-50">Próxima</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let allUsers = []; // Armazena todos os usuários para filtragem
        let allGroups = []; // Armazena todos os grupos
        let currentPage = 1;
        let pageSize = 10;
        let currentUsersList = [];

        document.addEventListener('DOMContentLoaded', function() {
            // Carrega os grupos para o filtro
            fetchGroups();

            // Função para buscar grupos
            async function fetchGroups() {
                try {
                    const response = await fetch('api/groups');
                    const data = await response.json();

                    if (data.status === 'success') {
                        allGroups = data.data;
                        populateGroupFilter();
                    }
                } catch (error) {
                    console.error('Erro ao carregar grupos:', error);
                }
            }

            // Popula o filtro de grupos
            function populateGroupFilter() {
                const groupFilter = document.getElementById('filter-group');
                groupFilter.innerHTML = '<option value="">Todos os Grupos</option>';

                allGroups.forEach(group => {
                    const option = document.createElement('option');
                    option.value = group.name.toLowerCase();
                    option.textContent = group.name;
                    groupFilter.appendChild(option);
                });
            }

            // Função para buscar usuários da API
            async function fetchUsers() {
                try {
                    const response = await fetch('api/users');
                    const data = await response.json();

                    if (data.status === 'success') {
                        allUsers = data.data; // Armazena globalmente
                        const totalPages = Math.max(1, Math.ceil(allUsers.length / pageSize));
                        currentPage = Math.min(currentPage, totalPages);
                        renderUsersPaginated(allUsers);
                        updateStats(allUsers);
                    } else {
                        throw new Error(data.message || 'Erro ao carregar usuários');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    document.getElementById('users-table-body').innerHTML = `
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-red-500">
                                Erro ao carregar usuários: ${error.message}
                            </td>
                        </tr>
                    `;
                }
            }

            // Função para renderizar usuários na tabela
            function renderUsers(users) {
                const tbody = document.getElementById('users-table-body');
                tbody.innerHTML = '';

                if (users.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center">
                                Nenhum usuário encontrado
                            </td>
                        </tr>
                    `;
                    return;
                }

                users.forEach(user => {
                    const row = document.createElement('tr');

                    // Nível de acesso
                    let badgeClass = '';
                    let badgeText = '';

                    if (user.is_admin === "1" || user.is_admin === 1) {
                        badgeClass = 'bg-red-100 text-red-800';
                        badgeText = 'Admin';
                    } else if (user.group_memberships && user.group_memberships.includes('admins')) {
                        badgeClass = 'bg-orange-100 text-orange-800';
                        badgeText = 'Operador';
                    } else {
                        badgeClass = 'bg-blue-100 text-blue-800';
                        badgeText = 'Visualizador';
                    }

                    // Status
                    const statusText = user.disabled === "0" || user.disabled === 0 ? 'Ativo' : 'Inativo';
                    const statusClass = user.disabled === "0" || user.disabled === 0 ?
                        'bg-green-100 text-green-800' :
                        'bg-red-100 text-red-800';

                    // Iniciais para o avatar - com verificação de segurança
                    const userName = user.name || 'U';
                    const initials = userName.split(' ')
                        .map(n => n[0] || '')
                        .join('')
                        .toUpperCase()
                        .substring(0, 2);

                    let editUrl = "{{ route('users.edit', ['uuid' => '__UUID__']) }}".replace('__UUID__',
                        user.uuid);

                    row.innerHTML = `
                        <td class="px-6 py-4">
                            <input type="checkbox" class="users-select" value="${user.uuid}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-blue-100 text-blue-800 font-medium">${initials}</div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${userName}</div>
                                    <div class="text-sm text-gray-500">${user.username || 'N/A'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${user.email || 'N/A'}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${badgeClass}">
                                ${badgeText}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">N/A</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
    <div class="flex items-center justify-end space-x-4">
        <a href="${editUrl}" class="text-gray-400 hover:text-indigo-600" title="Editar">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2
                         0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2
                         2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>
        <button class="text-gray-400 hover:text-red-600" title="Excluir"
                onclick="deleteUser('${user.uuid}')">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138
                         21H7.862a2 2 0 01-1.995-1.858L5
                         7m5 4v6m4-6v6m1-10V4a1 1
                         0 00-1-1h-4a1 1 0 00-1
                         1v3M4 7h16"/>
            </svg>
        </button>
    </div>
</td>

                    `;

                    tbody.appendChild(row);
                });

                // Ligação do checkbox geral
                const selectAll = document.getElementById('users-select-all');
                if (selectAll) {
                    selectAll.onchange = (e) => {
                        document.querySelectorAll('.users-select').forEach(cb => cb.checked = e.target.checked);
                    };
                }
            }

            function renderUsersPaginated(list) {
                const total = list.length;
                const start = (currentPage - 1) * pageSize;
                const pageItems = list.slice(start, start + pageSize);
                renderUsers(pageItems);
                const pagWrap = document.getElementById('users-pagination');
                const info = document.getElementById('users-pagination-info');
                const indicator = document.getElementById('users-page-indicator');
                const prevBtn = document.getElementById('users-prev');
                const nextBtn = document.getElementById('users-next');
                const sizeSel = document.getElementById('users-page-size');
                const numbers = document.getElementById('users-page-numbers');
                pagWrap.style.display = '';
                const totalPages = Math.max(1, Math.ceil(total / pageSize));
                info.textContent = `${total === 0 ? 0 : start + 1}-${Math.min(start + pageSize, total)} de ${total}`;
                indicator.textContent = `Página ${currentPage} de ${totalPages}`;
                prevBtn.disabled = currentPage === 1;
                nextBtn.disabled = currentPage >= totalPages;
                prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; renderUsersPaginated(list); }};
                nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; renderUsersPaginated(list); }};

                // Guardar lista atual para reuso
                currentUsersList = list;

                // Tamanho da página
                if (sizeSel && parseInt(sizeSel.value, 10) !== pageSize) {
                    sizeSel.value = String(pageSize);
                }
                sizeSel.onchange = () => {
                    pageSize = parseInt(sizeSel.value, 10) || 10;
                    currentPage = 1;
                    renderUsersPaginated(currentUsersList);
                };

                // Números de página
                numbers.innerHTML = '';
                const makeBtn = (p, label = null, disabled = false) => {
                    const b = document.createElement('button');
                    b.className = `px-2.5 py-1 rounded border text-sm ${p === currentPage ? 'bg-gray-100 font-semibold' : 'hover:bg-gray-50'}`;
                    b.textContent = label || String(p);
                    b.disabled = disabled || p === currentPage;
                    b.onclick = () => { currentPage = p; renderUsersPaginated(list); };
                    return b;
                };
                const addEllipsis = () => {
                    const s = document.createElement('span');
                    s.className = 'px-1 text-gray-500';
                    s.textContent = '…';
                    numbers.appendChild(s);
                };
                const totalToShow = 5; // janela central
                const totalPagesInt = totalPages;
                if (totalPagesInt <= totalToShow + 2) {
                    for (let p = 1; p <= totalPagesInt; p++) numbers.appendChild(makeBtn(p));
                } else {
                    numbers.appendChild(makeBtn(1));
                    let startP = Math.max(2, currentPage - 2);
                    let endP = Math.min(totalPagesInt - 1, currentPage + 2);
                    if (startP > 2) addEllipsis();
                    for (let p = startP; p <= endP; p++) numbers.appendChild(makeBtn(p));
                    if (endP < totalPagesInt - 1) addEllipsis();
                    numbers.appendChild(makeBtn(totalPagesInt));
                }
            }

            // Função para atualizar estatísticas
            function updateStats(users) {
                const totalUsers = users.length;
                const adminUsers = users.filter(user => user.is_admin === "1" || user.is_admin === 1).length;
                const activeUsers = users.filter(user => user.disabled === "0" || user.disabled === 0).length;
                const inactiveUsers = totalUsers - activeUsers;

                // Atualiza valores no DOM
                document.getElementById('total-users').textContent = totalUsers;
                document.getElementById('admin-users').textContent = adminUsers;
                document.getElementById('active-users').textContent = activeUsers;
                document.getElementById('inactive-users').textContent = inactiveUsers;
            }

            // Função para aplicar filtros
            function applyFilters() {
                const searchTerm = document.getElementById('search-input').value.toLowerCase();
                const filterAccessLevel = document.getElementById('filter-access-level').value;
                const filterGroup = document.getElementById('filter-group').value;
                const filterStatus = document.getElementById('filter-status').value;

                const filteredUsers = allUsers.filter(user => {
                    // Filtro de busca por texto
                    const userName = (user.name || '').toLowerCase();
                    const userEmail = (user.email || '').toLowerCase();
                    const matchesSearch = !searchTerm || userName.includes(searchTerm) || userEmail.includes(searchTerm);

                    // Filtro de nível de acesso
                    let matchesAccessLevel = true;
                    if (filterAccessLevel) {
                        if (filterAccessLevel === 'admin') {
                            matchesAccessLevel = user.is_admin === "1" || user.is_admin === 1;
                        } else if (filterAccessLevel === 'operator') {
                            const isNotAdmin = user.is_admin !== "1" && user.is_admin !== 1;
                            const hasAdminGroup = user.group_memberships && user.group_memberships.toLowerCase().includes('admins');
                            matchesAccessLevel = isNotAdmin && hasAdminGroup;
                        } else if (filterAccessLevel === 'viewer') {
                            const isNotAdmin = user.is_admin !== "1" && user.is_admin !== 1;
                            const noAdminGroup = !user.group_memberships || !user.group_memberships.toLowerCase().includes('admins');
                            matchesAccessLevel = isNotAdmin && noAdminGroup;
                        }
                    }

                    // Filtro de grupo
                    let matchesGroup = true;
                    if (filterGroup) {
                        const userGroups = (user.group_memberships || '').toLowerCase();
                        matchesGroup = userGroups.includes(filterGroup);
                    }

                    // Filtro de status
                    let matchesStatus = true;
                    if (filterStatus) {
                        const isActive = user.disabled === "0" || user.disabled === 0;
                        if (filterStatus === 'active') {
                            matchesStatus = isActive;
                        } else if (filterStatus === 'inactive') {
                            matchesStatus = !isActive;
                        }
                    }

                    return matchesSearch && matchesAccessLevel && matchesGroup && matchesStatus;
                });

                currentPage = 1;
                renderUsersPaginated(filteredUsers);
                updateStats(filteredUsers);
            }

            // Event listeners para os filtros
            document.getElementById('search-input').addEventListener('input', () => { currentPage = 1; applyFilters(); });
            document.getElementById('filter-access-level').addEventListener('change', () => { currentPage = 1; applyFilters(); });
            document.getElementById('filter-group').addEventListener('change', () => { currentPage = 1; applyFilters(); });
            document.getElementById('filter-status').addEventListener('change', () => { currentPage = 1; applyFilters(); });

            // Aplicar ação em massa
            document.getElementById('users-bulk-apply').addEventListener('click', async () => {
                const action = document.getElementById('users-bulk-action').value;
                const selected = Array.from(document.querySelectorAll('.users-select:checked')).map(cb => cb.value);
                if (!action || selected.length === 0) {
                    alert('Selecione uma ação e ao menos um usuário.');
                    return;
                }
                if (action === 'delete') {
                    if (!confirm(`Excluir ${selected.length} usuário(s)?`)) return;
                    // Execução em série simples
                    for (const id of selected) {
                        try {
                            const resp = await fetch(`/api/users/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });
                            await resp.json();
                        } catch (e) { console.error('Erro ao excluir', id, e); }
                    }
                    await fetchUsers();
                }
            });

            fetchUsers();

            setInterval(fetchUsers, 10000);
        });

        function deleteUser(userId) {
            if (confirm('Tem certeza que deseja excluir este usuário?')) {
                fetch(`/api/users/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert('Usuário excluído com sucesso');
                            location.reload();
                        } else {
                            alert('Erro ao excluir usuário: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao excluir usuário');
                    });
            }
        }
    </script>
@endsection
