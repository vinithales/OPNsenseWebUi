<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPNsense Manager - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-800 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md mx-auto">
        <div class="flex flex-col items-center mb-8 text-center">
            <div class="bg-green-600 p-3 rounded-lg shadow-md mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5.002a12.052 12.052 0 00-1.884 5.922c.287 4.155 3.54 7.63 7.718 8.132a1.125 1.125 0 001.13 0c4.178-.502 7.43-3.977 7.718-8.132a12.052 12.052 0 00-1.884-5.922A11.954 11.954 0 0110 1.944zM9 12.121l-2.828-2.828a.75.75 0 111.06-1.062L9 10.001l4.768-4.768a.75.75 0 111.06 1.06L9.53 12.65a.75.75 0 01-1.06 0l-.53-.53z" clip-rule="evenodd" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">OPNsense Manager</h1>
            <p class="text-gray-400 mt-1">Sistema de Gerenciamento de Servidores de Rede</p>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-xl space-y-6">
            <h2 class="text-2xl font-bold text-center text-gray-800">Fazer Login</h2>

            <form method="POST" action="{{ url('/login') }}">
                @csrf

                <div>
                    <label for="email" class="text-sm font-medium text-gray-700">Nome de Usuário</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            placeholder="Digite seu usuário"
                            value="{{ old('email') }}"
                            required>
                    </div>
                </div>

                <div>
                    <label for="password" class="text-sm font-medium text-gray-700">Senha</label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            placeholder="Digite sua senha"
                            required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 mt-2">
                    Entrar
                </button>

                @if($errors->any())
                    <div class="text-center text-sm text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif
            </form>



            <div class="mt-4 pt-4 border-t text-center">
                <form method="POST" action="{{ route('system.reset') }}" onsubmit="return confirm('Isto vai redefinir o sistema para o estado inicial. Você precisará informar novamente as credenciais do OPNsense e recriar o usuário administrador. Deseja continuar?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 text-red-700 bg-white hover:bg-red-50 rounded-md text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Perdi o acesso — Redefinir sistema
                    </button>
                </form>
                <p class="mt-2 text-xs text-gray-500">A redefinição exige informar as credenciais do OPNsense na próxima etapa.</p>
            </div>
        </div>
    </div>

</body>
</html>
