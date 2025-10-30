<?php

namespace App\Http\Controllers;

use App\Services\Opnsense\UserService;
use App\Services\Opnsense\GroupService;
use App\Services\Opnsense\AliasService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $userService;
    protected $groupService;
    protected $aliasService;

    public function __construct(
        UserService $userService,
        GroupService $groupService,
        AliasService $aliasService
    ) {
        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->aliasService = $aliasService;
    }

    /**
     * Exibe a página do dashboard
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * API para buscar estatísticas do dashboard
     */
    public function getStats(): JsonResponse
    {
        try {
            // Buscar usuários
            $users = $this->userService->getUsers();
            $totalUsers = count($users);

            // Buscar grupos
            $groups = $this->groupService->getGroups();
            $totalGroups = count($groups);

            // Buscar aliases
            $aliases = $this->aliasService->getAliases();
            $totalAliases = count($aliases);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_users' => $totalUsers,
                    'total_groups' => $totalGroups,
                    'total_aliases' => $totalAliases,
                    'system_status' => 'online',
                    'last_updated' => now()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar estatísticas do dashboard: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao carregar estatísticas',
                'data' => [
                    'total_users' => 0,
                    'total_groups' => 0,
                    'total_aliases' => 0,
                    'system_status' => 'error',
                    'last_updated' => now()->format('Y-m-d H:i:s')
                ]
            ], 500);
        }
    }
}
