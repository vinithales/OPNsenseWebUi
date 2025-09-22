@extends('layouts.header')

@section('main')
    <div class="p-8 bg-gray-100 min-h-screen">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gerenciamento de Usuários</h1>
                <p class="text-gray-600">Controle de acesso e permissões do sistema</p>
            </div>
            <div class="flex space-x-3">
                <button
                    class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Importar Usuários
                </button>
                <a href="{{ route('users.create') }}"
                    class="flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Adicionar Usuário
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
                    <p class="text-sm text-gray-500">Carregando...</p>
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

        <!-- Tabela de Usuários -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Lista de Usuários</h2>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" placeholder="Buscar usuários..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        id="search-input">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usuário</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nível
                                de Acesso</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Último Login</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Ações</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="users-table-body">
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center">
                                <div class="flex justify-center items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Carregando usuários...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Função para buscar usuários da API
            async function fetchUsers() {
                try {
                    const response = await fetch('api/users');
                    const data = await response.json();

                    if (data.status === 'success') {
                        renderUsers(data.data);
                        updateStats(data.data);
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
                            <td colspan="6" class="px-6 py-4 text-center">
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

                    row.innerHTML = `
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
                            <a href="/users/${user.uuid || user.id}/edit" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                            <button class="text-red-600 hover:text-red-900" onclick="deleteUser('${user.uuid || user.id}')">Excluir</button>
                        </td>
                    `;

                    tbody.appendChild(row);
                });
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

            document.getElementById('search-input').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('#users-table-body tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
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
