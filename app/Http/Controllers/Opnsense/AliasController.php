<?php

namespace App\Http\Controllers\Opnsense;

use App\Http\Controllers\Controller;
use App\Services\Opnsense\AliasService;
use Illuminate\Http\Request;

class AliasController extends Controller
{
    // View: Editar Alias
    public function edit(string $uuid)
    {
        $result = $this->aliasService->getAlias($uuid);
        if (!$result || !isset($result['alias'])) {
            abort(404, 'Alias não encontrado');
        }
        // Flatten: if the API returns ['alias' => [...]], but inside 'alias' there are nested arrays (e.g., 'type', 'content'),
        // we only want the direct fields for editing, not the full type/content/other option arrays.
        $alias = $result['alias'];
        $alias['uuid'] = $uuid;
        // Remove keys that are not direct alias fields (e.g., type options, proto options, etc.)
        foreach (['type', 'proto', 'interface', 'content'] as $key) {
            if (isset($alias[$key]) && is_array($alias[$key]) && array_keys($alias[$key]) !== range(0, count($alias[$key]) - 1)) {
                // If associative, try to extract the selected value
                $alias[$key] = $this->extractSelectedValue($alias[$key]);
            }
        }
        return view('aliases.edit', ['alias' => $alias]);
    }

    /**
     * Helper to extract the selected value from an associative array of options (OPNsense API style)
     */
    private function extractSelectedValue($optionArray)
    {
        // OPNsense returns e.g. [ 'host' => [...], 'network' => [...], ... ]
        // The selected value is usually the one with a 'selected' key or the only one with a value
        foreach ($optionArray as $key => $value) {
            if (is_array($value) && (isset($value['selected']) && $value['selected'])) {
                return $key;
            }
        }
        // Fallback: if only one key, return it
        if (count($optionArray) === 1) {
            return array_key_first($optionArray);
        }
        // Fallback: return empty string
        return '';
    }
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

            // Garantir todos os campos obrigatórios para o OPNsense
            $aliasData = [
                'name' => $validated['name'] ?? '',
                'type' => $validated['type'] ?? '',
                'content' => $validated['content'] ?? '',
                'description' => $validated['description'] ?? '',
                'enabled' => isset($validated['enabled']) ? ($validated['enabled'] ? '1' : '0') : '1',
                // Campos opcionais do OPNsense
                'proto' => '',
                'categories' => '',
                'updatefreq' => '',
                'interface' => '',
                'authtype' => '',
                'password' => '',
                'username' => '',
                'authgroup_content' => '',
                'network_content' => '',
                'path_expression' => '',
                'counters' => '',
            ];

            $result = $this->aliasService->updateAlias($uuid, $aliasData);
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
