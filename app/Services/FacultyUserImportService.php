<?php

namespace App\Services;

use App\Services\Opnsense\UserService as OPNsenseUserService;
use App\Services\Opnsense\GroupService;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FacultyUserImportService
{
    protected $opnsenseUserService;
    protected $groupService;

    public function __construct(OPNsenseUserService $opnsenseUserService, GroupService $groupService)
    {
        $this->opnsenseUserService = $opnsenseUserService;
        $this->groupService = $groupService;
    }

    /**
     * Processar arquivo Excel e importar usuários seguindo o padrão da faculdade
     * Colunas: RA_Matricula, Nome, Grupo, Login, Senha
     *
     * @param string $filePath Caminho do arquivo Excel
     * @param bool $createMissingGroups Se true, cria grupos que não existem
     * @return array Resultado da importação
     */
    public function importFromExcel(string $filePath, bool $createMissingGroups = false): array
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
            $missingGroups = [];

            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2; // +2 porque removemos header e index começa em 0

                // Validação básica - pula linhas vazias
                if (empty($row[0]) && empty($row[1]) && empty($row[2])) {
                    continue;
                }

                $ra = trim($row[0] ?? '');
                $nome = trim($row[1] ?? '');
                $grupo = trim($row[2] ?? '');
                $login = trim($row[3] ?? '');
                $senhaColuna = trim($row[4] ?? ''); // Coluna "Senha" do Excel

                // Validações
                if (empty($ra)) {
                    $errors[] = "Linha {$lineNumber}: RA/Matrícula não pode estar vazio";
                    continue;
                }

                if (empty($nome)) {
                    $errors[] = "Linha {$lineNumber}: Nome não pode estar vazio";
                    continue;
                }

                if (empty($grupo)) {
                    $errors[] = "Linha {$lineNumber}: Grupo não pode estar vazio";
                    continue;
                }

                // Gera login automático se não fornecido
                if (empty($login)) {
                    $login = $this->generateLogin($ra, $nome);
                }

                // Gera senha baseada no RA (usa a senha padrão gerada)
                $senha = $this->generatePasswordFromRA($ra);

                // Verifica duplicatas no OPNsense (por login)
                try {
                    $existingUser = $this->opnsenseUserService->findUserByName($login);
                    if ($existingUser) {
                        $errors[] = "Linha {$lineNumber}: Login '{$login}' já existe no sistema";
                        continue;
                    }
                } catch (\Exception $e) {
                    Log::debug("Verificação de login duplicado: " . $e->getMessage());
                }

                try {
                    // Busca grupo no OPNsense
                    $groupId = '';
                    $group = $this->groupService->findGroupWithVariations($grupo);

                    if ($group) {
                        $groupId = $group['gid'] ?? $group['uuid'] ?? '';
                        Log::info("Grupo encontrado para '{$grupo}'", ['grupo_id' => $groupId]);
                    } else {
                        // Se não deve criar grupos automaticamente, adiciona ao array de grupos ausentes
                        if (!$createMissingGroups) {
                            if (!in_array($grupo, $missingGroups)) {
                                $missingGroups[] = $grupo;
                            }
                            $errors[] = "Linha {$lineNumber}: Grupo '{$grupo}' não encontrado no sistema";
                            Log::warning("Grupo '{$grupo}' não encontrado");
                            continue;
                        }

                        // Cria o grupo automaticamente
                        Log::info("Criando grupo '{$grupo}' automaticamente");
                        $newGroup = $this->groupService->createGroup([
                            'group' => [
                                'name' => $grupo,
                                'description' => "Grupo criado automaticamente durante importação"
                            ]
                        ]);

                        if ($newGroup) {
                            // Busca o grupo recém-criado
                            $group = $this->groupService->findGroupWithVariations($grupo);
                            if ($group) {
                                $groupId = $group['gid'] ?? $group['uuid'] ?? '';
                                Log::info("Grupo '{$grupo}' criado com sucesso", ['grupo_id' => $groupId]);
                            } else {
                                $errors[] = "Linha {$lineNumber}: Falha ao criar grupo '{$grupo}'";
                                continue;
                            }
                        } else {
                            $errors[] = "Linha {$lineNumber}: Falha ao criar grupo '{$grupo}'";
                            continue;
                        }
                    }

                    // Gera código de redefinição
                    $resetCode = $this->generateResetCode($ra, $nome);

                    // Prepara dados para OPNsense API
                    // ALTERAÇÃO: usa descr (full-name) ao invés de fullname
                    $comment = "RA: {$ra} | Grupo: {$grupo} | Nome: {$nome} | Código: {$resetCode} | Importado: " . now()->format('Y-m-d H:i:s');

                    $userData = [
                        'user' => [
                            'name' => $login,
                            'password' => $senha,
                            'descr' => $nome,  // Usa descr ao invés de fullname
                            'comment' => $comment,
                            'group_memberships' => $groupId,  // Usa group_memberships ao invés de grouplist
                            'disabled' => '0',
                            'scrambled_password' => '0'
                        ]
                    ];

                    // Cria usuário no OPNsense
                    $created = $this->opnsenseUserService->createUser($userData);

                    if ($created) {
                        $imported[] = [
                            'ra' => $ra,
                            'nome' => $nome,
                            'grupo' => $grupo,
                            'login' => $login
                        ];

                        // Armazena credenciais para PDF
                        $credentials[] = [
                            'ra' => $ra,
                            'nome' => $nome,
                            'grupo' => $grupo,
                            'login' => $login,
                            'senha' => $senha,
                            'reset_code' => $resetCode
                        ];

                        Log::info("Usuário importado com sucesso", [
                            'ra' => $ra,
                            'login' => $login,
                            'grupo' => $grupo
                        ]);
                    } else {
                        $errors[] = "Linha {$lineNumber}: Falha ao criar usuário '{$login}' no OPNsense";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Linha {$lineNumber}: Erro ao criar usuário - " . $e->getMessage();
                    Log::error("Erro ao importar usuário", [
                        'ra' => $ra,
                        'login' => $login,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return [
                'success' => count($imported) > 0,
                'imported' => $imported,
                'credentials' => $credentials,
                'errors' => $errors,
                'missing_groups' => array_values(array_unique($missingGroups)),
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
     * Gera login automático baseado no RA
     * Formato: ad + RA (ex: ad44707)
     *
     * @param string $ra
     * @param string $nome
     * @return string
     */
    private function generateLogin(string $ra, string $nome): string
    {
        // Remove caracteres não numéricos do RA
        $raLimpo = preg_replace('/\D/', '', $ra);

        // Formato: ad + RA
        return 'ad' . $raLimpo;
    }

    /**
     * Gera senha padrão "fatec"
     *
     * @param string $ra
     * @return string
     */
    private function generatePasswordFromRA(string $ra): string
    {
        return 'fatec';
    }

    /**
     * Gera código de redefinição baseado no RA e nome completo
     * 4 últimos dígitos do RA + 3 últimos caracteres do último nome
     *
     * @param string $ra
     * @param string $nome
     * @return string
     */
    private function generateResetCode(string $ra, string $nome): string
    {
        // 4 últimos dígitos do RA
        $raDigits = preg_replace('/\D/', '', $ra);
        $lastFourRA = substr($raDigits, -4);
        $lastFourRA = str_pad($lastFourRA, 4, '0', STR_PAD_LEFT);

        // 3 últimos caracteres do último nome
        $names = explode(' ', trim($nome));
        $lastName = end($names);

        if (strlen($lastName) < 3) {
            $lastName = $names[0];
        }

        $lastThreeLetters = strtolower(substr($lastName, -3));
        $lastThreeLetters = str_pad($lastThreeLetters, 3, substr($names[0], -3), STR_PAD_LEFT);

        return $lastFourRA . $lastThreeLetters;
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
        $sheet->setCellValue('A1', 'RA_Matricula');
        $sheet->setCellValue('B1', 'Nome');
        $sheet->setCellValue('C1', 'Grupo');
        $sheet->setCellValue('D1', 'Login');
        $sheet->setCellValue('E1', 'Senha');
        $sheet->setCellValue('F1', 'Importar');

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
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Ajusta largura das colunas
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);

        // Exemplos
        $sheet->setCellValue('A2', '44707');
        $sheet->setCellValue('B2', 'Helio Massamitsu Oshima');
        $sheet->setCellValue('C2', 'Fatec Administrativo');
        $sheet->setCellValue('D2', 'ad44707');
        $sheet->setCellValue('E2', 'fatec');
        $sheet->setCellValue('F2', 'S');

        $sheet->setCellValue('A3', '52750');
        $sheet->setCellValue('B3', 'Sueli Satiko Yamashita Ikeda');
        $sheet->setCellValue('C3', 'Fatec Administrativo');
        $sheet->setCellValue('D3', 'ad52750');
        $sheet->setCellValue('E3', 'fatec');
        $sheet->setCellValue('F3', 'S');

        $sheet->setCellValue('A4', '############');
        $sheet->setCellValue('B4', 'LUCAS CASSIANO GARCIA DE OLIVEIRA');
        $sheet->setCellValue('C4', 'Fatec Discentes ADS Manhã');
        $sheet->setCellValue('D4', 'di1570482011102');
        $sheet->setCellValue('E4', 'fatec');
        $sheet->setCellValue('F4', 'S');

        return $spreadsheet;
    }
}
