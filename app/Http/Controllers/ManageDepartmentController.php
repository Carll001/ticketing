<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Services\ManageDepartmentService;
use Inertia\Inertia;

class ManageDepartmentController extends Controller
{
    protected ManageDepartmentService $service;

    public function __construct(ManageDepartmentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $departments = Department::latest()->get();

        return Inertia::render('manage-department/Index', [
            'departments' => DepartmentResource::collection($departments),
        ]);
    }

    public function create()
    {
        return Inertia::render('manage-department/Create');
    }

    public function store(DepartmentRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()->route('department.index');
    }

    public function edit(Department $department)
    {
        return Inertia::render('manage-department/Edit', [
            'department' => DepartmentResource::make($department),
        ]);
    }

    public function update(DepartmentRequest $request, Department $department)
    {
        $this->service->update($department, $request->validated());

        return redirect()->route('department.index');
    }

    public function destroy(Department $department)
    {
        $this->service->destroy($department);

        return redirect()->route('department.index');
    }
}