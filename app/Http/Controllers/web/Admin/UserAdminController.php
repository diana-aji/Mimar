<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Enums\SystemRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Web\User\StoreUserWebRequest;
use App\Http\Requests\Web\User\UpdateUserWebRequest;
use App\Services\Web\UserWebService;

class UserAdminController extends Controller
{
    public function __construct(
        protected UserWebService $service
    ) {
    }

    public function index(Request $request): View
    {
        $users = $this->service->paginate((int) $request->query('per_page', 12))
            ->withQueryString();

        $userItems = collect($users->items());

        $selectedUser = null;

        if ($request->filled('selected')) {
            $selectedUser = $userItems->firstWhere('id', (int) $request->query('selected'));
        }

        if (! $selectedUser) {
            $selectedUser = $userItems->first();
        }

        $roles = [
            SystemRole::ADMIN->value,
            SystemRole::USER->value,
        ];

        return view('admin.users.index', compact('users', 'selectedUser', 'roles'));
    }

    public function store(StoreUserWebRequest $request): RedirectResponse
    {
        $user = $this->service->create($request->validated());

        return redirect()
            ->route('admin.users.index', ['selected' => $user->id])
            ->with('success', __('messages.created_successfully'));
    }

    public function update(UpdateUserWebRequest $request, User $user): RedirectResponse
    {
        $user = $this->service->update($user, $request->validated());

        return redirect()
            ->route('admin.users.index', ['selected' => $user->id])
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->service->delete($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}