<script setup lang="ts">
import type {
    ColumnDef,
    ColumnFiltersState,
    ExpandedState,
    SortingState,
    VisibilityState,
} from '@tanstack/vue-table'
import {
    FlexRender,
    getCoreRowModel,
    getExpandedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table'
import { createReusableTemplate } from '@vueuse/core'
import { ArrowUpDown, MoreHorizontal } from 'lucide-vue-next'
import { h, ref } from 'vue'

import { valueUpdater } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
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
import user from '@/routes/user'

const props = defineProps<{
    users: User[]
    auth: User
}>()

const [DefineTemplate, ReuseTemplate] = createReusableTemplate<{
    user: User
    onExpand: () => void
    onDelete: () => void
}>()

const deleteDialog = ref(false)
const userToDelete = ref<User | null>(null)

const createUser = () => router.visit(user.create())
const editUser = (id: string) => router.visit(user.edit(id))
const showUser = (id: string) => router.visit(user.show(id))

const confirmDelete = (u: User) => {
    userToDelete.value = u
    deleteDialog.value = true
}

const deleteUser = () => {
    if (!userToDelete.value) return
    router.delete(user.destroy(userToDelete.value.id).url, {
        preserveScroll: true,
        onSuccess: () => {
            deleteDialog.value = false
            userToDelete.value = null
        },
    })
}

const columns: ColumnDef<User>[] = [
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
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('name')),
    },
    {
        accessorKey: 'email',
        header: ({ column }) => h(Button, {
            variant: 'ghost',
            onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
        }, () => ['Email', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]),
        cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('email')),
    },
    {
        accessorKey: 'role',
        header: ({ column }) => h(Button, {
            variant: 'ghost',
            onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
        }, () => ['Role', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })]),
        cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('role')),
    },
    {
        accessorKey: 'departments',
        header: 'Departments',
        cell: ({ row }) => {
            const departments = row.getValue('departments') as { id: string; name: string }[]
            if (!departments?.length) return h('span', { class: 'text-muted-foreground text-sm' }, 'None')

            return h('div', { class: 'flex flex-wrap gap-1 max-w-[400px]' },
                departments.map(dept =>
                    h('span', {
                        class: 'w-auto inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-medium w-[calc(33.333%-4px)]'
                    }, dept.name)
                )
            )
        },
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const u = row.original
            return h('div', { class: 'flex justify-end' }, [
                h(ReuseTemplate, {
                    user: u,
                    onExpand: row.toggleExpanded,
                    onDelete: () => confirmDelete(u),
                })
            ])
        },
    },
]

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})
const expanded = ref<ExpandedState>({})

const table = useVueTable({
    get data() { return props.users },
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getExpandedRowModel: getExpandedRowModel(),
    onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
    onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
    onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
    onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
    state: {
        get sorting() { return sorting.value },
        get columnFilters() { return columnFilters.value },
        get columnVisibility() { return columnVisibility.value },
        get rowSelection() { return rowSelection.value },
        get expanded() { return expanded.value },
    },
})
</script>

<template>
    <DefineTemplate v-slot="{ user, onDelete }">
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" class="h-8 w-8 p-0">
                    <span class="sr-only">Open menu</span>
                    <MoreHorizontal class="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuLabel>Actions</DropdownMenuLabel>
                <DropdownMenuItem @click="editUser(user.id)">
                    Edit User
                </DropdownMenuItem>
                <DropdownMenuItem @click="showUser(user.id)">View User</DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem
                    class="text-destructive focus:text-destructive"
                    @click="onDelete"
                >
                    Remove User
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </DefineTemplate>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:open="deleteDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Delete User</DialogTitle>
                <DialogDescription>
                    Are you sure you want to delete
                    <span class="font-medium text-foreground">{{ userToDelete?.name }}</span>?
                    This action cannot be undone.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="deleteDialog = false">
                    Cancel
                </Button>
                <Button variant="destructive" @click="deleteUser">
                    Delete
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <div class="w-full">
        <div class="flex items-center py-4">
            <Input
                class="max-w-sm"
                placeholder="Filter emails..."
                :model-value="table.getColumn('email')?.getFilterValue() as string"
                @update:model-value="table.getColumn('email')?.setFilterValue($event)"
            />
            <Button class="ml-auto" @click="createUser">Create User</Button>
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
                    <template v-if="table.getRowModel().rows?.length">
                        <template v-for="row in table.getRowModel().rows" :key="row.id">
                            <TableRow :data-state="row.getIsSelected() && 'selected'">
                                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                    <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="row.getIsExpanded()">
                                <TableCell :colspan="row.getAllCells().length">
                                    {{ JSON.stringify(row.original) }}
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
