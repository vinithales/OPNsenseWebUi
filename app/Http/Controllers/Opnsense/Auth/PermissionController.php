<?php

namespace App\Http\Controllers\Opnsense\Auth;

use App\Services\Opnsense\PermissionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        try {
            $privileges = $this->permissionService->getAvailablePrivileges();

            if (request()->wantsJson()) {
                return response()->json(['status' => 'success', 'data' => $privileges]);
            }

            return view('permissions.index', compact('privileges'));
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function getAvailablePrivileges()
    {
        try {
            $privileges = $this->permissionService->getAvailablePrivileges();

            if (request()->wantsJson()) {
                return response()->json(['status' => 'success', 'data' => $privileges]);
            }

            return $privileges;
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            throw $e;
        }
    }

    public function fetchPrivileges()
    {
        try {
            $privileges = $this->permissionService->fetchPrivileges();

            if (request()->wantsJson()) {
                return response()->json(['status' => 'success', 'data' => $privileges]);
            }

            return $privileges;
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            throw $e;
        }
    }

    public function assignPrivilegesToGroup(Request $request, string $groupId)
    {
        try {
            $validated = $request->validate([
                'privileges' => 'required|array',
                'privileges.*' => 'string'
            ]);

            $result = $this->permissionService->assignPrivilegesToGroup($groupId, $validated['privileges']);

            if (request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Privileges assigned successfully']);
            }

            return redirect()->route('groups.edit', $groupId)->with('success', 'Privileges assigned successfully');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function getGroupPrivileges(string $groupId)
    {
        try {
            $privileges = $this->permissionService->getGroupPrivileges($groupId);

            if (request()->wantsJson()) {
                return response()->json(['status' => 'success', 'data' => $privileges]);
            }

            return $privileges;
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            throw $e;
        }
    }

    public function clearPrivilegesCache()
    {
        try {
            $this->permissionService->clearPrivilegesCache();

            if (request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Privileges cache cleared successfully']);
            }

            return redirect()->route('permissions.index')->with('success', 'Privileges cache cleared successfully');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
