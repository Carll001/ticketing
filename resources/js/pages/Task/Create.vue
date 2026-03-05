<script setup lang='ts'>
import TaskCreateForm from '@/components/TaskComponents/TaskCreateForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

type Department = {
    id: string;
    name: string;
};

const props = defineProps<{
    departments: Department[];
}>();
</script>

<template>
    <Head title='Create Task' />

    <AppLayout>
        <div class='flex flex-1 flex-col gap-4 p-4'>
            <TaskCreateForm :departments='props.departments' />
        </div>
    </AppLayout>
</template>
