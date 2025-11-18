<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class LoginController extends Controller
{
    // Exibe o formulário de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Processa o login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Autenticado com sucesso
            $request->session()->regenerate();

            Log::info('Usuário autenticado com sucesso, redirecionando para dashboard');

            // Tenta redirecionar para a URL pretendida ou para o dashboard
            return redirect()->intended(route('dashboard'));
        }

        // Falha na autenticação
        Log::warning('Tentativa de login falhada', ['email' => $credentials['email']]);

        return back()->withErrors([
            'email' => 'Credenciais inválidas.',
        ])->withInput();
    }

    // Realiza o logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
