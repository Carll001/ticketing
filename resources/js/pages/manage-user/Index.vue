<script setup lang="ts">
import DataTable from '@/components/manage-user-components/DataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, User } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import user from '@/routes/user';

const props = defineProps<{
    users: { data: User[] }
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: user.index(),
    },
];

const page = usePage();
const auth = computed(() => page.props.auth)

</script>
<template>

    <Head title="Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col flex-1 gap-4 p-4">
            <DataTable :users="props.users.data" :auth="auth.user"/>
        </div>
    </AppLayout>
</template>