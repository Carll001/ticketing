<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Task;
use App\Models\TaskPreset;
use App\Models\User;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = request()->user();
        $departmentIds = $user->departments()->pluck('departments.id');

        $tasks = Task::with(['departmentAssigned', 'creator', 'steps', 'lastStep.claimedBy'])
            ->withCount('steps')
            ->where(function ($query) use ($user, $departmentIds) {
                $query->whereNull('department_assigned_id')
                    ->orWhere('creator_id', $user->id);

                if ($departmentIds->isNotEmpty()) {
                    $query->orWhereIn('department_assigned_id', $departmentIds);
                }
            })
            ->latest()
            ->get();

        $statusSummary = [
            'open' => 0,
            'in_progress' => 0,
            'completed' => 0,
        ];

        foreach ($tasks as $task) {
            $statusSummary[$this->resolveTaskStatus($task)]++;
        }

        $tasksByDepartment = $tasks
            ->groupBy(fn (Task $task) => $task->departmentAssigned?->name ?? 'Open for anyone')
            ->map(fn (Collection $group, string $department) => [
                'department' => $department,
                'tasks' => $group->count(),
            ])
            ->values()
            ->sortByDesc('tasks')
            ->values();

        $isAdmin = ($user->role ?? null) === 'admin';
        $recentTasks = $tasks
            ->take(8)
            ->map(fn (Task $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'department' => $task->departmentAssigned?->name ?? 'Open for anyone',
                'creator' => $task->creator?->name,
                'steps_count' => $task->steps_count ?? 0,
                'status' => $this->resolveTaskStatus($task),
                'last_step' => $task->lastStep?->title,
                'taker' => $isAdmin ? $task->lastStep?->claimedBy?->name : null,
                'created_at' => $task->created_at,
            ])
            ->values();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_users' => User::count(),
                'total_departments' => Department::count(),
                'total_presets' => TaskPreset::count(),
                'total_tasks' => $tasks->count(),
                'open_tasks' => $statusSummary['open'],
                'in_progress_tasks' => $statusSummary['in_progress'],
                'completed_tasks' => $statusSummary['completed'],
            ],
            'tasks_by_department' => $tasksByDepartment,
            'recent_tasks' => $recentTasks,
            'is_admin' => $isAdmin,
        ]);
    }

    private function resolveTaskStatus(Task $task): string
    {
        $steps = $task->steps;

        if ($steps->isEmpty()) {
            return 'open';
        }

        if ($steps->every(fn ($step) => $step->status === 'done')) {
            return 'completed';
        }

        if ($steps->contains(fn ($step) => $step->status === 'in_progress')) {
            return 'in_progress';
        }

        return 'open';
    }
}
