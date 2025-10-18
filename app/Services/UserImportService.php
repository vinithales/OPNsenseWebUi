<?php

namespace App\Services;

use App\Models\User;
use App\Services\Opnsense\UserService as OPNsenseUserService;
use App\Services\Opnsense\GroupService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserImportService
{
    protected $opnsenseUserService;
    protected $groupService;

    public function __construct(OPNsenseUserService $opnsenseUserService, GroupService $groupService)
    {
        $this->opnsenseUserService = $opnsenseUserService;
        $this->groupService = $groupService;
    }
    /**
     * Processar arquivo Excel e importar usuários
     *
     * @param string $filePath Caminho do arquivo Excel
     * @param string $userType Tipo de usuário (aluno/professor)
     * @return array Resultado da importação
     */
    public function importFromExcel(string $filePath, string $userType = User::TYPE_ALUNO): array
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            $imported = [];
            $errors = [];
            $credentials = [];

            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2; // +2 porque removemos header e index começa em 0

                // Validação básica
                if (empty($row[0]) && empty($row[1])) {
                    continue; // Pula linhas vazias
                }

                $ra = trim($row[0] ?? '');
                $email = trim($row[1] ?? '');

                // Validações
                if (empty($ra)) {
                    $errors[] = "Linha {$lineNumber}: RA não pode estar vazio";
                    continue;
                }

                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Linha {$lineNumber}: E-mail inválido ou vazio";
                    continue;
                }

                // Verifica duplicatas no OPNsense
                // Como o RA é usado como 'name' (username), verificamos se já existe
                try {
                    $existingUser = $this->opnsenseUserService->findUserByName($ra);
                    if ($existingUser) {
                        $errors[] = "Linha {$lineNumber}: RA {$ra} já existe no sistema (usuário: {$existingUser['name']})";
                        continue;
                    }
                } catch (\Exception $e) {
                    // Se der erro na busca, continua (usuário não existe)
                    Log::debug("Verificação de RA duplicado: " . $e->getMessage());
                }

                // Gera senha segura
                $password = $this->generateSecurePassword();

                try {
                    // Busca grupo apropriado com variações de nome
                    $groupId = '';
                    $group = $this->groupService->findGroupWithVariations($userType);

                    if ($group) {
                        $groupId = $group['gid'] ?? $group['uuid'] ?? '';
                        Log::info("Grupo encontrado para tipo '{$userType}'", [
                            'grupo_nome' => $group['name'] ?? 'N/A',
                            'grupo_id' => $groupId
                        ]);
                    } else {
                        Log::warning("Grupo não encontrado para tipo '{$userType}'. Usuário será criado sem grupo.");
                    }

                    // Prepara dados para OPNsense API
                    // Armazena RA e tipo no campo comment em formato estruturado
                    $comment = "RA: {$ra} | Tipo: {$userType} | Importado: " . now()->format('Y-m-d H:i:s');

                    $userData = [
                        'user' => [
                            'name' => $ra, // Usando RA como nome de usuário
                            'fullname' => $email, // Nome completo (pode ser melhorado)
                            'email' => $email,
                            'password' => $password,
                            'group_memberships' => $groupId, // Vincula ao grupo encontrado
                            'comment' => $comment,
                            'expires' => '',
                            'user.shell' => '/sbin/nologin',
                            'authorizedkeys' => ''
                        ]
                    ];

                    // Cria usuário no OPNsense
                    $created = $this->opnsenseUserService->createUser($userData);

                    if ($created) {
                        $imported[] = [
                            'ra' => $ra,
                            'email' => $email,
                            'user_type' => $userType,
                        ];

                        // Armazena credenciais para PDF (LGPD: temporário, não persistido)
                        $credentials[] = [
                            'ra' => $ra,
                            'email' => $email,
                            'password' => $password, // Senha em texto simples apenas para PDF
                            'user_type' => $userType,
                        ];

                        Log::info("Usuário importado com sucesso no OPNsense", [
                            'ra' => $ra,
                            'email' => $email,
                            'user_type' => $userType
                        ]);
                    } else {
                        $errors[] = "Linha {$lineNumber}: Erro ao criar usuário no OPNsense";
                        Log::error("Falha ao criar usuário no OPNsense", [
                            'ra' => $ra,
                            'email' => $email
                        ]);
                    }
                } catch (\Exception $e) {
                    $errors[] = "Linha {$lineNumber}: Erro ao criar usuário - " . $e->getMessage();
                    Log::error("Erro ao importar usuário", [
                        'ra' => $ra,
                        'email' => $email,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return [
                'success' => count($imported) > 0,
                'imported' => $imported,
                'credentials' => $credentials, // Para geração de PDF
                'errors' => $errors,
                'total_processed' => count($rows),
                'total_imported' => count($imported),
                'total_errors' => count($errors),
            ];

        } catch (\Exception $e) {
            Log::error("Erro ao processar arquivo Excel", ['error' => $e->getMessage()]);
            throw new \Exception("Erro ao processar arquivo: " . $e->getMessage());
        }
    }

    /**
     * Gera senha segura de 10 caracteres
     * Contém letras maiúsculas, minúsculas, números e caracteres especiais
     *
     * @return string
     */
    public function generateSecurePassword(int $length = 10): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%&*';

        // Garante pelo menos um caractere de cada tipo
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        // Completa o resto com caracteres aleatórios
        $allCharacters = $uppercase . $lowercase . $numbers . $special;
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $allCharacters[random_int(0, strlen($allCharacters) - 1)];
        }

        // Embaralha a senha
        return str_shuffle($password);
    }

    /**
     * Cria template Excel para download
     *
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function createTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeçalhos
        $sheet->setCellValue('A1', 'RA');
        $sheet->setCellValue('B1', 'E-mail');

        // Estilização do cabeçalho
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
        ];
        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        // Ajusta largura das colunas
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);

        // Exemplos (linhas 2 e 3)
        $sheet->setCellValue('A2', '123456');
        $sheet->setCellValue('B2', 'aluno@exemplo.com');
        $sheet->setCellValue('A3', '789012');
        $sheet->setCellValue('B3', 'professor@exemplo.com');

        return $spreadsheet;
    }
}
