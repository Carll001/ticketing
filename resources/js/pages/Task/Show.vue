<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

type RelatedUser = {
    id: number | string;
    name: string;
};

type RelatedDepartment = {
    id: number | string;
    name: string;
};

type Task = {
    id: number;
    title: string;
    description: string | null;
    status: string;
    due_date: string | null;
    created_at: string;
    updated_at: string;
    creator?: RelatedUser | null;
    department_assigned?: RelatedDepartment | null;
};

const props = defineProps<{
    task: Task;
}>();
</script>

<template>
    <Head :title="`Task #${props.task.id}`" />

    <AppLayout>
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">{{ props.task.title }}</h1>
                <div class="flex items-center gap-2">
                    <Link :href="`/task/${props.task.id}/edit`" class="rounded-md border px-3 py-2 text-sm">
                        Edit
                    </Link>
                    <Link href="/task" class="rounded-md border px-3 py-2 text-sm">Back</Link>
                </div>
            </div>

            <div class="rounded-lg border p-4">
                <dl class="grid gap-3 text-sm md:grid-cols-2">
                    <div>
                        <dt class="text-muted-foreground">Status</dt>
                        <dd class="font-medium">{{ props.task.status }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Due Date</dt>
                        <dd class="font-medium">{{ props.task.due_date ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Creator</dt>
                        <dd class="font-medium">{{ props.task.creator?.name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Department</dt>
                        <dd class="font-medium">{{ props.task.department_assigned?.name ?? '-' }}</dd>
                    </div>
                </dl>

                <div class="mt-4">
                    <p class="text-sm text-muted-foreground">Description</p>
                    <p class="mt-1 whitespace-pre-wrap">{{ props.task.description ?? 'No description provided.' }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
