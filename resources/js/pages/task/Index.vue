<script setup lang="ts">
import DataTable from '@/components/task-components/DataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import task from '@/routes/task';
import { BreadcrumbItem } from '@/types';
import { Task } from '@/types/task';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    tasks: { data: Task[] }
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task',
        href: task.index(),
    },
];



const page = usePage();
const auth = computed(() => page.props.auth)
</script>
<template>

    <Head title="Tasks" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <DataTable :tasks="props.tasks.data" :auth="auth.user"/>
        </div>
    </AppLayout>
</template>
