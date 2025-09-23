
@extends('layouts.header')

@section('main')
{{-- Envolvemos todo o componente em um x-data do Alpine.js para gerenciar os estados --}}
<div class="p-8 bg-gray-100 min-h-screen" x-data="{ 
    multiSelectMode: false, 
    selectedRules: [], 
    activeMenu: null,
    // Função helper para adicionar/remover IDs do array de seleção
    toggleRule(ruleId) {
        const index = this.selectedRules.indexOf(ruleId);
        if (index === -1) {
            this.selectedRules.push(ruleId);
        } else {
            this.selectedRules.splice(index, 1);
        }
    } 
}">
    {{-- Cabeçalho da Página --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-green-100 text-green-600">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
            </div>
            <div class="ml-4">
                <h1 class="text-3xl font-bold text-gray-900">Gestão de Firewall</h1>
                <p class="text-gray-600">Controle de regras e políticas de segurança</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                Firewall Ativo
            </span>
            <button class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 20h5v-5M20 4h-5v5"></path></svg>
                Atualizar
            </button>
            <button class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                 <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Configurações
            </button>
            <button class="flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Nova Regra
            </button>
        </div>
    </div>

    {{-- Tabela de Regras --}}
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 flex items-center justify-between border-b">
             <div class="flex items-center space-x-4">
                <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <h2 class="text-xl font-semibold text-gray-900">Regras do Firewall</h2>
                
                {{-- Botões de Seleção que alternam com base no estado 'multiSelectMode' --}}
                <button x-show="!multiSelectMode" @click="multiSelectMode = true" class="flex items-center px-3 py-1 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    Seleção Múltipla
                </button>
                <button x-show="multiSelectMode" @click="multiSelectMode = false; selectedRules = []" x-cloak class="flex items-center px-3 py-1 bg-gray-100 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Cancelar Seleção
                </button>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">4 de 5 ativas</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Firewall Ativo</span>
            </div>
        </div>
        
        {{-- Barra de Ações em Massa --}}
        <div x-show="multiSelectMode && selectedRules.length > 0" x-cloak class="bg-green-50 border-b border-green-200 p-3 flex items-center justify-between transition-all">
            <span class="text-sm font-medium text-green-800">
                <strong x-text="selectedRules.length"></strong> regras selecionadas
            </span>
            <div class="flex items-center space-x-2">
                <button class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 flex items-center">Ativar Todas</button>
                <button class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 flex items-center">Desativar Todas</button>
                <button class="px-3 py-1 text-sm text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 flex items-center">Excluir Selecionadas</button>
                <button @click="multiSelectMode = false; selectedRules = []" class="p-1 text-gray-500 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" x-show="multiSelectMode" x-cloak class="px-4 py-3 w-12"><input type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"></th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridade</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ação</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interface</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Protocolo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origem</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Porta</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Controles</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Exemplo de Linha 1: Ativa, Permitir --}}
                    <tr class="transition-colors" :class="{ 'bg-green-50': multiSelectMode && selectedRules.includes(1) }">
                        <td x-show="multiSelectMode" x-cloak class="px-4 py-4"><input type="checkbox" @change="toggleRule(1)" :checked="selectedRules.includes(1)" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 text-gray-400 cursor-grab" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                <input type="text" value="1" class="w-12 p-1 text-center border border-gray-300 rounded-md">
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-green-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Permitir</span></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">LAN</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TCP</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">192.168.1.0/24</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ANY</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">80,443</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Permitir HTTP/HTTPS da LAN</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="relative flex items-center justify-center space-x-2">
                                <button class="text-gray-400 hover:text-gray-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg></button>
                                <button class="text-gray-400 hover:text-gray-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></button>
                                <button @click="activeMenu = (activeMenu === 1 ? null : 1)" class="text-gray-400 hover:text-gray-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2z"></path></svg></button>
                                
                                {{-- Menu Dropdown para a Linha 1 --}}
                                <div x-show="activeMenu === 1" @click.outside="activeMenu = null" x-cloak class="origin-top-right absolute right-0 top-full mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                    <div class="py-1" role="menu">
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Editar</a>
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Duplicar</a>
                                        <div class="border-t border-gray-100"></div>
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50" role="menuitem">Excluir</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                     {{-- Exemplo de Linha 3: Desativada --}}
                    <tr class="transition-colors" :class="{ 'bg-green-50': multiSelectMode && selectedRules.includes(3) }">
                        <td x-show="multiSelectMode" x-cloak class="px-4 py-4"><input type="checkbox" @change="toggleRule(3)" :checked="selectedRules.includes(3)" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 text-gray-400 cursor-grab" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                <input type="text" value="3" class="w-12 p-1 text-center border border-gray-300 rounded-md">
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-green-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Desativada</span></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ANY</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">UDP</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ANY</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">192.168.1.1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">53</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Permitir DNS</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                             <div class="relative flex items-center justify-center space-x-2">
                                <button class="text-gray-400 hover:text-gray-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg></button>
                                <button class="text-gray-400 hover:text-gray-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></button>
                                <button @click="activeMenu = (activeMenu === 3 ? null : 3)" class="text-gray-400 hover:text-gray-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2z"></path></svg></button>

                                {{-- Menu Dropdown para a Linha 3 --}}
                                <div x-show="activeMenu === 3" @click.outside="activeMenu = null" x-cloak class="origin-top-right absolute right-0 top-full mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                   <div class="py-1" role="menu">
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Editar</a>
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Duplicar</a>
                                        <div class="border-t border-gray-100"></div>
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50" role="menuitem">Excluir</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Adicione o Alpine.js ao seu layout principal (geralmente antes do fechamento da tag </body>) se ainda não o fez --}}
@endsection
```