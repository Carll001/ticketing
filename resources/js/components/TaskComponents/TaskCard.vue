<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

type RelatedUser = {
    id: string | number;
    name: string;
};

type RelatedDepartment = {
    id: string | number;
    name: string;
};

type Task = {
    id: string | number;
    title: string;
    description: string | null;
    status: string;
    due_date: string | null;
    creator?: RelatedUser | null;
    department_assigned?: RelatedDepartment | null;
};

defineProps<{
    tasks: Task[];
}>();
</script>

<template>
    <div v-if="tasks.length === 0" class="rounded-lg border p-6 text-sm text-muted-foreground">
        No tasks yet.
    </div>

    <div v-else class="grid gap-3">
        <Link
            v-for="task in tasks"
            :key="task.id"
            :href="`/task/${task.id}`"
            class="rounded-lg border p-4 transition-colors hover:bg-muted/40"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate font-medium">{{ task.title }}</p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ task.description || 'No description' }}
                    </p>
                </div>

                <span class="rounded border px-2 py-0.5 text-xs capitalize">
                    {{ task.status }}
                </span>
            </div>

            <div class="mt-3 flex flex-wrap gap-3 text-xs text-muted-foreground">
                <span>Due: {{ task.due_date || '-' }}</span>
                <span>Department: {{ task.department_assigned?.name || '-' }}</span>
                <span>Creator: {{ task.creator?.name || '-' }}</span>
            </div>
        </Link>
    </div>
</template>
