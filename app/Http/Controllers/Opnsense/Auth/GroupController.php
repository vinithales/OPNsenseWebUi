<?php

namespace App\Http\Controllers\Opnsense\Auth;

use App\Services\Opnsense\GroupService;
use App\Services\Opnsense\PermissionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GroupController extends Controller
{
    protected $groupService;
    protected $permissionService;

    public function __construct(GroupService $groupService, PermissionService $permissionService)
    {
        $this->groupService = $groupService;
        $this->permissionService = $permissionService;
    }

    public function apiIndex()
    {
        try {
            $groups = $this->groupService->getGroups();
            return response()->json([
                'status' => 'success',
                'rows' => $groups
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

    public function create()
    {
        $privileges = $this->permissionService->getAvailablePrivileges();
        return view('groups.create', compact('privileges'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'description' => 'nullable|string',
                'privileges' => 'nullable|array',
                'privileges.*' => 'string'
            ]);

            // Convert privileges array to comma-separated string
            if (isset($validated['privileges'])) {
                $validated['priv'] = implode(',', $validated['privileges']);
                unset($validated['privileges']);
            }

            $result = $this->groupService->createGroup($validated);

            if (request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Group created successfully'], 201);
            }

            return redirect()->route('groups.index')->with('success', 'Group created successfully');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
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
            $privileges = $this->permissionService->getAvailablePrivileges();
            return view('groups.edit', compact('group', 'privileges'));
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
                'privileges' => 'nullable|array',
                'privileges.*' => 'string'
            ]);

            // Convert privileges array to comma-separated string
            if (isset($validated['privileges'])) {
                $validated['priv'] = implode(',', $validated['privileges']);
                unset($validated['privileges']);
            }

            $result = $this->groupService->updateGroup($id, $validated);

            if (request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Group updated successfully']);
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

            if (request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Group deleted successfully']);
            }

            return redirect()->route('groups.index')->with('success', 'Group deleted successfully');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
