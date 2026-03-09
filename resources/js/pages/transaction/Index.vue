<script setup lang="ts">
import DataTable from '@/components/transaction-components/DataTable.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import transaction from '@/routes/transaction'
import type { BreadcrumbItem } from '@/types'
import type { TransactionRow } from '@/types/transaction'
import { Head } from '@inertiajs/vue3'

const props = defineProps<{
    transactions: { data: TransactionRow[] }
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transactions',
        href: transaction.index(),
    },
]
</script>

<template>
    <Head title="Transactions" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <DataTable :transactions="props.transactions.data" />
        </div>
    </AppLayout>
</template>
