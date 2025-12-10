@extends('layouts.header')

@section('main')
    <div class="p-8 bg-gray-100 min-h-screen">
        {{-- Cabeçalho da Página --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 text-blue-600">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h1 class="text-3xl font-bold text-gray-900">Importar Usuários em Massa</h1>
                    <p class="text-gray-600">Adicione, atualize ou remova múltiplos usuários de uma vez</p>
                </div>
            </div>
            <div>
                <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar para a Lista
                </a>
            </div>
        </div>

        {{-- Mensagens de Erro e Avisos no Topo --}}
        @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="mb-6 p-4 bg-yellow-50 border-2 border-yellow-400 rounded-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-grow">
                    <h4 class="text-lg font-semibold text-yellow-800 mb-2">Erros encontrados:</h4>
                    <ul class="list-disc list-inside text-yellow-700 text-sm space-y-1">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-2 border-green-400 rounded-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-2 border-red-400 rounded-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('warning'))
        <div class="mb-6 p-4 bg-yellow-50 border-2 border-yellow-400 rounded-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="space-y-8">
            {{-- Seção de Importação Padrão da Faculdade (PRINCIPAL) --}}
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg shadow-lg p-6 border-l-4 border-green-600">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-grow">
                        <div class="flex items-center gap-2 mb-2">
                            <h2 class="text-2xl font-bold text-gray-900">Importação Padrão da Faculdade</h2>
                            <span class="px-3 py-1 bg-green-600 text-white text-xs font-bold rounded-full">RECOMENDADO</span>
                        </div>
                        <p class="text-gray-700 mt-2">Formato padrão institucional com login e senha automáticos baseados no RA</p>

                        <div class="mt-4 bg-white rounded-lg p-4 border border-green-200">
                            <h3 class="font-semibold text-gray-900 mb-2">Formato do arquivo:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-1">
                                <li><strong>RA_Matricula</strong> - Número de matrícula do usuário</li>
                                <li><strong>Nome</strong> - Nome completo</li>
                                <li><strong>Grupo</strong> - Grupo de acesso (ex: Fatec Administrativo, Fatec Discentes ADS Manhã)</li>
                                <li><strong>Login</strong> - Login gerado automaticamente (formato: ad + RA ou di + código)</li>
                                <li><strong>Senha</strong> - Senha padrão gerada: <code class="bg-gray-200 px-2 py-1 rounded">fatec</code></li>
                                <li><strong>Importar</strong> - Marcar com "S" para importar</li>
                            </ul>
                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                                <p class="text-sm text-blue-800"><strong>Dica:</strong> O template já vem com exemplos. Login e senha são gerados automaticamente se não informados.</p>
                            </div>
                        </div>

                        <form action="{{ route('users.import.faculty.process') }}" method="POST" enctype="multipart/form-data" class="mt-6">
                            @csrf

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Arquivo Excel (.xlsx)</label>
                                <input type="file" name="excel_file" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-white focus:outline-none focus:ring-green-500 focus:border-green-500 p-2">
                            </div>

                            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-300 rounded-md">
                                <label class="flex items-start cursor-pointer">
                                    <input type="checkbox" name="update_existing_users" value="1" class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                    <span class="ml-2">
                                        <span class="block text-sm font-medium text-gray-900">Atualizar usuários existentes</span>
                                        <span class="block text-xs text-yellow-800 mt-1">⚠️ <strong>Atenção:</strong> Se marcado, usuários que já existirem no sistema terão suas informações <strong>atualizadas</strong> com os dados da planilha (nome, grupo, etc). A senha será preservada.</span>
                                    </span>
                                </label>
                            </div>

                            <div class="flex gap-3">
                                <a href="{{ route('users.import.faculty.template') }}" class="inline-flex items-center px-4 py-2 bg-white border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 hover:bg-green-50">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Baixar Template Padrão
                                </a>

                                <button type="submit" class="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Processar Importação
                                </button>
                            </div>
                        </form>

                        @if(session('show_faculty_pdf_button'))
                        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-green-800 mb-3 font-semibold">Importação concluída com sucesso!</p>
                            <p class="text-green-700 text-sm mb-3">Baixe o PDF com as credenciais para distribuir aos usuários:</p>
                            <a href="{{ route('users.import.faculty.credentials.pdf') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Baixar PDF com Credenciais
                            </a>
                        </div>
                        @endif

                        @if(session('show_create_groups_confirmation'))
                        <div class="mt-4 p-4 bg-yellow-50 border-2 border-yellow-400 rounded-md">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-grow">
                                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Grupos não encontrados no sistema</h3>
                                    <p class="text-yellow-700 mb-3">Os seguintes grupos não existem no sistema e precisam ser criados para continuar a importação:</p>
                                    <ul class="list-disc list-inside text-yellow-700 mb-4 space-y-1">
                                        @foreach(session('missing_groups', []) as $group)
                                            <li><strong>{{ $group }}</strong></li>
                                        @endforeach
                                    </ul>
                                    <p class="text-yellow-800 font-medium mb-4">Deseja criar esses grupos automaticamente e continuar com a importação?</p>
                                    <div class="flex gap-3">
                                        <form action="{{ route('users.import.faculty.reprocess') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Sim, Criar Grupos e Importar
                                            </button>
                                        </form>
                                        <a href="{{ route('users.import') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-400">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Cancelar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Divisor --}}
            <div class="border-t-2 border-gray-300 pt-8">
                <h2 class="text-lg font-semibold text-gray-600 mb-2">Outros Métodos de Importação</h2>
                <p class="text-sm text-gray-500">Métodos alternativos para casos específicos</p>
            </div>

            {{-- Seção de Importação via Excel (Por Grupo) --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-12 w-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-grow">
                        <h2 class="text-2xl font-bold text-gray-900">Importação por Grupo</h2>
                        <p class="text-gray-700 mt-2">Importe alunos, professores e funcionários em um grupo específico</p>

                        <div class="mt-4 bg-white rounded-lg p-4 border border-indigo-200">
                            <h3 class="font-semibold text-gray-900 mb-2">Como funciona:</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-1">
                                <li>Baixe o template Excel com as colunas: <strong>RA</strong> e <strong>Nome completo</strong></li>
                                <li>Preencha os dados dos usuários (um por linha)</li>
                                <li>Senhas seguras são geradas automaticamente</li>
                                <li>Após importar, baixe o PDF com as credenciais para entrega aos usuários</li>
                            </ul>
                        </div>

                        <form action="{{ route('users.import.excel.process') }}" method="POST" enctype="multipart/form-data" class="mt-6">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Usuário</label>
                                    <select name="user_type" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="aluno">Aluno</option>
                                        <option value="professor">Professor</option>
                                        <option value="funcionario">Funcionário</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Arquivo Excel</label>
                                    <input type="file" name="excel_file" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 p-2">
                                </div>
                            </div>

                            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-300 rounded-md">
                                <label class="flex items-start cursor-pointer">
                                    <input type="checkbox" name="update_existing_users" value="1" class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">
                                        <span class="block text-sm font-medium text-gray-900">Atualizar usuários existentes</span>
                                        <span class="block text-xs text-yellow-800 mt-1">⚠️ <strong>Atenção:</strong> Se marcado, usuários que já existirem no sistema (verificado pelo RA) terão suas informações <strong>atualizadas</strong> com os dados da planilha. A senha será preservada.</span>
                                    </span>
                                </label>
                            </div>

                            <div class="flex gap-3">
                                <a href="{{ route('users.import.excel.template') }}" class="inline-flex items-center px-4 py-2 bg-white border border-indigo-300 rounded-md shadow-sm text-sm font-medium text-indigo-700 hover:bg-indigo-50">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Baixar Template Excel
                                </a>

                                <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Processar Importação
                                </button>
                            </div>
                        </form>

                        @if(session('show_pdf_button'))
                        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-green-800 mb-3">Importação concluída! Baixe o PDF com as credenciais:</p>
                            <a href="{{ route('users.import.credentials.pdf') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Baixar PDF com Credenciais
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="border-t-2 border-gray-200 pt-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Importação via CSV (Método Legado)</h2>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <span
                            class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600 font-bold text-lg">1</span>
                    </div>
                    <div class="ml-4 flex-grow">
                        <h2 class="text-xl font-semibold text-gray-900">Instruções e Formato do Arquivo</h2>
                        <p class="text-gray-600 mt-1">Para garantir uma importação bem-sucedida, seu arquivo deve estar no
                            formato CSV e conter as seguintes colunas:</p>
                        <ul class="list-disc list-inside mt-3 text-gray-700 space-y-1">
                            <li><code class="bg-gray-200 px-1 rounded">name</code> (Obrigatório): Nome de usuário para
                                login.</li>
                            <li><code class="bg-gray-200 px-1 rounded">fullname</code> (Opcional): Nome completo do usuário.
                            </li>
                            <li><code class="bg-gray-200 px-1 rounded">email</code> (Opcional): Endereço de e-mail.</li>
                            <li><code class="bg-gray-200 px-1 rounded">password</code> (Obrigatório para novos usuários):
                                Senha do usuário.</li>
                            <li><code class="bg-gray-200 px-1 rounded">group</code> (Opcional): Grupo de acesso (ex: admins,
                                visualizadores).</li>
                            <li><code class="bg-gray-200 px-1 rounded">disabled</code> (Opcional): <code
                                    class="bg-gray-200 px-1 rounded">0</code> para ativo, <code
                                    class="bg-gray-200 px-1 rounded">1</code> para inativo.</li>
                        </ul>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <a href="{{ route('users.import.template') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Baixar Modelo CSV
                        </a>
                    </div>
                </div>
            </div>

            <form action="{{ route('users.import.process') }}" method="POST" enctype="multipart/form-data"
                x-data="{ action: 'add_update', file: null }"
                @submit="if(action === 'delete') { if(!confirm('ATENÇÃO!\n\nVocê tem certeza que deseja EXCLUIR permanentemente todos os usuários listados no arquivo? Esta ação não pode ser desfeita.')) event.preventDefault() }">
                @csrf

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <span
                                class="flex items-center justify-center h-10 w-10 rounded-full bg-green-100 text-green-600 font-bold text-lg">2</span>
                        </div>
                        <div class="ml-4 w-full">
                            <h2 class="text-xl font-semibold text-gray-900">Selecione a Ação</h2>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label @click="action = 'add_update'"
                                    :class="{ 'border-indigo-600 ring-2 ring-indigo-600': action === 'add_update', 'border-gray-300': action !== 'add_update' }"
                                    class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none transition-all">
                                    <input type="radio" name="action" value="add_update" class="sr-only"
                                        x-model="action">
                                    <div class="flex flex-1">
                                        <div class="flex flex-col">
                                            <span class="block text-sm font-medium text-indigo-900">Adicionar / Atualizar
                                                Usuários</span>
                                            <span class="mt-1 flex items-center text-sm text-gray-500">Cria novos usuários e
                                                atualiza os existentes com base no 'name'.</span>
                                        </div>
                                    </div>
                                    <svg :class="{ 'block': action === 'add_update', 'hidden': action !== 'add_update' }"
                                        class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </label>

                                <label @click="action = 'delete'"
                                    :class="{ 'border-red-600 ring-2 ring-red-600': action === 'delete', 'border-gray-300': action !== 'delete' }"
                                    class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none transition-all">
                                    <input type="radio" name="action" value="delete" class="sr-only" x-model="action">
                                    <div class="flex flex-1">
                                        <div class="flex flex-col">
                                            <span class="block text-sm font-medium text-red-900">Excluir Usuários</span>
                                            <span class="mt-1 flex items-center text-sm text-gray-500">Remove
                                                permanentemente os usuários listados no arquivo.</span>
                                        </div>
                                    </div>
                                    <svg :class="{ 'block': action === 'delete', 'hidden': action !== 'delete' }"
                                        class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 mt-8"
                    :class="{ 'border-indigo-500': action === 'add_update', 'border-red-500': action === 'delete' }">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <span class="flex items-center justify-center h-10 w-10 rounded-full font-bold text-lg"
                                :class="{ 'bg-indigo-100 text-indigo-600': action === 'add_update', 'bg-red-100 text-red-600': action === 'delete' }">3</span>
                        </div>
                        <div class="ml-4 w-full">
                            <h2 class="text-xl font-semibold text-gray-900">Upload do Arquivo CSV</h2>

                            <div x-show="action === 'delete'" x-cloak class="mt-4 rounded-md bg-red-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Ação Destrutiva Selecionada</h3>
                                        <p class="mt-2 text-sm text-red-700">A exclusão de usuários é permanente e não pode
                                            ser desfeita. Tenha certeza de que o arquivo contém apenas os usuários que você
                                            deseja remover.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="file-upload"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <div
                                        class="flex justify-center items-center w-full px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <span>Selecionar um arquivo</span>
                                                <input id="file-upload" name="file_upload" type="file"
                                                    class="sr-only" required @change="file = $event.target.files[0]">
                                            </div>
                                            <p class="text-xs text-gray-500">CSV até 10MB</p>
                                        </div>
                                    </div>
                                </label>
                                <div x-show="file" x-cloak class="mt-3 text-sm text-gray-700">
                                    <strong>Arquivo selecionado:</strong> <span x-text="file?.name"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white transition-colors"
                        :class="{ 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500': action === 'add_update', 'bg-red-600 hover:bg-red-700 focus:ring-red-500': action === 'delete' }"
                        :disabled="!file">
                        <svg class="w-5 h-5 mr-3 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="action === 'add_update'" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            <path x-show="action === 'delete'" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        <span x-text="action === 'add_update' ? 'Processar Importação' : 'Excluir Usuários'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- No final do formulário de importação --}}
    @if (session('import_results'))
        <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Resultado da Importação</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-50 p-4 rounded-md">
                    <p class="text-sm text-gray-500">Total Processado</p>
                    <p class="text-2xl font-semibold">{{ count(session('import_results')) }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-md">
                    <p class="text-sm text-green-600">Sucessos</p>
                    <p class="text-2xl font-semibold text-green-700">{{ session('success_count', 0) }}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-md">
                    <p class="text-sm text-red-600">Falhas</p>
                    <p class="text-2xl font-semibold text-red-700">{{ session('error_count', 0) }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Linha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usuário</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Resultado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach (session('import_results') as $result)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result['line'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $result['data']['name'] ?? ($result['data'][0] ?? 'Dado Inválido') }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm {{ $result['success'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $result['message'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection
