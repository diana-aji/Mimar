<?php

namespace App\Http\Controllers\Web\Admin;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\Web\RoleWebService;
use App\Http\Requests\Web\Role\StoreRoleWebRequest;
use App\Http\Requests\Web\Role\UpdateRoleWebRequest;

class RoleAdminController extends Controller
{
    public function __construct(
        protected RoleWebService $service
    ) {
    }

    public function index(Request $request): View
    {
        $roles = Role::query()
            ->with('permissions')
            ->where('guard_name', 'web')
            ->latest()
            ->get();

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $selectedRole = null;

        if ($request->filled('selected')) {
            $selectedRole = $roles->firstWhere('id', (int) $request->query('selected'));
        }

        if (! $selectedRole) {
            $selectedRole = $roles->first();
        }

        return view('admin.roles.index', compact('roles', 'permissions', 'selectedRole'));
    }

    public function store(StoreRoleWebRequest $request): RedirectResponse
    {
        $role = $this->service->create($request->validated());

        return redirect()
            ->route('admin.roles.index', ['selected' => $role->id])
            ->with('success', __('messages.created_successfully'));
    }

    public function update(UpdateRoleWebRequest $request, Role $role): RedirectResponse
    {
        $role = $this->service->update($role, $request->validated());

        return redirect()
            ->route('admin.roles.index', ['selected' => $role->id])
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->service->delete($role);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}