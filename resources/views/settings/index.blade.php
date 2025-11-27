@extends('layouts.header')

@section('title', 'Configurações do Sistema')

@section('main')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-600 flex items-center">
            <svg class="w-8 h-8 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Configurações do Sistema
        </h1>
        <p class="text-gray-600 mt-2">Gerencie as configurações da aplicação e conexão com OPNsense</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="flex">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-red-800">Erros encontrados:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" class="bg-white rounded-lg shadow-lg" x-data="{ activeTab: 'opnsense' }">
        @csrf
        @method('PUT')

        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button type="button" @click="activeTab = 'opnsense'" :class="activeTab === 'opnsense' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                    Conexão OPNsense
                </button>
                <button type="button" @click="activeTab = 'advanced'" :class="activeTab === 'advanced' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    Configurações Avançadas
                </button>
            </nav>
        </div>

        <div class="p-8">
            <!-- Tab: OPNsense -->
            <div x-show="activeTab === 'opnsense'" x-transition>
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Configuração de Conexão OPNsense</h2>
                <p class="text-gray-600 mb-6">Atualize as credenciais de acesso à API do seu firewall OPNsense</p>

                <div class="space-y-4">
                    <div>
                        <label for="opnsense_url" class="block text-sm font-medium text-gray-700 mb-2">
                            URL do OPNsense *
                        </label>
                        <input type="url" id="opnsense_url" name="opnsense_url" value="{{ old('opnsense_url', $settings['opnsense_url']) }}"
                            placeholder="http://192.168.1.1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500">Exemplo: http://192.168.1.1 ou https://firewall.local</p>
                    </div>

                    <div>
                        <label for="opnsense_api_key" class="block text-sm font-medium text-gray-700 mb-2">
                            API Key *
                        </label>
                        <textarea id="opnsense_api_key" name="opnsense_api_key" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent font-mono text-sm">{{ old('opnsense_api_key', $settings['opnsense_api_key']) }}</textarea>
                    </div>

                    <div>
                        <label for="opnsense_api_secret" class="block text-sm font-medium text-gray-700 mb-2">
                            API Secret *
                        </label>
                        <textarea id="opnsense_api_secret" name="opnsense_api_secret" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent font-mono text-sm">{{ old('opnsense_api_secret', $settings['opnsense_api_secret']) }}</textarea>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-500 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-blue-800">Como obter as credenciais API?</h3>
                                <p class="mt-2 text-sm text-blue-700">
                                    1. Acesse seu OPNsense<br>
                                    2. Vá em <strong>System → Access → Users</strong><br>
                                    3. Edite seu usuário<br>
                                    4. Na aba <strong>API Keys</strong>, clique em <strong>+</strong> para gerar
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Avançado -->
            <div x-show="activeTab === 'advanced'" x-transition x-cloak>
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Configurações Avançadas</h2>
                <p class="text-gray-600 mb-6">Configurações do sistema e banco de dados</p>

                <div class="space-y-4">
                    <div>
                        <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome da Aplicação
                        </label>
                        <input type="text" id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name']) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="app_url" class="block text-sm font-medium text-gray-700 mb-2">
                            URL da Aplicação
                        </label>
                        <input type="url" id="app_url" name="app_url" value="{{ old('app_url', $settings['app_url']) }}"
                            placeholder="http://localhost"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="db_port" class="block text-sm font-medium text-gray-700 mb-2">
                            Porta do Banco de Dados
                        </label>
                        <input type="number" id="db_port" name="db_port" value="{{ old('db_port', $settings['db_port']) }}"
                            min="1" max="65535"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500">Porta padrão MySQL: 3306</p>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-500 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <p class="text-sm text-yellow-700">
                                Altere estas configurações apenas se necessário. Mudanças incorretas podem afetar o funcionamento do sistema.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 rounded-b-lg flex justify-between items-center">
            <p class="text-sm text-gray-500">* Campos obrigatórios</p>
            <div class="flex gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Salvar Configurações
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
