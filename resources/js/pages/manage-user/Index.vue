<script setup lang="ts">
import DataTable from '@/components/manage-user-components/DataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { User } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    users: { data: User[] }
}>()

const page = usePage();
const auth = computed(() => page.props.auth)

</script>
<template>

    <Head title="Users" />
    <AppLayout>
        <div class="flex flex-col flex-1 gap-4 p-4">
            <DataTable :users="props.users.data" :auth="auth.user"/>
        </div>
    </AppLayout>
</template>