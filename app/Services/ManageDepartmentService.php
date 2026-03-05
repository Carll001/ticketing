<?php

namespace App\Services;

use App\Models\Department;

class ManageDepartmentService
{
    public function store(array $data): Department
    {
        return Department::create($data);
    }

    public function update(Department $department, array $data): Department
    {
        $department->update($data);

        return $department;
    }

    public function destroy(Department $department): void
    {
        $department->delete();
    }
}