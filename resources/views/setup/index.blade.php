<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração Inicial - OPNsense Web UI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-800 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-3xl mx-auto px-4 py-8">
        <!-- Header alinhado com login -->
        <div class="flex flex-col items-center mb-8 text-center">
            <div class="bg-green-600 p-3 rounded-lg shadow-md mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5.002a12.052 12.052 0 00-1.884 5.922c.287 4.155 3.54 7.63 7.718 8.132a1.125 1.125 0 001.13 0c4.178-.502 7.43-3.977 7.718-8.132a12.052 12.052 0 00-1.884-5.922A11.954 11.954 0 0110 1.944zM9 12.121l-2.828-2.828a.75.75 0 111.06-1.062L9 10.001l4.768-4.768a.75.75 0 111.06 1.06L9.53 12.65a.75.75 0 01-1.06 0l-.53-.53z" clip-rule="evenodd" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">OPNsense Manager</h1>
            <p class="text-gray-400 mt-1">Configuração inicial do sistema</p>
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

            <form action="{{ route('setup.store') }}" method="POST" class="bg-white p-8 rounded-lg shadow-xl space-y-6" x-data="{ activeTab: 'admin' }">
                @csrf

                <!-- Título do Card -->
                <h2 class="text-2xl font-bold text-center text-gray-800">Configuração Inicial</h2>

                <!-- Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button type="button" @click="activeTab = 'admin'" :class="activeTab === 'admin' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-user-shield mr-2"></i>
                            Administrador
                        </button>
                        <button type="button" @click="activeTab = 'opnsense'" :class="activeTab === 'opnsense' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-network-wired mr-2"></i>
                            OPNsense
                        </button>
                        <button type="button" @click="activeTab = 'advanced'" :class="activeTab === 'advanced' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
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
                <div class="bg-gray-50 px-6 py-4 border border-gray-200 rounded-md flex justify-between items-center">
                    <p class="text-sm text-gray-500">* Campos obrigatórios</p>
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Concluir Configuração
                    </button>
                </div>
            </form>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
