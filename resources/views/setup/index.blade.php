<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração Inicial - OPNsense Web UI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-600 rounded-full mb-4">
                    <i class="fas fa-cog text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">Bem-vindo ao OPNsense Web UI</h1>
                <p class="text-gray-600">Configure o sistema para começar a usar</p>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
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

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('setup.store') }}" method="POST" class="bg-white rounded-lg shadow-lg" x-data="{ activeTab: 'admin' }">
                @csrf

                <!-- Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button type="button" @click="activeTab = 'admin'" :class="activeTab === 'admin' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-user-shield mr-2"></i>
                            Administrador
                        </button>
                        <button type="button" @click="activeTab = 'opnsense'" :class="activeTab === 'opnsense' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-network-wired mr-2"></i>
                            OPNsense
                        </button>
                        <button type="button" @click="activeTab = 'advanced'" :class="activeTab === 'advanced' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-sliders-h mr-2"></i>
                            Avançado
                        </button>
                    </nav>
                </div>

                <div class="p-8">
                    <!-- Tab: Administrador -->
                    <div x-show="activeTab === 'admin'" x-transition>
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Criar Usuário Administrador</h2>
                        <p class="text-gray-600 mb-6">Este usuário terá acesso total ao sistema</p>

                        <div class="space-y-4">
                            <div>
                                <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nome Completo *
                                </label>
                                <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email *
                                </label>
                                <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Senha *
                                </label>
                                <input type="password" id="admin_password" name="admin_password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <p class="mt-1 text-sm text-gray-500">Mínimo de 8 caracteres</p>
                            </div>

                            <div>
                                <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirmar Senha *
                                </label>
                                <input type="password" id="admin_password_confirmation" name="admin_password_confirmation"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Tab: OPNsense -->
                    <div x-show="activeTab === 'opnsense'" x-transition x-cloak>
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Configurar Conexão OPNsense</h2>
                        <p class="text-gray-600 mb-6">Informe os dados de acesso à API do seu firewall OPNsense</p>

                        <div class="space-y-4">
                            <div>
                                <label for="opnsense_url" class="block text-sm font-medium text-gray-700 mb-2">
                                    URL do OPNsense *
                                </label>
                                <input type="url" id="opnsense_url" name="opnsense_url" value="{{ old('opnsense_url') }}"
                                    placeholder="http://192.168.1.1"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <p class="mt-1 text-sm text-gray-500">Exemplo: http://192.168.1.1 ou https://firewall.local</p>
                            </div>

                            <div>
                                <label for="opnsense_api_key" class="block text-sm font-medium text-gray-700 mb-2">
                                    API Key *
                                </label>
                                <textarea id="opnsense_api_key" name="opnsense_api_key" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-sm">{{ old('opnsense_api_key') }}</textarea>
                            </div>

                            <div>
                                <label for="opnsense_api_secret" class="block text-sm font-medium text-gray-700 mb-2">
                                    API Secret *
                                </label>
                                <textarea id="opnsense_api_secret" name="opnsense_api_secret" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-sm">{{ old('opnsense_api_secret') }}</textarea>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex">
                                    <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                                    <div class="ml-3">
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
                        <p class="text-gray-600 mb-6">Configurações opcionais do sistema</p>

                        <div class="space-y-4">
                            <div>
                                <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nome da Aplicação
                                </label>
                                <input type="text" id="app_name" name="app_name" value="{{ old('app_name', 'OPNsense Web UI') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="app_url" class="block text-sm font-medium text-gray-700 mb-2">
                                    URL da Aplicação
                                </label>
                                <input type="url" id="app_url" name="app_url" value="{{ old('app_url', config('app.url')) }}"
                                    placeholder="http://localhost"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="db_port" class="block text-sm font-medium text-gray-700 mb-2">
                                    Porta do Banco de Dados
                                </label>
                                <input type="number" id="db_port" name="db_port" value="{{ old('db_port', env('DB_PORT', '3307')) }}"
                                    min="1" max="65535"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <p class="mt-1 text-sm text-gray-500">Porta padrão MySQL: 3306</p>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div class="flex">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mt-1"></i>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            Altere estas configurações apenas se necessário. Os valores padrão funcionam para a maioria dos casos.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 rounded-b-lg flex justify-between items-center">
                    <p class="text-sm text-gray-500">* Campos obrigatórios</p>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Concluir Configuração
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
