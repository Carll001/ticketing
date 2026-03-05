<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Services\TaskService;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TaskRepository $taskRepository): Response
    {
        return Inertia::render('Task/Index', [
            'tasks' => TaskResource::collection($taskRepository->getForIndex())->resolve(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(TaskRepository $taskRepository): Response
    {
        return Inertia::render('Task/Create', [
            'departments' => $taskRepository->getDepartmentOptions(),
            'users' => $taskRepository->getUserOptions(),
            'presets' => $taskRepository->getTaskPresetOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request, TaskService $taskService): RedirectResponse
    {
        $task = $taskService->post($request->validated(), $request->user());

        return redirect()
            ->route('task.show', $task)
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task, TaskRepository $taskRepository): Response
    {
        $loadedTask = $taskRepository->getByIdForShow($task->id);

        return Inertia::render('Task/Show', [
            'task' => (new TaskResource($loadedTask))->resolve(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task, TaskRepository $taskRepository): Response
    {
        $loadedTask = $taskRepository->getByIdForShow($task->id);

        return Inertia::render('Task/Edit', [
            'task' => (new TaskResource($loadedTask))->resolve(),
            'departments' => $taskRepository->getDepartmentOptions(),
            'users' => $taskRepository->getUserOptions(),
            'presets' => $taskRepository->getTaskPresetOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task, TaskService $taskService): RedirectResponse
    {
        $updatedTask = $taskService->update($task, $request->validated());

        return redirect()
            ->route('task.show', $updatedTask)
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
