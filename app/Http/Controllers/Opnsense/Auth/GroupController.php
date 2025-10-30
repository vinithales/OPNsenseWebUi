<?php

namespace App\Http\Controllers\Opnsense\Auth;

use App\Services\Opnsense\GroupService;
use App\Services\Opnsense\UserService;
use App\Services\Opnsense\PermissionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GroupUsersExport;

class GroupController extends Controller
{
    protected $groupService;
    protected $permissionService;
    protected $userService;

    public function __construct(
        GroupService $groupService,
        PermissionService $permissionService,
        UserService $userService
    ) {
        $this->groupService = $groupService;
        $this->permissionService = $permissionService;
        $this->userService = $userService;
    }

    public function index()
    {
        try {
            $groups = $this->groupService->getGroups();
            return response()->json([
                'status' => 'success',
                'data' => $groups
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function indexView()
    {
        return view('group.index');
    }

    public function createView()
    {
        return view('group.create');
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'priv' => 'nullable|array',
                'priv.*' => 'string',
                'members' => 'nullable|array',
                'members.*' => 'string'
            ]);

            $groupData = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
            ];

            if (!empty($validated['priv'])) {
                $groupData['priv'] = implode(',', $validated['priv']);
            }

            if (!empty($validated['members'])) {
                $groupData['member'] = implode(',', $validated['members']);
            }
            if ($this->groupService->createGroup(['group' => $groupData])) {
                return redirect()->route('groups.index')
                    ->with('success', 'Grupo criado com sucesso!');
            }

            return back()->with('error', 'Falha na criação do grupo')->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }


    public function edit(string $id)
    {
        try {
            $group = $this->groupService->getGroup($id);
            if (!$group) {
                return redirect()->route('groups.index')->with('error', 'Grupo não encontrado');
            }

            $group['uuid'] = $id;

            return view('group.show', compact('group'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'description' => 'nullable|string',
                'priv' => 'nullable|array',
                'priv.*' => 'string',
                'members' => 'nullable|array',
                'members.*' => 'string'
            ]);

            $data = [
                'group' => [
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? '',
                    'scope' => 'user'
                ]
            ];

            if (isset($validated['priv'])) {
                $data['group']['priv'] = implode(',', $validated['priv']);
            }

            if (isset($validated['members'])) {
                $data['group']['member'] = implode(',', $validated['members']);
            }
            Log::debug('Payload enviado: ' . json_encode($data));


            if ($this->groupService->updateGroup($id, $data)) {
                return redirect()->route('groups.index')
                    ->with('success', 'Grupo atualizado com sucesso!');
            }


            return redirect()->route('groups.index')->with('error', 'Falha na atualização do grupo');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $result = $this->groupService->deleteGroup($id);

            if ($result === true || $result === 'true') {
                return response()->json(['status' => 'success', 'message' => 'Grupo excluído com sucesso']);
            }

            return response()->json(['status' => 'error', 'message' => 'Falha na exclusão do grupo'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Exporta usuários de um grupo específico para Excel
     */
    public function exportUsers(string $groupId)
    {
        try {
            // Busca o grupo
            $group = $this->groupService->getGroup($groupId);

            if (!$group) {
                return back()->with('error', 'Grupo não encontrado');
            }

            Log::debug('Grupo encontrado:', ['group' => $group, 'groupId' => $groupId]);

            // Busca todos os usuários
            $allUsers = $this->userService->getUsers();

            Log::debug('Total de usuários encontrados: ' . count($allUsers));

            // Log dos primeiros 2 usuários para ver a estrutura
            if (count($allUsers) > 0) {
                Log::debug('Exemplo de usuário 1:', ['user' => $allUsers[0]]);
                if (count($allUsers) > 1) {
                    Log::debug('Exemplo de usuário 2:', ['user' => $allUsers[1]]);
                }
            }

            // Filtra usuários que pertencem ao grupo
            $groupUsers = array_filter($allUsers, function($user) use ($groupId, $group) {
                // Log para debug
                Log::debug('Verificando usuário:', [
                    'username' => $user['name'] ?? 'N/A',
                    'group_memberships' => $user['group_memberships'] ?? 'não definido',
                    'buscando_grupo' => $group['name'] ?? 'N/A',
                    'groupId' => $groupId
                ]);

                // Verifica group_memberships (campo correto do OPNsense)
                if (isset($user['group_memberships']) && !empty($user['group_memberships'])) {
                    $memberships = is_string($user['group_memberships'])
                        ? array_map('trim', explode(',', $user['group_memberships']))
                        : $user['group_memberships'];

                    // Verifica por UUID do grupo
                    if (in_array($groupId, $memberships)) {
                        Log::debug('✓ Usuário ' . ($user['name'] ?? 'N/A') . ' encontrado por UUID');
                        return true;
                    }

                    // Verifica por GID do grupo
                    if (isset($group['gid']) && in_array($group['gid'], $memberships)) {
                        Log::debug('✓ Usuário ' . ($user['name'] ?? 'N/A') . ' encontrado por GID');
                        return true;
                    }

                    // Verifica por nome do grupo (case-insensitive)
                    foreach ($memberships as $membership) {
                        if (strcasecmp($membership, $group['name']) === 0) {
                            Log::debug('✓ Usuário ' . ($user['name'] ?? 'N/A') . ' encontrado por nome do grupo');
                            return true;
                        }
                    }
                }

                Log::debug('✗ Usuário ' . ($user['name'] ?? 'N/A') . ' NÃO pertence ao grupo');
                return false;
            });

            Log::debug('Usuários filtrados do grupo: ' . count($groupUsers));

            // Enriquece com metadados do campo comment
            $groupUsers = $this->userService->enrichUsersWithMetadata($groupUsers);

            // Se não encontrou usuários, retorna mensagem específica
            if (empty($groupUsers)) {
                Log::warning('Nenhum usuário encontrado no grupo: ' . $group['name']);
                return back()->with('error', 'Nenhum usuário encontrado no grupo "' . $group['name'] . '"');
            }

            // Prepara dados para exportação
            $exportData = array_map(function($user) use ($group) {
                return [
                    'RA' => $user['ra'] ?? 'N/A',
                    'Nome de Usuário' => $user['name'] ?? '',
                    'Nome Completo' => $user['fullname'] ?? '',
                    'Email' => $user['email'] ?? '',
                    'Tipo' => $user['user_type'] ?? 'N/A',
                    'Grupo' => $group['name'],
                    'Status' => !empty($user['disabled']) ? 'Inativo' : 'Ativo',
                ];
            }, $groupUsers);

            // Nome do arquivo com o nome do grupo e data
            $filename = 'usuarios_grupo_' . str_replace(' ', '_', strtolower($group['name'])) . '_' . date('Y-m-d_His') . '.xlsx';

            return Excel::download(new GroupUsersExport($exportData, $group['name']), $filename);

        } catch (\Exception $e) {
            Log::error('Erro ao exportar usuários do grupo: ' . $e->getMessage());
            return back()->with('error', 'Erro ao exportar usuários: ' . $e->getMessage());
        }
    }
}
