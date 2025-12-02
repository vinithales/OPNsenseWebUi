@extends('layouts.header')

@section('main')
<div class="p-8 bg-gray-100 min-h-screen">
    {{-- Cabeçalho da Página --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-green-100 text-green-600">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h1 class="text-3xl font-bold text-gray-900">Gestão de Aliases</h1>
                <p class="text-gray-600">Grupos de IPs, redes, portas e listas</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="loadAliases()" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 20h5v-5M20 4h-5v5"></path></svg>
                Atualizar
            </button>
            <button onclick="openAliasModal()" class="flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Novo Alias
            </button>
        </div>
    </div>

    {{-- Tabela de Aliases --}}
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 flex items-center justify-between border-b">
            <div class="flex items-center space-x-4">
                <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-900">Aliases</h2>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600" id="aliasStats">0 aliases</span>
                <select id="aliases-bulk-action" class="pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">Ação em massa</option>
                    <option value="delete">Excluir selecionados</option>
                </select>
                <button id="aliases-bulk-apply" class="px-3 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">Aplicar</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><input type="checkbox" id="aliases-select-all"></th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conteúdo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Conteúdo será preenchido via JavaScript --}}
                </tbody>
            </table>
        </div>
        <!-- Paginação -->
        <div class="px-6 py-4 border-t flex flex-col md:flex-row md:items-center md:justify-between gap-3" id="aliases-pagination" style="display:none">
            <div class="flex items-center gap-3">
                <div class="text-sm text-gray-600" id="aliases-pagination-info"></div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Por página:</span>
                    <select id="aliases-page-size" class="border rounded px-2 py-1 text-sm">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-2 md:justify-end">
                <button id="aliases-prev" class="px-3 py-1.5 rounded border text-sm hover:bg-gray-50">Anterior</button>
                <div id="aliases-page-numbers" class="flex items-center gap-1"></div>
                <span id="aliases-page-indicator" class="text-sm text-gray-700"></span>
                <button id="aliases-next" class="px-3 py-1.5 rounded border text-sm hover:bg-gray-50">Próxima</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Criar Alias --}}
<div id="aliasModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Novo Alias</h3>
                <button onclick="closeAliasModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="aliasForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome *</label>
                        <input type="text" id="aliasName" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo *</label>
                        <select id="aliasType" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="host">Host(s)</option>
                            <option value="network">Network(s)</option>
                            <option value="port">Port(s)</option>
                            <option value="url">URL (IPs)</option>
                            <option value="geoip">GeoIP</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Conteúdo *</label>
                    <textarea id="aliasContent" required rows="4" placeholder="Ex: 192.168.1.0/24 ou 10.0.0.1" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"></textarea>
                    <p class="mt-1 text-sm text-gray-500">Insira IPs, redes ou portas (um por linha ou separados por vírgula)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea id="aliasDescription" rows="2" placeholder="Descrição do alias..." class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"></textarea>
                </div>

                <div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="aliasEnabled" checked class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Ativo</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeAliasModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Salvar Alias</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Estado global
let allAliases = [];
let isLoading = false;
let currentPage = 1;
let pageSize = 10;
let currentAliasesList = [];

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    loadAliases();
    setupEventListeners();

    // Check for success message from edit page
    const successMessage = sessionStorage.getItem('aliasEditSuccess');
    if (successMessage) {
        showNotification('success', successMessage);
        sessionStorage.removeItem('aliasEditSuccess');
    }
});

// Configurar event listeners
function setupEventListeners() {
    document.getElementById('aliasForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveAlias();
    });

    // Aplicação em massa
    const bulkBtn = document.getElementById('aliases-bulk-apply');
    if (bulkBtn) {
        bulkBtn.addEventListener('click', async () => {
            const action = document.getElementById('aliases-bulk-action').value;
            const selected = Array.from(document.querySelectorAll('.aliases-select:checked')).map(cb => cb.value);
            if (!action || selected.length === 0) {
                alert('Selecione uma ação e ao menos um alias.');
                return;
            }
            if (action === 'delete') {
                if (!confirm(`Excluir ${selected.length} alias(es)?`)) return;
                for (const id of selected) {
                    try {
                        const resp = await fetch(`/api/aliases/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        await resp.json();
                    } catch (e) { console.error('Erro ao excluir alias', id, e); }
                }
                await applyAliasChanges();
                await loadAliases();
            }
        });
    }
}

// Carregar aliases
async function loadAliases() {
    if (isLoading) return;

    isLoading = true;
    const tbody = document.querySelector('tbody');

    try {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center">
                    <div class="flex justify-center items-center">
                        <svg class="animate-spin h-8 w-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="ml-3 text-gray-600">Carregando aliases...</span>
                    </div>
                </td>
            </tr>
        `;

        const response = await fetch('/api/aliases');
        const data = await response.json();

        if (data.status === 'success') {
            allAliases = Array.isArray(data.data) ? data.data : [];
            const totalPages = Math.max(1, Math.ceil(allAliases.length / pageSize));
            currentPage = Math.min(currentPage, totalPages);
            renderAliasesPaginated(allAliases);
            updateStats();
        } else {
            throw new Error(data.message || 'Erro ao carregar aliases');
        }
    } catch (error) {
        console.error('Erro:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-red-500">
                    <div class="flex items-center justify-center">
                        <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Erro ao carregar aliases: ${error.message}</span>
                    </div>
                </td>
            </tr>
        `;
    } finally {
        isLoading = false;
    }
}

// Renderizar aliases na tabela
function renderAliases(aliases) {
    const tbody = document.querySelector('tbody');

    if (!aliases || aliases.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.47 3.84a.75.75 0 01.78 0l7.5 4.5a.75.75 0 010 1.32l-7.5 4.5a.75.75 0 01-.78 0l-7.5-4.5a.75.75 0 010-1.32l7.5-4.5z" clip-rule="evenodd" />
                    </svg>
                    <p class="mt-2">Nenhum alias configurado</p>
                    <button onclick="openAliasModal()" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Criar Primeiro Alias
                    </button>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = aliases.map((alias, index) => {
            const uuid = alias.uuid;
            const name = alias.name || alias['alias']?.name || '-';
            const type = alias.type || alias['alias']?.type || '-';
            const content = alias.content || alias['alias']?.content || '-';
            const description = alias.description || alias.descr || alias['alias']?.description || '';

            // Truncar conteúdo se for muito longo
            const displayContent = content.length > 50 ? content.substring(0, 50) + '...' : content;

            // Só exibe botão editar se houver uuid válido
            return `
                <tr class="transition-colors hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="aliases-select" value="${uuid || ''}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">${type}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-mono" title="${content}">${displayContent}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${description || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm flex gap-2 justify-center">
                        ${uuid ? `<a href="/aliases/${uuid}/edit" class="text-gray-400 hover:text-indigo-600" title="Editar">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>` : ''}
                        ${uuid ? `<button onclick="deleteAlias('${uuid}')" class="text-gray-400 hover:text-red-600" title="Excluir">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>` : ''}
                    </td>
                </tr>
            `;
    }).join('');
}

// Renderização paginada
function renderAliasesPaginated(list) {
    const total = list.length;
    const start = (currentPage - 1) * pageSize;
    const pageItems = list.slice(start, start + pageSize);
    renderAliases(pageItems);

    const wrap = document.getElementById('aliases-pagination');
    const info = document.getElementById('aliases-pagination-info');
    const indicator = document.getElementById('aliases-page-indicator');
    const prevBtn = document.getElementById('aliases-prev');
    const nextBtn = document.getElementById('aliases-next');
    const sizeSel = document.getElementById('aliases-page-size');
    const numbers = document.getElementById('aliases-page-numbers');

    wrap.style.display = '';
    const totalPages = Math.max(1, Math.ceil(total / pageSize));
    info.textContent = `${total === 0 ? 0 : start + 1}-${Math.min(start + pageSize, total)} de ${total}`;
    indicator.textContent = `Página ${currentPage} de ${totalPages}`;
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage >= totalPages;
    prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; renderAliasesPaginated(list); } };
    nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; renderAliasesPaginated(list); } };

    currentAliasesList = list;
    if (sizeSel && parseInt(sizeSel.value, 10) !== pageSize) sizeSel.value = String(pageSize);
    sizeSel.onchange = () => { pageSize = parseInt(sizeSel.value, 10) || 10; currentPage = 1; renderAliasesPaginated(currentAliasesList); };

    numbers.innerHTML = '';
    const makeBtn = (p, label = null, disabled = false) => {
        const b = document.createElement('button');
        b.className = `px-2.5 py-1 rounded border text-sm ${p === currentPage ? 'bg-gray-100 font-semibold' : 'hover:bg-gray-50'}`;
        b.textContent = label || String(p);
        b.disabled = disabled || p === currentPage;
        b.onclick = () => { currentPage = p; renderAliasesPaginated(list); };
        return b;
    };
    const addEllipsis = () => { const s = document.createElement('span'); s.className='px-1 text-gray-500'; s.textContent='…'; numbers.appendChild(s); };
    const totalToShow = 5;
    const totalPagesInt = totalPages;
    if (totalPagesInt <= totalToShow + 2) {
        for (let p=1;p<=totalPagesInt;p++) numbers.appendChild(makeBtn(p));
    } else {
        numbers.appendChild(makeBtn(1));
        let startP = Math.max(2, currentPage-2);
        let endP = Math.min(totalPagesInt-1, currentPage+2);
        if (startP>2) addEllipsis();
        for (let p=startP;p<=endP;p++) numbers.appendChild(makeBtn(p));
        if (endP<totalPagesInt-1) addEllipsis();
        numbers.appendChild(makeBtn(totalPagesInt));
    }
}

// Atualizar estatísticas
function updateStats() {
    const totalCount = allAliases.length;
    const statsSpan = document.getElementById('aliasStats');
    if (statsSpan) {
        statsSpan.textContent = `${totalCount} ${totalCount === 1 ? 'alias' : 'aliases'}`;
    }
}

// Abrir modal de alias (criar ou editar)
let editingAliasUuid = null;
function openAliasModal(editData = null) {
    const modal = document.getElementById('aliasModal');
    const title = document.getElementById('modalTitle');
    document.getElementById('aliasForm').reset();
    document.getElementById('aliasEnabled').checked = true;
    editingAliasUuid = null;

    if (editData) {
        title.textContent = 'Editar Alias';
        document.getElementById('aliasName').value = editData.name || '';
        document.getElementById('aliasType').value = editData.type || '';
        document.getElementById('aliasContent').value = editData.content || '';
        document.getElementById('aliasDescription').value = editData.description || editData.descr || '';
        document.getElementById('aliasEnabled').checked = editData.enabled == 1 || editData.enabled === true || editData.enabled === '1';
        editingAliasUuid = editData.uuid;
    } else {
        title.textContent = 'Novo Alias';
    }
    modal.classList.remove('hidden');
}

// Função para buscar dados e abrir modal de edição
async function editAlias(uuid) {
    try {
        const resp = await fetch(`/api/aliases/${uuid}`);
        const data = await resp.json();
        if (data.status === 'success') {
            openAliasModal({ ...data.data, uuid });
        } else {
            showNotification('error', data.message || 'Erro ao buscar alias');
        }
    } catch (e) {
        showNotification('error', 'Erro ao buscar alias');
    }
}

// Fechar modal
function closeAliasModal() {
    document.getElementById('aliasModal').classList.add('hidden');
    document.getElementById('aliasForm').reset();
}

// Salvar alias (criar ou editar)
async function saveAlias() {
    const aliasData = {
        name: document.getElementById('aliasName').value,
        type: document.getElementById('aliasType').value,
        content: document.getElementById('aliasContent').value,
        description: document.getElementById('aliasDescription').value,
        enabled: document.getElementById('aliasEnabled').checked
    };

    try {
        let response;
        if (editingAliasUuid) {
            // Editar
            response = await fetch(`/api/aliases/${editingAliasUuid}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(aliasData)
            });
        } else {
            // Criar
            response = await fetch('/api/aliases', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(aliasData)
            });
        }

        const data = await response.json();

        if (data.status === 'success') {
            closeAliasModal();
            showNotification('success', data.message || (editingAliasUuid ? 'Alias atualizado com sucesso!' : 'Alias criado com sucesso!'));
            await applyAliasChanges();
            await loadAliases();
        } else {
            throw new Error(data.message || (editingAliasUuid ? 'Erro ao atualizar alias' : 'Erro ao criar alias'));
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('error', error.message);
    }
}

// Deletar alias
async function deleteAlias(uuid) {
    if (!confirm('Tem certeza que deseja excluir este alias?')) {
        return;
    }

    try {
        const response = await fetch(`/api/aliases/${uuid}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.status === 'success') {
            showNotification('success', data.message || 'Alias excluído com sucesso!');
            await applyAliasChanges();
            await loadAliases();
        } else {
            throw new Error(data.message || 'Erro ao excluir alias');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('error', error.message);
    }
}

// Aplicar mudanças de aliases
async function applyAliasChanges() {
    try {
        const response = await fetch('/api/aliases/apply', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.status !== 'success') {
            console.warn('Aviso ao aplicar mudanças:', data.message);
        }
    } catch (error) {
        console.error('Erro ao aplicar mudanças:', error);
    }
}

// Mostrar notificação
function showNotification(type, message) {
    const colors = {
        success: 'bg-green-100 border-green-400 text-green-700',
        error: 'bg-red-100 border-red-400 text-red-700',
        info: 'bg-blue-100 border-blue-400 text-blue-700'
    };

    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} border px-4 py-3 rounded-lg shadow-lg z-50 max-w-md`;
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="mr-2">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>

@endsection
