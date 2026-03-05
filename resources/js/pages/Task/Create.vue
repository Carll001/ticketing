<script setup lang='ts'>
import { Head } from '@inertiajs/vue3';
import TaskCreateForm from '@/components/TaskComponents/TaskCreateForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';

type Department = {
    id: string;
    name: string;
};
type UserOption = {
    id: string;
    name: string;
};
type PresetOption = {
    id: string;
    name: string;
};

const props = defineProps<{
    departments: Department[];
    users: UserOption[];
    presets: PresetOption[];
}>();
</script>

<template>
    <Head title='Create Task' />

    <AppLayout>
        <div class='flex flex-1 flex-col gap-4 p-4'>
            <TaskCreateForm
                :departments='props.departments'
                :users='props.users'
                :presets='props.presets'
            />
        </div>
    </AppLayout>
</template>
