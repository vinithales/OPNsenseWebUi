<?php

namespace App\Http\Controllers\Opnsense\Auth;

use App\Services\Opnsense\UserService;
use App\Services\Opnsense\GroupService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $userService;
    protected $groupService;

    public function __construct(UserService $userService, GroupService $groupService)
    {
        $this->userService = $userService;
        $this->groupService = $groupService;
    }



    public function index()
    {
        return view('users.index');
    }

    public function create()
    {
        return view('users.create');
    }

    public function edit(string $uuid)
    {
        try {
            $user = $this->userService->getUser($uuid);
            $group = $this->groupService->getGroups();

            if (!$user) {
                return redirect()->route('users.index')->with('error', 'User not found');
            }

            $user['uuid'] = $uuid;


            return view('users.show', compact('user', 'group'));
        } catch (\Exception $e) {
            Log::debug('ERRO DO EDIT AQUI:' . $e->getMessage());
            return back()->with('error do edit', $e->getMessage());
        }
    }

    public function importView()
    {
        return view('users.import');
    }

    public function apiIndex()
    {
        try {
            $users = $this->userService->getUsers();
            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'fullname' => 'required|string|max:255',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
                'group' => 'required|array',
                'group.*' => 'string',
                'comment' => 'nullable|string',
                'expires' => 'nullable|date',
                'user_shell' => 'nullable|string',
                'authorizedkeys' => 'nullable|string'
            ]);

            $groupMemberships = implode(',', $validated['group']);

            $userData = [
                'user' => [
                    'name' => $validated['name'],
                    'fullname' => $validated['fullname'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'group_memberships' => $groupMemberships,
                    'comment' => $validated['comment'] ?? '',
                    'expires' => $validated['expires'] ?? '',
                    'user.shell' => $validated['user_shell'] ?? '/sbin/nologin',
                    'authorizedkeys' => $validated['authorizedkeys'] ?? ''
                ]
            ];
            Log::debug('Payload enviado: ' . json_encode($userData));

            if ($this->userService->createUser($userData)) {
                return redirect()->route('users.index')
                    ->with('success', 'Usuário criado com sucesso!');
            }

            return back()->with('error', 'Falha na criação do usuário');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $result = $this->userService->deleteUser($id);

            if ($result === true || $result === 'true') {
                return response()->json(['status' => 'success', 'message' => 'User deleted successfully']);
            }

            return response()->json(['status' => 'error', 'message' => 'Falha na exclusão do usuário'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, string $uuid)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'password' => 'nullable|string|confirmed|min:6',
                'email' => 'nullable|email',
                'descr' => 'nullable|string',
                'expires' => 'nullable|date',
                'authorizedkeys' => 'nullable|string',
                'disabled' => 'nullable|boolean',
                'shell' => 'nullable|string',
                'language' => 'nullable|string',
                'groups' => 'nullable|array',
                'groups.*' => 'string',
                'priv' => 'nullable|array',
                'priv.*' => 'string'
            ]);

            $userData = [
                'disabled' => $validated['disabled'] ?? '0',
                'name' => $validated['name'],
                'password' => $validated['password'] ?? '',
                'scrambled_password' => '0',
                'descr' => $validated['descr'] ?? '',
                'email' => $validated['email'] ?? '',
                'comment' => 'Usuário atualizado via API',
                'landing_page' => '',
                'language' => $validated['language'] ?? '',
                'shell' => $validated['shell'] ?? '',
                'expires' => $validated['expires'] ?? '',
                'user.group_memberships' => isset($validated['groups']) ? implode(',', $validated['groups']) : '',
                'priv' => isset($validated['priv']) ? implode(',', $validated['priv']) : '',
                'otp_uri' => '',
                'otp_seed' => '',
                'authorizedkeys' => $validated['authorizedkeys'] ?? ''
            ];


            $userData = array_filter($userData, function ($value) {
                return $value !== null && $value !== '';
            });

            if ($this->userService->updateUser($uuid, $userData)) {
                return redirect()->route('users.index')
                    ->with('success', 'Usuário atualizado com sucesso!');
            }

            return redirect()->route('users.index')->with('error', 'Erro ao atualizar usuário');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }

            return back()->with('error', $e->getMessage())->withInput();
        }
    }


    /**
     * Gera e faz download do template CSV
     */
    public function downloadTemplate()
    {
        $filename = "template-importacao-usuarios.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            fwrite($file, "\xEF\xBB\xBF");
            // Cabeçalhos do CSV
            fputcsv($file, ['name', 'fullname', 'email', 'password', 'group', 'disabled', 'comment', 'user_shell'], ";");

            // Linha de exemplo
            fputcsv($file, ['joao.silva', 'João da Silva', 'joao@empresa.com', 'senha123', 'admins', '0', 'Usuário de exemplo', '/bin/bash'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Processa o arquivo CSV de importação
     */
    public function processImport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:add_update,delete',
                'file_upload' => 'required|file|mimes:csv,txt|max:10240', // 10MB
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $action = $request->input('action');
            $file = $request->file('file_upload');

            $results = $this->processCsvFile($file, $action);

            $successCount = count(array_filter($results, fn($r) => $r['success']));
            $errorCount = count($results) - $successCount;

            return redirect()->back()
                ->with('import_results', $results)
                ->with('success_count', $successCount)
                ->with('error_count', $errorCount)
                ->with('action', $action);
        } catch (\Exception $e) {
            Log::error('Erro ao processar importação: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao processar arquivo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Processa o arquivo CSV linha por linha
     */
    private function processCsvFile($file, $action)
    {
        $results = [];
        $handle = fopen($file->getRealPath(), 'r');

        // Pular cabeçalho
        fgetcsv($handle, 0, ";");

        $lineNumber = 1;
        while (($row = fgetcsv($handle, 0, ";")) !== false) {
            $lineNumber++;

            // Verificar se a linha tem colunas suficientes
            if (count($row) < 5) {
                $results[] = [
                    'line' => $lineNumber,
                    'success' => false,
                    'message' => 'Linha com formato inválido (número insuficiente de colunas)',
                    'data' => $row
                ];
                continue;
            }

            // Mapear colunas
            $userData = [
                'name' => $row[0] ?? '',
                'fullname' => $row[1] ?? '',
                'email' => $row[2] ?? '',
                'password' => $row[3] ?? '',
                'group' => $row[4] ?? '',
                'disabled' => $row[5] ?? '0',
                'comment' => $row[6] ?? '',
                'user_shell' => $row[7] ?? '/sbin/nologin',
            ];

            try {
                if ($action === 'add_update') {
                    $result = $this->processAddUpdateUser($userData);
                } else {
                    $result = $this->processDeleteUser($userData);
                }

                $results[] = [
                    'line' => $lineNumber,
                    'success' => $result['success'],
                    'message' => $result['message'],
                    'data' => $userData
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'line' => $lineNumber,
                    'success' => false,
                    'message' => 'Erro: ' . $e->getMessage(),
                    'data' => $userData
                ];
            }
        }

        fclose($handle);
        return $results;
    }

    /**
     * Processa adição ou atualização de usuário
     */
    private function processAddUpdateUser($userData)
    {
        if (empty($userData['name']) || empty($userData['password'])) {
            return [
                'success' => false,
                'message' => 'Nome de usuário e senha são obrigatórios'
            ];
        }

        $existingUser = $this->userService->findUserByName($userData['name']);

        $groupMemberships = '';
        if (!empty($userData['group'])) {
            $group = $this->groupService->findGroupByName($userData['group']);
            if ($group) {
                $groupMemberships = $group['gid'];
            } else {
                $groupMemberships = $userData['group'];
            }
        }

        if ($existingUser) {
            $updateData = [
                'user' => [
                    'uid' => $existingUser['uid'],
                    'name' => $userData['name'],
                    'fullname' => $userData['fullname'],
                    'email' => $userData['email'],
                    'password' => $userData['password'],
                    'group_memberships' => $groupMemberships,
                    'disabled' => $userData['disabled'],
                    'comment' => $userData['comment'],
                    'user.shell' => $userData['user_shell'],
                ]
            ];

            $result = $this->userService->updateUser($existingUser['uid'], $updateData);
            $message = $result ? 'Usuário atualizado com sucesso' : 'Falha ao atualizar usuário';
        } else {
            $createData = [
                'user' => [
                    'name' => $userData['name'],
                    'fullname' => $userData['fullname'],
                    'email' => $userData['email'],
                    'password' => $userData['password'],
                    'group_memberships' => $groupMemberships,
                    'disabled' => $userData['disabled'],
                    'comment' => $userData['comment'],
                    'user.shell' => $userData['user_shell'],
                ]
            ];

            $result = $this->userService->createUser($createData);
            $message = $result ? 'Usuário criado com sucesso' : 'Falha ao criar usuário';
        }

        return [
            'success' => $result,
            'message' => $message
        ];
    }

    /**
     * Processa exclusão de usuário
     */
    private function processDeleteUser($userData)
    {
        if (empty($userData['name'])) {
            return [
                'success' => false,
                'message' => 'Nome de usuário é obrigatório para exclusão'
            ];
        }

        $existingUser = $this->userService->findUserByName($userData['name']);

        if (!$existingUser) {
            return [
                'success' => false,
                'message' => 'Usuário não encontrado'
            ];
        }

        // Excluir usuário
        $result = $this->userService->deleteUser($existingUser['uuid']);

        return [
            'success' => $result,
            'message' => $result ? 'Usuário excluído com sucesso' : 'Falha ao excluir usuário'
        ];
    }
}
