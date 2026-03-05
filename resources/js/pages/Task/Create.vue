<script setup lang="ts">
import TaskCreateForm from '@/components/TaskComponents/TaskCreateForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Create Task" />

    <AppLayout>
        <div class="flex flex-1 flex-col gap-4 p-4">
            <TaskCreateForm />
        </div>
    </AppLayout>
</template>
