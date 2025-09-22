<?php

namespace App\Http\Controllers\Opnsense;

use App\Services\Opnsense\UserService;
use App\Services\Opnsense\GroupService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    protected $userService;
    protected $groupService;

    public function __construct(UserService $userService, GroupService $groupService)
    {
        $this->userService = $userService;
        $this->groupService = $groupService;
    }

    public function apiIndex()
    {
        try {
            $users = $this->userService->getUsers();
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

    public function indexView()
    {
        return view('users.index');
    }

    public function createView()
    {
        return view('users.create');
    }

    public function apiCreate(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'fullname' => 'required|string|max:255',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
                'group' => 'required|array',
                'group.*' => 'string',
                'comment' => 'nullable|string',
                'expires' => 'nullable|date',
                'user_shell' => 'nullable|string',
                'authorizedkeys' => 'nullable|string'
            ]);

             $groupMemberships = implode(',', $validated['group']);

            $userData = [
                'user' => [
                    'name' => $validated['name'],
                    'fullname' => $validated['fullname'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'group_memberships' => $groupMemberships,
                    'comment' => $validated['comment'] ?? '',
                    'expires' => $validated['expires'] ?? '',
                    'user.shell' => $validated['user_shell'] ?? '/sbin/nologin',
                    'authorizedkeys' => $validated['authorizedkeys'] ?? ''
                ]
            ];

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
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
                'email' => 'nullable|email',
                'fullname' => 'nullable|string',
                'groups' => 'nullable|array',
                'groups.*' => 'string'
            ]);

            $result = $this->userService->createUser($validated);

            if (request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'User created successfully'], 201);
            }

            return redirect()->route('users.index')->with('success', 'User created successfully');
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
            $user = $this->userService->getUser($id);
            if (!$user) {
                return redirect()->route('users.index')->with('error', 'User not found');
            }
            $groups = $this->groupService->getGroups();
            return view('users.edit', compact('user', 'groups'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string',
                'password' => 'nullable|string',
                'email' => 'nullable|email',
                'fullname' => 'nullable|string',
                'groups' => 'nullable|array',
                'groups.*' => 'string'
            ]);

            $result = $this->userService->updateUser($id, $validated);

            if (request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'User updated successfully']);
            }

            return redirect()->route('users.index')->with('success', 'User updated successfully');
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
            $result = $this->userService->deleteUser($id);

            if (request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'User deleted successfully']);
            }

            return redirect()->route('users.index')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
