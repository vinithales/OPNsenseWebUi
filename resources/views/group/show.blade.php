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
                <h1 class="text-3xl font-bold text-gray-900">Editar Grupo: <span class="text-indigo-600">{{ $group['name'] ?? '' }}</span></h1>
                <p class="text-gray-600">Altere a descrição, os membros e as permissões do grupo.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('groups.index') }}" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Voltar para a Lista
            </a>
        </div>
    </div>

    {{-- Formulário de Edição --}}
    <form action="{{ route('groups.update', $group['uuid'] ?? '') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-lg shadow-sm p-8 space-y-8">

            <div>
                <h2 class="text-xl font-semibold text-gray-900">Informações do Grupo</h2>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome do Grupo</label>
                        <input type="text" name="name" id="name" value="{{ $group['name'] ?? '' }}" readonly disabled
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed sm:text-sm">
                        <p class="mt-2 text-xs text-gray-500">O nome do grupo não pode ser alterado.</p>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                        <textarea name="description" id="description" rows="1" placeholder="Qual a finalidade deste grupo?"
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ $group['description'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200"></div>

            <div>
                <h2 class="text-xl font-semibold text-gray-900">Membros do Grupo</h2>
                <p class="mt-1 text-sm text-gray-600">Adicione ou remova usuários deste grupo.</p>
                <div class="mt-4">
                    <select id="members-select" name="members[]" multiple placeholder="Selecione um ou mais usuários..." class="tom-select-class">
                        @if(isset($group['member']))
                            @foreach($group['member'] as $userId => $memberData)
                                <option value="{{ $userId }}" selected>
                                    {{ $memberData['value'] ?? '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="border-t border-gray-200"></div>

            <div>
                <h2 class="text-xl font-semibold text-gray-900">Permissões do Grupo</h2>
                <p class="mt-1 text-sm text-gray-600">Selecione todas as permissões que os membros deste grupo terão acesso.</p>
                <div class="mt-4 space-y-4">
                    @php
                        // Agrupar privilégios por categoria
                        $groupedPrivileges = [];
                        if (isset($group['priv'])) {
                            foreach ($group['priv'] as $privKey => $privData) {
                                $category = explode(':', $privData['value'])[0] ?? 'Outros';
                                $groupedPrivileges[$category][$privKey] = $privData;
                            }
                        }
                    @endphp

                    @foreach($groupedPrivileges as $groupName => $privileges)
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button type="button" @click="open = !open" class="w-full flex justify-between items-center p-3 bg-gray-50 hover:bg-gray-100 focus:outline-none">
                            <span class="font-medium text-gray-800">{{ $groupName }}</span>
                            <svg class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-cloak class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-2 border-t">
                            @foreach($privileges as $privKey => $priv)
                            <label class="flex items-center font-normal">
                                <input type="checkbox" name="priv[]" value="{{ $privKey }}" {{ ($priv['selected'] ?? 0) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">{{ $priv['value'] ?? '' }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Botões de Ação --}}
            <div class="pt-6 mt-4 flex justify-end space-x-3 border-t border-gray-200">
                <a href="{{ route('groups.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700">
                    Salvar Alterações
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Inicializar Tom Select para o select de membros
document.addEventListener('DOMContentLoaded', function() {
    new TomSelect('#members-select', {
        plugins: ['remove_button'],
        create: false,
        maxItems: null
    });
});
</script>

<style>
[x-cloak] {
    display: none;
}
</style>
@endsection
