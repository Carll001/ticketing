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
import { createReusableTemplate } from '@vueuse/core'
import { MoreHorizontal } from 'lucide-vue-next'
import { h, ref } from 'vue'

import { valueUpdater } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import { User } from '@/types'
import { router } from '@inertiajs/vue3'
import { Task } from '@/types/task'
import task from '@/routes/task'
import PermissionGuard from '@/components/PermissionGuard.vue'

const props = defineProps<{
    tasks: Task[]
    auth: User
}>()

const [DefineTemplate, ReuseTemplate] = createReusableTemplate<{
    task: Task
}>()

const createTask = () => router.visit(task.create())
const editTask = (id: string) => router.visit(task.edit(id))
const showTask = (id: string) => router.visit(task.show(id))
const removeTask = (id: string) => router.delete(task.destroy(id).url)
const isAdmin = props.auth?.role === 'admin'

const columns: ColumnDef<Task>[] = [
    {
        id: 'select',
        header: ({ table }) => h(Checkbox, {
            'modelValue': table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
            'onUpdate:modelValue': value => table.toggleAllPageRowsSelected(!!value),
            'ariaLabel': 'Select all',
        }),
        cell: ({ row }) => h(Checkbox, {
            'modelValue': row.getIsSelected(),
            'onUpdate:modelValue': value => row.toggleSelected(!!value),
            'ariaLabel': 'Select row',
        }),
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'title',
        header: 'Title',
        cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('title')),
    },
    {
        id: 'department_assigned',
        header: 'Department Assigned',
        cell: ({ row }) => {
            const task = row.original
            const departmentName = task.department_assigned?.name

            return h(
                'div',
                { class: departmentName ? 'capitalize' : 'text-muted-foreground italic' },
                departmentName || 'Open for anyone'
            )
        },
    },
    {
        id: 'creator',
        header: 'Creator',
        cell: ({ row }) => {
            const task = row.original
            const creatorName = task.creator?.name

            return h(
                'div',
                { class: creatorName ? '' : 'text-muted-foreground italic' },
                creatorName || '-'
            )
        },
    },
    {
        id: 'steps_count',
        header: 'Steps',
        cell: ({ row }) => {
            const count = row.original.steps_count ?? 0
            return h('div', String(count))
        },
    },
    {
        id: 'last_step',
        header: 'Last Step',
        cell: ({ row }) => {
            const currentTask = row.original
            const lastStepTitle = currentTask.last_step?.title
            const lastStepTaker = currentTask.last_step?.claimed_by?.name

            if (!lastStepTitle) {
                return h('div', { class: 'text-muted-foreground italic' }, 'No steps')
            }

            return h('div', { class: 'space-y-1' }, [
                h('p', { class: 'text-sm font-medium' }, lastStepTitle),
                ...(isAdmin
                    ? [h('p', { class: 'text-xs text-muted-foreground' }, `Taker: ${lastStepTaker || 'Unclaimed'}`)]
                    : []),
            ])
        },
    },
    {
        id: 'status',
        header: 'Status',
        cell: ({ row }) => {
            const status = row.original.last_step?.status || row.original.status || 'pending'
            const label = status === 'done'
                ? 'Completed'
                : status === 'in_progress'
                    ? 'In Progress'
                    : 'Pending'
            return h('div', label)
        },
    },
    {
        id: 'actions',
        header: 'Actions',
        enableHiding: false,
        cell: ({ row }) => {
            const currentTask = row.original

            return h('div', { class: 'flex justify-end' }, [
                h(ReuseTemplate, {
                    task: currentTask,
                }),
            ])
        },
    },
]

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})

const table = useVueTable({
    get data() { return props.tasks },
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
    onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
    onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
    state: {
        get sorting() { return sorting.value },
        get columnFilters() { return columnFilters.value },
        get columnVisibility() { return columnVisibility.value },
        get rowSelection() { return rowSelection.value },
    },
})
</script>

<template>
    <DefineTemplate v-slot="{ task }">
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="h-8 w-8 p-0">
                <span class="sr-only">Open menu</span>
                <MoreHorizontal class="h-4 w-4" />
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end">
            <DropdownMenuLabel>Actions</DropdownMenuLabel>

            <PermissionGuard permission="manage tasks">
                <DropdownMenuItem @click="editTask(task.id)">
                    Edit Task
                </DropdownMenuItem>
            </PermissionGuard>

            
                <DropdownMenuItem @click="showTask(task.id)">
                    View Task
                </DropdownMenuItem>
            

            <DropdownMenuSeparator />

            <PermissionGuard permission="manage tasks">
                <DropdownMenuItem @click="removeTask(task.id)">
                    Remove Task
                </DropdownMenuItem>
            </PermissionGuard>
        </DropdownMenuContent>
    </DropdownMenu>

    
        <Button size="sm" @click="showTask(task.id)">
            View Task
        </Button>
    
</DefineTemplate>
    <div class="w-full">
        <div class="flex items-center py-4">
            <Input class="max-w-sm" placeholder="Filter tasks..."
                :model-value="table.getColumn('title')?.getFilterValue() as string"
                @update:model-value="table.getColumn('title')?.setFilterValue($event)" />

            <Button @click="createTask" class="ml-auto">Create</Button>
        </div>
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length" class="">
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
                <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()"
                    @click="table.previousPage()">
                    Previous
                </Button>
                <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
                    Next
                </Button>
            </div>
        </div>
    </div>
</template>
