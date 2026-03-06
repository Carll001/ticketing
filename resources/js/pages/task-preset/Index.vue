<script setup lang="ts">
import DataTable from '@/components/task-preset-components/DataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import taskPreset from '@/routes/taskPreset';
import { BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type TaskPresetRow = {
    id: string
    name: string
    description?: string | null
    created_at?: string
    updated_at?: string
}

const props = defineProps<{
    presets?: { data: TaskPresetRow[] }
    taskPresets?: { data: TaskPresetRow[] }
    tasks?: { data: TaskPresetRow[] }
}>()

const presetRows = computed(() => props.presets?.data ?? props.taskPresets?.data ?? props.tasks?.data ?? [])

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task Presets',
        href: taskPreset.index(),
    },
];



const page = usePage();
const auth = computed(() => page.props.auth)
</script>
<template>

    <Head title="Task Presets" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <DataTable :presets="presetRows" :auth="auth.user"/>
        </div>
    </AppLayout>
</template>
