<?php

namespace App\Http\Controllers\Opnsense\Auth;

use App\Services\Opnsense\GroupService;
use App\Services\Opnsense\PermissionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    protected $groupService;
    protected $permissionService;

    public function __construct(GroupService $groupService, PermissionService $permissionService)
    {
        $this->groupService = $groupService;
        $this->permissionService = $permissionService;
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

            return back()->with('error', 'Falha na criação do Grupo')->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }


    public function edit(string $id)
    {
        try {
            $group = $this->groupService->getGroup($id);
            if (!$group) {
                return redirect()->route('groups.index')->with('error', 'Group not found');
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
                // vírgula entre os privilégios
                $data['group']['priv'] = implode(',', $validated['priv']);
            }

            if (isset($validated['members'])) {
                // vírgula entre os IDs de membro
                $data['group']['member'] = implode(',', $validated['members']);
            }
            Log::debug('Payload enviado: ' . json_encode($data));


            if ($this->groupService->updateGroup($id, $data)) {
                return redirect()->route('groups.index')
                    ->with('success', 'Grupo criado com sucesso!');
            }


            return redirect()->route('groups.index')->with('success', 'Group updated successfully');
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
                return response()->json(['status' => 'success', 'message' => 'Group deleted successfully']);
            }

            return response()->json(['status' => 'error', 'message' => 'Falha na exclusão do usuário'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
