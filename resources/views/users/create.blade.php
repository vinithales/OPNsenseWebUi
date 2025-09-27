@extends('layouts.header')

@section('main')
    <div class="p-8 bg-gray-100 min-h-screen">
        {{-- Cabeçalho da Página --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Criar Novo Usuário</h1>
                <p class="text-gray-600">Preencha os dados abaixo para cadastrar um novo usuário no sistema.</p>
            </div>
            <div>
                <a href="{{ route('users.index') }}"
                    class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar para a Lista
                </a>
            </div>
        </div>

        {{-- Formulário de Criação --}}
        <div class="bg-white rounded-lg shadow-sm p-8">
            {{-- Substitua a action pela sua rota de armazenamento --}}
            <form action="{{ route('users.api.create') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Coluna 1 --}}
                    <div class="space-y-6">
                        {{-- Nome Completo --}}
                        <div>
                            <label for="fullname" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                            <input type="text" name="fullname" id="fullname" placeholder="Ex: João da Silva"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                        </div>

                        {{-- Nome de Usuário (Login) --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome de Usuário
                                (Login)</label>
                            <input type="text" name="name" id="name" placeholder="Ex: joao.silva"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                            <p class="mt-2 text-xs text-gray-500">Será usado para o login. Sem espaços ou caracteres
                                especiais.</p>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" placeholder="Ex: joao.silva@empresa.com"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                        </div>

                        {{-- Nível de Acesso --}}
                        <div>
                            <label for="group" class="block text-sm font-medium text-gray-700">Nível de Acesso
                                (Grupo)</label>

                            <select id="group" name="group[]" multiple
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            </select>

                            <p class="mt-2 text-xs text-gray-500">Comece a digitar para buscar ou selecione na lista.</p>
                        </div>
                    </div>

                    {{-- Coluna 2 --}}
                    <div class="space-y-6">
                        {{-- Senha --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                        </div>

                        {{-- Confirmar Senha --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar
                                Senha</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                        </div>

                        {{-- Comentário --}}
                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700">Comentário /
                                Observações</label>
                            <textarea name="comment" id="comment" rows="3" placeholder="Alguma informação adicional sobre o usuário..."
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                        </div>

                        {{-- Data de Expiração --}}
                        <div>
                            <label for="expires" class="block text-sm font-medium text-gray-700">Data de Expiração
                                (Opcional)</label>
                            <input type="date" name="expires" id="expires"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <p class="mt-2 text-xs text-gray-500">Deixe em branco para que a conta nunca expire.</p>
                        </div>
                    </div>

                    {{-- Campos Avançados (Ocupando as duas colunas) --}}
                    <div class="md:col-span-2 mt-4 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Configurações Avançadas</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Acesso ao Terminal --}}
                            <div>
                                <label for="shell" class="block text-sm font-medium text-gray-700">Acesso ao Terminal
                                    (Shell)</label>
                                <select id="shell" name="user_shell"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="/sbin/nologin">Acesso Desabilitado</option>
                                    <option value="/bin/bash">Acesso Padrão (/bin/bash)</option>
                                </select>
                            </div>

                            {{-- Chaves SSH Autorizadas --}}
                            <div class="md:col-span-2">
                                <label for="authorizedkeys" class="block text-sm font-medium text-gray-700">Chaves SSH
                                    Autorizadas</label>
                                <textarea name="authorizedkeys" id="authorizedkeys" rows="4"
                                    placeholder="Cole uma ou mais chaves públicas SSH, uma por linha."
                                    class="mt-1 font-mono text-sm block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                <p class="mt-2 text-xs text-gray-500">Para acesso sem senha via terminal. Deixe em branco se
                                    não for necessário.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botões de Ação --}}
                <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
                    <a href="{{-- {{ route('users.index') }} --}}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Salvar Usuário
                    </button>
                </div>
            </form>
            @if (session('success'))
                <div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-md">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-md shadow-md">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
    <script>
        // Script para esconder as mensagens de sucesso/erro após alguns segundos
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('.bg-green-500');
            const errorMessage = document.querySelector('.bg-red-500');

            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 5000); // Esconde após 5 segundos
            }
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 5000); // Esconde após 5 segundos
            }


            async function fetchGroupDataAndInitSelect() {
                try {
                    const response = await fetch('http://127.0.0.1:8000/api/groups');
                    const data = await response.json();

                    if (data.status === 'success') {
                        const groupSelect = document.getElementById('group');

                        // ADICIONE ESTA VERIFICAÇÃO
                        if (!groupSelect) {
                            console.error('Elemento <select id="group"> não foi encontrado na página.');
                            return; // Para a execução da função se o elemento não existe
                        }

                        groupSelect.innerHTML = ''; // Agora esta linha é segura

                        data.data.forEach(group => {
                            const option = document.createElement('option');
                            option.value = group.gid;
                            option.textContent = group.name;
                            groupSelect.appendChild(option);
                        });

                        // Inicializa o Tom Select
                        new TomSelect(groupSelect, { // Pode passar o elemento diretamente
                            plugins: ['remove_button'],
                            placeholder: 'Selecione um ou mais grupos...',
                        });

                    } else {
                        console.error('Erro ao buscar grupos:', data.message);
                    }
                } catch (error) {
                    console.error('Erro na requisição:', error);
                }
            }

            fetchGroupDataAndInitSelect();

        });

        //validação de senha
        document.querySelector('form').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;

            if (password !== confirmPassword) {
                event.preventDefault();
                alert('As senhas não coincidem. Por favor, verifique e tente novamente.');
            }
        });
    </script>
@endsection
