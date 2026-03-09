<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\UserResource;
use App\Models\Department;
use App\Models\User;
use App\Services\ManageUserService;
use Inertia\Inertia;

class ManageUserController extends Controller
{
    protected ManageUserService $service;

    public function __construct(ManageUserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return Inertia::render('manage-user/Index', [
            'users' => UserResource::collection(User::whereNot('role', 'superadmin')->with('departments')->get()),
            'departments' => DepartmentResource::collection(Department::all())
        ]);
    }

    public function create()
    {
        return Inertia::render('manage-user/Create', [
            'departments' => DepartmentResource::collection(Department::all())
        ]);
    }

    public function store(UserRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()->route('user.index');
    }

    public function edit(User $user)
    {
        return Inertia::render('manage-user/Edit', [
            'user' => UserResource::make($user->load('departments', 'permissions')),
            'departments' => DepartmentResource::collection(Department::all())
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $this->service->update($user, $request->validated());

        return redirect()->route('user.index');
    }

    public function destroy(User $user)
    {
        $this->service->destroy($user);

        return redirect()->route('user.index');
    }
}