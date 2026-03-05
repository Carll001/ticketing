<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Services\TaskService;
use App\Models\Department;
use App\Models\Task;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Task/Index', [
            'tasks' => Task::query()
                ->latest()
                ->with(['departmentAssigned:id,name', 'creator:id,name'])
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Task/Create', [
            'departments' => Department::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request, TaskService $taskService): RedirectResponse
    {
        $task = $taskService->create($request->validated(), $request->user());

        return redirect()
            ->route('task.show', $task)
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): Response
    {
        $task->load(['departmentAssigned:id,name', 'creator:id,name']);

        return Inertia::render('Task/Show', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): Response
    {
        return Inertia::render('Task/Edit', [
            'task' => $task,
            'departments' => Department::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task, TaskService $taskService): RedirectResponse
    {
        $taskService->update($task, $request->validated());

        return redirect()
            ->route('task.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()
            ->route('task.index')
            ->with('success', 'Task deleted successfully.');
    }
}
