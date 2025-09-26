@extends('layouts.header')

@section('main')
    <div class="p-8 bg-gray-100 min-h-screen">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <strong>⚠️ Erros encontrados:</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Cabeçalho da Página --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar Usuário: <span
                        class="text-indigo-600">{{ $user['name'] }}</span></h1>
                <p class="text-gray-600">Altere as informações do usuário, grupos e permissões.</p>
            </div>
            <div>
                <a href="{{ route('users.index') }}"
                    class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar para a Lista
                </a>
            </div>
        </div>

        {{-- Container Principal com Abas --}}
        <div class="bg-white rounded-lg shadow-sm" x-data="{ tab: 'general' }">
            {{-- Navegação das Abas --}}
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-8" aria-label="Tabs">
                    <button @click="tab = 'general'"
                        :class="{ 'border-indigo-500 text-indigo-600': tab === 'general', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'general' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none">
                        Informações Gerais
                    </button>
                    <button @click="tab = 'groups'"
                        :class="{ 'border-indigo-500 text-indigo-600': tab === 'groups', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'groups' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none">
                        Grupos e Acesso
                    </button>
                    <button @click="tab = 'permissions'"
                        :class="{ 'border-indigo-500 text-indigo-600': tab === 'permissions', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'permissions' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none">
                        Permissões Efetivas
                    </button>
                </nav>
            </div>

            {{-- Formulário --}}
            <form action="{{ route('users.update', $user['uuid']) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                {{-- Conteúdo da Aba 1: Informações Gerais --}}
                <div x-show="tab === 'general'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome de Usuário
                                (Login)</label>
                            <input type="text" name="name" id="name" value="{{ $user['name'] }}" readonly
                                disabled
                                class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm sm:text-sm cursor-not-allowed">
                            <p class="mt-2 text-xs text-gray-500">O nome de usuário não pode ser alterado.</p>
                        </div>
                        <div>
                            <label for="descr" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                            <input type="text" name="descr" id="descr" value="{{ $user['descr'] ?? '' }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ $user['email'] ?? '' }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="expires" class="block text-sm font-medium text-gray-700">Data de Expiração
                                (Opcional)</label>
                            <input type="date" name="expires" id="expires" value="{{ $user['expires'] ?? '' }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <p class="mt-2 text-xs text-gray-500">Deixe em branco para não alterar a senha atual.</p>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar
                                Nova Senha</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label for="authorizedkeys" class="block text-sm font-medium text-gray-700">Chaves SSH
                                Autorizadas</label>
                            <textarea name="authorizedkeys" id="authorizedkeys" rows="4"
                                class="mt-1 font-mono text-sm block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">{{ $user['authorizedkeys'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status da Conta</label>
                            <label class="mt-2 inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="disabled" value="1" class="sr-only peer"
                                    {{ $user['disabled'] == '0' ? '' : 'checked' }}>
                                <div
                                    class="relative w-11 h-6 bg-red-500 rounded-full peer peer-focus:ring-4 peer-focus:ring-red-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-400">
                                </div>
                                <span
                                    class="ms-3 text-sm font-medium text-gray-900">{{ $user['disabled'] == '0' ? 'Ativo' : 'Desativado' }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Conteúdo da Aba 2: Grupos e Acesso --}}
                <div x-show="tab === 'groups'" class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Grupos de Usuários</h3>
                        <div class="space-y-2">
                            @foreach ($user['group_memberships'] ?? [] as $group)
                                <label class="flex items-center">
                                    <input type="checkbox" name="groups[]" value="{{ $group['value'] }}"
                                        {{ isset($group['selected']) && $group['selected'] ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $group['value'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label for="shell" class="block text-sm font-medium text-gray-700">Acesso ao Terminal
                                (Shell)</label>
                            <select id="shell" name="shell"
                                class="mt-1 block w-full pl-3 pr-10 py-2 border-gray-300 rounded-md">
                                @foreach ($user['shell'] ?? [] as $key => $shellInfo)
                                    <option value="{{ $key }}"
                                        {{ isset($shellInfo['selected']) && $shellInfo['selected'] ? 'selected' : '' }}>
                                        {{ $shellInfo['value'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700">Idioma da
                                Interface</label>
                            <select id="language" name="language"
                                class="mt-1 block w-full pl-3 pr-10 py-2 border-gray-300 rounded-md">
                                @foreach ($user['language'] ?? [] as $key => $langInfo)
                                    <option value="{{ $key }}"
                                        {{ isset($langInfo['selected']) && $langInfo['selected'] ? 'selected' : '' }}>
                                        {{ $langInfo['value'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Conteúdo da Aba 3: Permissões Efetivas --}}
                <div x-show="tab === 'permissions'" class="space-y-4">
                    <p class="text-sm text-gray-600">Selecione as permissões específicas que este usuário terá acesso no
                        sistema.</p>
                    {{-- Aqui você faria um loop nos privilégios agrupados --}}
                    @foreach ($groupedPrivileges ?? [] as $groupName => $privileges)
                        <div x-data="{ open: false }" class="border rounded-md">
                            <button type="button" @click="open = !open"
                                class="w-full flex justify-between items-center p-3 bg-gray-50 hover:bg-gray-100 focus:outline-none">
                                <span class="font-medium text-gray-800">{{ $groupName }}</span>
                                <svg class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': open }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-2 border-t">
                                @foreach ($privileges as $privKey => $priv)
                                    <label class="flex items-center font-normal">
                                        <input type="checkbox" name="priv[]" value="{{ $privKey }}"
                                            {{ isset($priv['selected']) && $priv['selected'] ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $priv['value'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Botões de Ação --}}
                <div class="mt-8 flex justify-end space-x-3 border-t pt-6">
                    <a href="{{ route('users.index') }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                            </path>
                        </svg>
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
