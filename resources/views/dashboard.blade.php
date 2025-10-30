@extends('layouts.header')

@section('main')
    <div class="p-8 bg-gray-100 min-h-screen">
        {{-- Cabeçalho da Página --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 text-purple-600">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600">Visão geral do sistema e estatísticas</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                    Sistema Online
                </span>
                <button onclick="loadStats()" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Atualizar
                </button>
            </div>
        </div>

        {{-- Cards de Estatísticas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total de Usuários --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Total de Usuários</h3>
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-semibold text-gray-900 mb-1" id="totalUsers">--</p>
                    <p class="text-sm text-gray-500">Usuários registrados</p>
                </div>
            </div>

            {{-- Total de Grupos --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Total de Grupos</h3>
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-semibold text-gray-900 mb-1" id="totalGroups">--</p>
                    <p class="text-sm text-gray-500">Grupos criados</p>
                </div>
            </div>

            {{-- Total de Aliases --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Total de Aliases</h3>
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-semibold text-gray-900 mb-1" id="totalAliases">--</p>
                    <p class="text-sm text-gray-500">Aliases configurados</p>
                </div>
            </div>

            {{-- Status do Sistema --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Status do Sistema</h3>
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-purple-100 text-purple-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-semibold text-green-600 mb-1" id="systemStatus">Online</p>
                    <p class="text-sm text-gray-500">Tudo funcionando</p>
                </div>
            </div>
        </div>

        {{-- Ações Rápidas --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b">
                <div class="flex items-center space-x-4">
                    <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900">Ações Rápidas</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('users.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Adicionar Usuário</p>
                            <p class="text-xs text-gray-500">Criar novo usuário</p>
                        </div>
                    </a>

                    <a href="{{ route('groups.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100 text-indigo-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Criar Grupo</p>
                            <p class="text-xs text-gray-500">Novo grupo de usuários</p>
                        </div>
                    </a>

                    <a href="{{ route('users.import') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-green-100 text-green-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Importar Usuários</p>
                            <p class="text-xs text-gray-500">Upload de arquivo Excel</p>
                        </div>
                    </a>

                    <a href="{{ route('aliases.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-yellow-100 text-yellow-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Gerenciar Aliases</p>
                            <p class="text-xs text-gray-500">Grupos de IPs e redes</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript para carregar estatísticas --}}
    <script>
        // Carregar estatísticas quando a página carrega
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
        });

        // Função para carregar estatísticas do dashboard
        async function loadStats() {
            try {
                // Mostrar indicador de carregamento
                updateStatsDisplay('--', '--', '--', 'Carregando...', 'text-gray-600');

                const response = await fetch('/api/dashboard/stats', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    const stats = data.data;

                    // Atualizar os valores na interface
                    updateStatsDisplay(
                        stats.total_users,
                        stats.total_groups,
                        stats.total_aliases,
                        stats.system_status === 'online' ? 'Online' : 'Offline',
                        stats.system_status === 'online' ? 'text-green-600' : 'text-red-600'
                    );

                    console.log('Estatísticas carregadas:', stats);
                } else {
                    throw new Error(data.message || 'Erro ao carregar estatísticas');
                }
            } catch (error) {
                console.error('Erro ao carregar estatísticas:', error);

                // Mostrar erro na interface
                updateStatsDisplay('Erro', 'Erro', 'Erro', 'Offline', 'text-red-600');

                // Opcional: mostrar notificação de erro
                showNotification('error', 'Erro ao carregar estatísticas do dashboard');
            }
        }

        // Função para atualizar a exibição das estatísticas
        function updateStatsDisplay(totalUsers, totalGroups, totalAliases, systemStatus, statusClass) {
            const totalUsersElement = document.getElementById('totalUsers');
            const totalGroupsElement = document.getElementById('totalGroups');
            const totalAliasesElement = document.getElementById('totalAliases');
            const systemStatusElement = document.getElementById('systemStatus');

            if (totalUsersElement) {
                totalUsersElement.textContent = totalUsers;
            }

            if (totalGroupsElement) {
                totalGroupsElement.textContent = totalGroups;
            }

            if (totalAliasesElement) {
                totalAliasesElement.textContent = totalAliases;
            }

            if (systemStatusElement) {
                systemStatusElement.textContent = systemStatus;
                systemStatusElement.className = `text-3xl font-semibold mb-1 ${statusClass}`;
            }
        }

        // Função para mostrar notificações (opcional, se não existir no layout)
        function showNotification(type, message) {
            // Verifica se existe uma função de notificação global
            if (typeof window.showNotification === 'function') {
                window.showNotification(type, message);
            } else {
                // Fallback simples
                console.log(`${type.toUpperCase()}: ${message}`);
            }
        }
    </script>
@endsection
