@extends('layouts.header')

@section('main')
    <div class="p-8 bg-gray-100 min-h-screen">
        {{-- Cabeçalho da Página --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-600">
                        <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h1 class="text-3xl font-bold text-gray-900">Criar Novo Grupo</h1>
                    <p class="text-gray-600">Defina o nome, os membros e as permissões para o novo grupo.</p>
                </div>
            </div>
            <div>
                <a href="{{ route('groups.index') }} "
                    class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar para a Lista
                </a>
            </div>
        </div>

        <form action="{{ route('groups.store') }}" method="POST">
            @csrf
            <div class="bg-white rounded-lg shadow-sm p-8 space-y-8">

                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Informações do Grupo</h2>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome do Grupo</label>
                            <input type="text" name="name" id="name" placeholder="Ex: Operadores de Suporte"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required value="{{ old('name') }}">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                            <textarea name="description" id="description" rows="1" placeholder="Qual a finalidade deste grupo?"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Membros do Grupo</h2>
                    <p class="mt-1 text-sm text-gray-600">Selecione os usuários que farão parte deste grupo inicialmente
                        (opcional).</p>
                    <div class="mt-4">
                        <select id="members-select" name="members[]" multiple
                            placeholder="Selecione um ou mais usuários...">
                        </select>
                    </div>
                    @error('members')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-200"></div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Permissões do Grupo</h2>
                    <p class="mt-1 text-sm text-gray-600">Selecione todas as permissões que os membros deste grupo terão
                        acesso.</p>
                    <div id="permissions-container" class="mt-4 space-y-4"></div>
                    @error('priv')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botões de Ação --}}
                <div class="pt-6 mt-4 flex justify-end space-x-3 border-t border-gray-200">
                    <a href="{{ route('groups.index') }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Salvar Grupo
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        async function fetchUsersDataAndInitSelect() {
            try {
                const response = await fetch('http://127.0.0.1:8000/api/users');
                const data = await response.json();

                if (data.status === 'success') {
                    const userSelect = document.getElementById('members-select');
                    if (!userSelect) return;

                    userSelect.innerHTML = '';

                    data.data.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.uid;
                        option.textContent = user.name;
                        userSelect.appendChild(option);
                    });

                    new TomSelect("#members-select", {
                        plugins: ['remove_button'],
                        persist: false,
                        create: false,
                        placeholder: "Selecione um ou mais usuários...",
                    });
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
            }
        }

        async function fetchPrevilegesData() {
            try {
                const response = await fetch('http://127.0.0.1:8000/api/permissions');
                const data = await response.json();

                if (data.status === 'success') {
                    const permissions = data.data;
                    const container = document.querySelector('#permissions-container');
                    container.innerHTML = '';

                    const grid = document.createElement('div');
                    grid.className = 'p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-2';

                    permissions.forEach(priv => {
                        const label = document.createElement('label');
                        label.className = 'flex items-center font-normal';

                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.name = 'priv[]';
                        checkbox.value = priv.id;
                        checkbox.className =
                            'h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500';

                        const text = document.createElement('span');
                        text.className = 'ml-2 text-sm text-gray-700';
                        text.textContent = priv.name;

                        label.appendChild(checkbox);
                        label.appendChild(text);
                        grid.appendChild(label);
                    });

                    container.appendChild(grid);
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            fetchUsersDataAndInitSelect();
            fetchPrevilegesData();
        });
    </script>
@endsection
