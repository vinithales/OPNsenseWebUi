@extends('layouts.header')

@section('main')
<div class="p-8 bg-gray-100 min-h-screen">
    {{-- Cabeçalho da Página --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-600">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h1 class="text-3xl font-bold text-gray-900">Gerenciamento de Grupos</h1>
                <p class="text-gray-600">Crie e administre os grupos de usuários do sistema</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Importar em CSV
            </button>
            <button class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Exportar em CSV
            </button>
            <button class="flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Novo Grupo
            </button>
        </div>
    </div>

    {{-- Tabela de Grupos --}}
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 flex items-center justify-between border-b">
             <div class="flex items-center space-x-4">
                <h2 class="text-xl font-semibold text-gray-900">Lista de Grupos</h2>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" placeholder="Buscar grupo..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md sm:text-sm" id="search-input">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome do Grupo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nº de Membros</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="groups-table-body">
                    {{-- Exemplo de Linha 1: Administradores --}}

                    {{-- Mais linhas podem ser adicionadas dinamicamente aqui --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função para buscar grupos da API
        async function fetchGroups() {
            try {
                const response = await fetch('api/groups');
                const data = await response.json();

                if (data.status === 'success') {
                    renderGroups(data.data);
                } else {
                    throw new Error(data.message || 'Erro ao carregar grupos');
                }
            } catch (error) {
                console.error('Erro:', error);
                document.getElementById('groups-table-body').innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-red-500">
                            Erro ao carregar grupos: ${error.message}
                        </td>
                    </tr>
                `;
            }
        }

        // Função para renderizar grupos na tabela
        function renderGroups(groups) {
            const tbody = document.getElementById('groups-table-body');
            tbody.innerHTML = '';

            if (groups.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center">
                            Nenhum grupo encontrado
                        </td>
                    </tr>
                `;
                return;
            }

            groups.forEach(group => {
                const row = document.createElement('tr');
                row.classList.add('hover:bg-gray-50');
                let editUrl = "#".replace('__UUID__', group.uuid);

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${group.name || 'Sem nome'}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600">${group.description || 'Sem descrição'}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-800">${group.members_count || 0}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-4">
                            <a href="${editUrl}" class="text-gray-400 hover:text-indigo-600" title="Editar">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828
                                        2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <button class="text-gray-400 hover:text-red-600" title="Excluir" onclick="deleteGroup('${group.uuid}')">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138
                                        21H7.862a2 2 0 01-1.995-1.858L5
                                        7m5 4v6m4-6v6m1-10V4a1 1 0
                                        00-1-1h-4a1 1 0 00-1
                                        1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                `;

                tbody.appendChild(row);
            });
        }

        // Filtro de busca
        document.getElementById('search-input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#groups-table-body tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        fetchGroups();

        setInterval(fetchGroups, 10000);
    });

    // Função para excluir grupo
    function deleteGroup(groupId) {
        if (confirm('Tem certeza que deseja excluir este grupo?')) {
            fetch(`/api/groups/${groupId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Grupo excluído com sucesso');
                    location.reload();
                } else {
                    alert('Erro ao excluir grupo: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao excluir grupo');
            });
        }
    }
</script>

@endsection
