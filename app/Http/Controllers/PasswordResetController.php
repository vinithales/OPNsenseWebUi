<?php

namespace App\Http\Controllers;

use App\Services\Opnsense\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Exibe o formulário de redefinição de senha
     */
    public function showForm()
    {
        return view('auth.password-reset');
    }

    /**
     * Processa a redefinição de senha
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ra' => 'required|string|max:20',
                'reset_code' => 'required|string|min:7|max:7',
                'new_password' => 'required|string|min:8|confirmed',
            ], [
                'ra.required' => 'O RA é obrigatório',
                'reset_code.required' => 'O código de redefinição é obrigatório',
                'reset_code.min' => 'O código deve ter exatamente 7 caracteres',
                'reset_code.max' => 'O código deve ter exatamente 7 caracteres',
                'new_password.required' => 'A nova senha é obrigatória',
                'new_password.min' => 'A senha deve ter pelo menos 8 caracteres',
                'new_password.confirmed' => 'A confirmação da senha não confere',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $ra = $request->input('ra');
            $resetCode = $request->input('reset_code');
            $newPassword = $request->input('new_password');

            // Busca usuário pelo RA (name no OPNsense)
            $user = $this->userService->findUserByName($ra);

            if (!$user) {
                return back()->withErrors(['ra' => 'RA não encontrado no sistema'])->withInput();
            }

            // Valida código de redefinição
            if (!$this->validateResetCode($user, $resetCode)) {
                return back()->withErrors(['reset_code' => 'Código de redefinição inválido'])->withInput();
            }

            // Atualiza senha no OPNsense
            $updateData = [
                'user' => [
                    'password' => $newPassword,
                    'scrambled_password' => '0', // Senha não criptografada
                ]
            ];

            $updated = $this->userService->updateUser($user['uuid'], $updateData);

            if ($updated) {
                Log::info("Senha redefinida com sucesso", [
                    'ra' => $ra,
                    'user_uuid' => $user['uuid']
                ]);

                return redirect()->route('login')
                    ->with('success', 'Senha redefinida com sucesso! Você pode fazer login com a nova senha.');
            } else {
                return back()->withErrors(['general' => 'Erro ao atualizar a senha. Tente novamente.']);
            }

        } catch (\Exception $e) {
            Log::error('Erro na redefinição de senha: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Erro interno do sistema. Tente novamente.']);
        }
    }

    /**
     * Valida o código de redefinição
     * O código é formado por: 4 últimos dígitos do RA + 3 últimos caracteres do último nome
     */
    private function validateResetCode($user, $inputCode): bool
    {
        try {
            // Extrai informações do comment
            $comment = $user['comment'] ?? '';
            
            // Parse do comment para extrair RA e nome completo
            if (preg_match('/RA:\s*(\w+)/', $comment, $raMatch)) {
                $ra = $raMatch[1];
            } else {
                $ra = $user['name']; // Fallback para o name do usuário
            }

            // Busca o nome completo no fullname ou no comment
            $fullname = $user['fullname'] ?? '';
            
            // Se não tiver fullname, tenta extrair do comment
            if (empty($fullname) && preg_match('/Nome:\s*(.+?)\s*\|/', $comment, $nameMatch)) {
                $fullname = trim($nameMatch[1]);
            }

            if (empty($fullname)) {
                Log::warning("Nome completo não encontrado para validação do código", [
                    'user_uuid' => $user['uuid'],
                    'ra' => $ra
                ]);
                return false;
            }

            // Gera código esperado
            $expectedCode = $this->generateResetCode($ra, $fullname);
            
            Log::debug("Validação do código de redefinição", [
                'ra' => $ra,
                'fullname' => $fullname,
                'expected_code' => $expectedCode,
                'input_code' => $inputCode,
                'match' => $inputCode === $expectedCode
            ]);

            return $inputCode === $expectedCode;

        } catch (\Exception $e) {
            Log::error('Erro ao validar código de redefinição: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gera código de redefinição baseado no RA e nome
     * 4 últimos dígitos do RA + 3 últimos caracteres do último nome
     */
    private function generateResetCode(string $ra, string $fullname): string
    {
        // 4 últimos dígitos do RA
        $raDigits = preg_replace('/\D/', '', $ra); // Remove não-dígitos
        $lastFourRA = substr($raDigits, -4);
        $lastFourRA = str_pad($lastFourRA, 4, '0', STR_PAD_LEFT); // Preenche com zeros se necessário

        // 3 últimos caracteres do último nome
        $names = explode(' ', trim($fullname));
        $lastName = end($names);
        
        // Se o último nome tem menos de 3 caracteres, usa o primeiro nome
        if (strlen($lastName) < 3) {
            $lastName = $names[0];
        }
        
        $lastThreeLetters = strtolower(substr($lastName, -3));
        $lastThreeLetters = str_pad($lastThreeLetters, 3, substr($names[0], -3), STR_PAD_LEFT);

        return $lastFourRA . $lastThreeLetters;
    }
}