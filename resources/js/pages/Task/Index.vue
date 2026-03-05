<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import { Head, Link } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';    

type Task = {
    id: string;
    title: string;
    description: string | null;
    due_date: string | null;
    created_at: string;
    department_assigned?: {
        id: string;
        name: string;
    } | null;
    creator?: {
        id: string;
        name: string;
    } | null;
};

const props = defineProps<{
    tasks: Task[];
}>();
</script>
<template>

    <Head title="Task" />
    <AppLayout >
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex justify-between gap-4">
                <div class="relative w-120">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input class="pl-10" placeholder="Search..." />
                </div>
                <div>
                    <Link href="/task/create">
                        <Button>Create</Button>
                    </Link>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <div
                    v-for="task in props.tasks"
                    :key="task.id"
                    class="rounded-lg border p-4"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-lg font-semibold">{{ task.title }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ task.description || 'No description provided.' }}
                            </p>
                        </div>
                        <Link :href="`/task/${task.id}`" class="text-sm underline">
                            View
                        </Link>
                    </div>
                    <div class="mt-3 text-xs text-muted-foreground">
                        <span>Department: {{ task.department_assigned?.name ?? '-' }}</span>
                        <span class="mx-2">|</span>
                        <span>Creator: {{ task.creator?.name ?? '-' }}</span>
                        <span class="mx-2">|</span>
                        <span>Due: {{ task.due_date ?? '-' }}</span>
                    </div>
                </div>

                <p v-if="props.tasks.length === 0" class="rounded-lg border p-6 text-center text-sm text-muted-foreground">
                    Wala pang tasks.
                </p>
            </div>
            <!-- pagination -->
            <div class="border-t px-6 py-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm">{{ props.tasks.length }} task(s)</p>
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm">Previous</Button>
                        <Button variant="outline" size="sm">Next</Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
