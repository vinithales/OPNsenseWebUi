<?php

namespace App\Http\Controllers\Opnsense;

use App\Http\Controllers\Controller;
use App\Services\Opnsense\AliasService;
use Illuminate\Http\Request;

class AliasController extends Controller
{
    protected AliasService $aliasService;

    public function __construct(AliasService $aliasService)
    {
        $this->aliasService = $aliasService;
    }

    // View: Aliases
    public function index()
    {
        return view('aliases.index');
    }

    // API: Lista aliases
    public function list()
    {
        try {
            $response = $this->aliasService->getAliases();
            // Extrair apenas os dados dos aliases da resposta da API
            $aliases = $response['rows'] ?? [];
            return response()->json(['status' => 'success', 'data' => $aliases]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // API: Detalhar alias
    public function get(string $uuid)
    {
        try {
            $alias = $this->aliasService->getAlias($uuid);
            if (!$alias) {
                return response()->json(['status' => 'error', 'message' => 'Alias não encontrado'], 404);
            }
            return response()->json(['status' => 'success', 'data' => $alias]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // API: Criar alias
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'type' => 'required|string',
                'content' => 'required|string',
                'description' => 'nullable|string',
                'enabled' => 'boolean',
            ]);

            $result = $this->aliasService->createAlias($validated);
            if (!$result) {
                return response()->json(['status' => 'error', 'message' => 'Falha ao criar alias'], 400);
            }
            return response()->json(['status' => 'success', 'message' => 'Alias criado com sucesso', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // API: Atualizar alias
    public function update(Request $request, string $uuid)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string',
                'type' => 'nullable|string',
                'content' => 'nullable|string',
                'description' => 'nullable|string',
                'enabled' => 'nullable|boolean',
            ]);

            $result = $this->aliasService->updateAlias($uuid, $validated);
            if (!$result) {
                return response()->json(['status' => 'error', 'message' => 'Falha ao atualizar alias'], 400);
            }
            return response()->json(['status' => 'success', 'message' => 'Alias atualizado com sucesso', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // API: Deletar alias
    public function delete(string $uuid)
    {
        try {
            $result = $this->aliasService->deleteAlias($uuid);
            if (!$result) {
                return response()->json(['status' => 'error', 'message' => 'Falha ao deletar alias'], 400);
            }
            return response()->json(['status' => 'success', 'message' => 'Alias deletado com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // API: Aplicar mudanças (aliases)
    public function applyChanges()
    {
        try {
            $result = $this->aliasService->applyAliases();
            return response()->json(['status' => 'success', 'message' => 'Mudanças de aliases aplicadas', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
