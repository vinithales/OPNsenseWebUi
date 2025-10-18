<?php

namespace App\Http\Controllers\Opnsense\Auth;

use App\Services\Opnsense\UserService;
use App\Services\Opnsense\GroupService;
use App\Services\UserImportService;
use App\Services\UserCredentialsPdfService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends Controller
{
    protected $userService;
    protected $groupService;
    protected $importService;
    protected $pdfService;

    public function __construct(
        UserService $userService,
        GroupService $groupService,
        UserImportService $importService,
        UserCredentialsPdfService $pdfService
    ) {
        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->importService = $importService;
        $this->pdfService = $pdfService;
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

            // Enriquece usuários com metadados (RA e tipo) extraídos do comment
            $users = $this->userService->enrichUsersWithMetadata($users);

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
                'ra' => 'nullable|string|max:50',
                'user_type' => 'required|in:aluno,professor,admin',
                'comment' => 'nullable|string',
                'expires' => 'nullable|date',
                'user_shell' => 'nullable|string',
                'authorizedkeys' => 'nullable|string'
            ]);

            // Verifica se RA já existe (se fornecido)
            if (!empty($validated['ra'])) {
                $existingUser = $this->userService->findUserByName($validated['ra']);
                if ($existingUser) {
                    return back()
                        ->withErrors(['ra' => "RA {$validated['ra']} já está cadastrado no sistema."])
                        ->withInput();
                }
            }

            // Verifica se o nome de usuário já existe
            $existingUser = $this->userService->findUserByName($validated['name']);
            if ($existingUser) {
                return back()
                    ->withErrors(['name' => "Nome de usuário '{$validated['name']}' já existe."])
                    ->withInput();
            }

            $groupMemberships = implode(',', $validated['group']);

            // Prepara comment com RA e tipo de usuário
            $commentParts = [];
            if (!empty($validated['ra'])) {
                $commentParts[] = "RA: {$validated['ra']}";
            }
            $commentParts[] = "Tipo: {$validated['user_type']}";
            if (!empty($validated['comment'])) {
                $commentParts[] = $validated['comment'];
            }
            $commentParts[] = "Criado: " . now()->format('Y-m-d H:i:s');
            $comment = implode(' | ', $commentParts);

            $userData = [
                'user' => [
                    'name' => $validated['name'],
                    'fullname' => $validated['fullname'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'group_memberships' => $groupMemberships,
                    'comment' => $comment,
                    'expires' => $validated['expires'] ?? '',
                    'user.shell' => $validated['user_shell'] ?? '/sbin/nologin',
                    'authorizedkeys' => $validated['authorizedkeys'] ?? ''
                ]
            ];

            Log::debug('Payload enviado: ' . json_encode($userData));

            // Cria usuário no OPNsense
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
     * Download do template Excel para importação
     */
    public function downloadExcelTemplate()
    {
        try {
            $spreadsheet = $this->importService->createTemplate();
            $writer = new Xlsx($spreadsheet);

            $filename = 'template_importacao_usuarios_' . now()->format('Y-m-d') . '.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $filename);

            $writer->save($temp_file);

            return response()->download($temp_file, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar template Excel: ' . $e->getMessage());
            return back()->with('error', 'Erro ao gerar template: ' . $e->getMessage());
        }
    }

    /**
     * Processar importação via Excel
     */
    public function processExcelImport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'excel_file' => 'required|file|mimes:xlsx,xls|max:5120', // 5MB
                'user_type' => 'required|in:aluno,professor',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $file = $request->file('excel_file');
            $userType = $request->input('user_type');

            // Processa importação
            $result = $this->importService->importFromExcel($file->getRealPath(), $userType);

            if ($result['success']) {
                // Armazena credenciais na sessão para geração de PDF
                session(['import_credentials' => $result['credentials']]);

                $message = "{$result['total_imported']} usuários importados com sucesso!";

                if ($result['total_errors'] > 0) {
                    $message .= " {$result['total_errors']} erros encontrados.";
                }

                return redirect()->back()
                    ->with('success', $message)
                    ->with('import_errors', $result['errors'])
                    ->with('show_pdf_button', true);
            } else {
                return redirect()->back()
                    ->with('error', 'Nenhum usuário foi importado.')
                    ->with('import_errors', $result['errors']);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao processar importação Excel: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao processar arquivo: ' . $e->getMessage());
        }
    }

    /**
     * Gerar e baixar PDF com credenciais dos usuários importados
     */
    public function downloadCredentialsPdf()
    {
        try {
            $credentials = session('import_credentials', []);

            if (empty($credentials)) {
                return redirect()->back()
                    ->with('error', 'Nenhuma credencial disponível para gerar PDF.');
            }

            // Gera PDF
            $pdf = $this->pdfService->downloadCredentialsPdf($credentials);

            // Limpa credenciais da sessão (LGPD: não manter senhas em sessão)
            session()->forget('import_credentials');

            return $pdf;

        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF de credenciais: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Gera e faz download do template CSV (método legado mantido)
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
