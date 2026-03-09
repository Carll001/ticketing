<script setup lang="ts">
import type {
    ColumnDef,
    ColumnFiltersState,
    SortingState,
    VisibilityState,
} from '@tanstack/vue-table'
import {
    FlexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table'
import { h, ref } from 'vue'

import { valueUpdater } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import type { TransactionRow } from '@/types/transaction'

const props = defineProps<{
    transactions: TransactionRow[]
}>()

const prettyAction = (action: string) => action
    .split('_')
    .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
    .join(' ')

const formatDateTime = (value: string) => {
    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return '-'

    return new Intl.DateTimeFormat('en-US', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date)
}

const columns: ColumnDef<TransactionRow>[] = [
    {
        id: 'select',
        header: ({ table }) => h(Checkbox, {
            modelValue: table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
            'onUpdate:modelValue': (value) => table.toggleAllPageRowsSelected(!!value),
            ariaLabel: 'Select all',
        }),
        cell: ({ row }) => h(Checkbox, {
            modelValue: row.getIsSelected(),
            'onUpdate:modelValue': (value) => row.toggleSelected(!!value),
            ariaLabel: 'Select row',
        }),
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'action',
        header: 'Action',
        cell: ({ row }) => h('div', prettyAction(String(row.getValue('action')))),
    },
    {
        accessorKey: 'summary',
        header: 'Summary',
        cell: ({ row }) => h('div', { class: 'max-w-[440px] truncate' }, String(row.getValue('summary') || '-')),
    },
    {
        id: 'task',
        header: 'Task',
        accessorFn: (row) => row.task?.title ?? '',
        cell: ({ row }) => {
            const taskTitle = row.original.task?.title
            return h('div', taskTitle || '-')
        },
    },
    {
        id: 'step',
        header: 'Step',
        accessorFn: (row) => row.step?.title ?? '',
        cell: ({ row }) => {
            const stepTitle = row.original.step?.title
            return h('div', stepTitle || '-')
        },
    },
    {
        id: 'actor',
        header: 'Actor',
        accessorFn: (row) => row.actor?.name ?? row.actor?.email ?? '',
        cell: ({ row }) => {
            const actor = row.original.actor
            return h('div', actor?.name || actor?.email || '-')
        },
    },
    {
        accessorKey: 'created_at',
        header: 'Date/Time',
        cell: ({ row }) => h('div', formatDateTime(String(row.getValue('created_at')))),
    },
]

const sorting = ref<SortingState>([{ id: 'created_at', desc: true }])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})

const table = useVueTable({
    get data() {
        return props.transactions
    },
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnFilters),
    onColumnVisibilityChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnVisibility),
    onRowSelectionChange: (updaterOrValue) => valueUpdater(updaterOrValue, rowSelection),
    state: {
        get sorting() {
            return sorting.value
        },
        get columnFilters() {
            return columnFilters.value
        },
        get columnVisibility() {
            return columnVisibility.value
        },
        get rowSelection() {
            return rowSelection.value
        },
    },
})
</script>

<template>
    <div class="w-full">
        <div class="flex items-center py-4">
            <Input
                class="max-w-sm"
                placeholder="Filter logs..."
                :model-value="table.getColumn('summary')?.getFilterValue() as string"
                @update:model-value="table.getColumn('summary')?.setFilterValue($event)"
            />
        </div>
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id">
                            <FlexRender
                                v-if="!header.isPlaceholder"
                                :render="header.column.columnDef.header"
                                :props="header.getContext()"
                            />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <template v-for="row in table.getRowModel().rows" :key="row.id">
                            <TableRow :data-state="row.getIsSelected() && 'selected'">
                                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                    <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                </TableCell>
                            </TableRow>
                        </template>
                    </template>
                    <TableRow v-else>
                        <TableCell :colspan="columns.length" class="h-24 text-center">
                            No results.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <div class="flex items-center justify-end space-x-2 py-4">
            <div class="flex-1 text-sm text-muted-foreground">
                {{ table.getFilteredSelectedRowModel().rows.length }} of
                {{ table.getFilteredRowModel().rows.length }} row(s) selected.
            </div>
            <div class="space-x-2">
                <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
                    Previous
                </Button>
                <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
                    Next
                </Button>
            </div>
        </div>
    </div>
</template>
