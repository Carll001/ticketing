<script setup lang="ts">
import DataTable from '@/components/manage-department-components/DataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { User } from '@/types';
import { Department } from '@/types/department';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    departments: { data: Department[] }
}>()

const page = usePage();
const auth = computed(() => page.props.auth)

</script>
<template>

    <Head title="Departments" />
    <AppLayout>
        <div class="flex flex-col flex-1 gap-4 p-4">
            <DataTable :departments="props.departments.data" :auth="auth.user"/>
        </div>
    </AppLayout>
</template>