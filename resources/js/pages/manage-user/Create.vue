<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Checkbox } from '@/components/ui/checkbox';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import AppLayout from '@/layouts/AppLayout.vue';
import user from '@/routes/user';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Department } from '@/types/department';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { computed, ref } from 'vue'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from 'vue-sonner'
import { BreadcrumbItem } from '@/types';

const props = defineProps<{
    departments: { data: Department[] }
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: user.index(),
    },
    {
        title: 'Create user',
        href: user.create(),
    },
];

const page = usePage();
const auth = computed(() => page.props.auth);
const isSuperAdmin = computed(() => auth.value.user.role === 'superadmin')

const open = ref(false)

const PERMISSIONS = [
    { value: 'manage users', label: 'Manage Users' },
    { value: 'manage departments', label: 'Manage Departments' },
    { value: 'manage tasks', label: 'Manage Tasks' },
    { value: 'manage transactions', label: 'Manage Transactions' },
]

const form = useForm({
    name: '',
    email: '',
    department_ids: [] as string[],
    role: '',
    password: '',
    password_confirmation: '',
    permissions: [] as string[],
})

const toggleDepartment = (id: string) => {
    const index = form.department_ids.indexOf(id)
    if (index === -1) {
        form.department_ids.push(id)
    } else {
        form.department_ids.splice(index, 1)
    }
}

const togglePermission = (value: string) => {
    const index = form.permissions.indexOf(value)
    if (index === -1) {
        form.permissions.push(value)
    } else {
        form.permissions.splice(index, 1)
    }
}

const createUser = () => {
    form.post(user.store().url, {
        onSuccess: () => {
            toast.success('User added successfully!');
        }
    })
}
</script>

<template>
    <Head title="Create User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <form class="flex flex-col flex-1 gap-4 p-4" @submit.prevent="createUser">

            <section class="flex justify-end">
                <Button>Create User</Button>
            </section>

            <section class="grid grid-cols-[2fr_1fr] gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>User Details</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-[2fr_1fr] gap-4">
                            <section class="space-y-2">
                                <Label>Name</Label>
                                <Input v-model="form.name" placeholder="Enter full name" />
                                <InputError :message="form.errors.name" />
                            </section>
                            <section class="space-y-2">
                                <Label>Department</Label>
                                <Popover v-model:open="open">
                                    <PopoverTrigger as-child>
                                        <Button variant="outline" role="combobox"
                                            class="w-full justify-between font-normal">
                                            <span v-if="!form.department_ids.length" class="text-muted-foreground">
                                                Select departments...
                                            </span>
                                            <span v-else>
                                                {{ form.department_ids.length }} department{{ form.department_ids.length > 1 ? 's' : '' }} selected
                                            </span>
                                            <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="p-0">
                                        <Command>
                                            <CommandInput placeholder="Search departments..." />
                                            <CommandList>
                                                <CommandEmpty>No departments found.</CommandEmpty>
                                                <CommandGroup>
                                                    <CommandItem
                                                        v-for="dept in props.departments.data"
                                                        :key="dept.id"
                                                        :value="dept.id"
                                                        @select.prevent="toggleDepartment(dept.id)"
                                                    >
                                                        <Check class="mr-2 h-4 w-4"
                                                            :class="form.department_ids.includes(dept.id) ? 'opacity-100' : 'opacity-0'" />
                                                        {{ dept.name }}
                                                    </CommandItem>
                                                </CommandGroup>
                                            </CommandList>
                                        </Command>
                                    </PopoverContent>
                                </Popover>
                                <InputError :message="form.errors.department_ids" />
                            </section>
                        </div>

                        <div :class="isSuperAdmin ? 'grid grid-cols-[2fr_1fr] gap-4' : 'grid grid-cols-1'">
                            <section class="space-y-2">
                                <Label>Email</Label>
                                <Input v-model="form.email" type="email" placeholder="Enter email" />
                                <InputError :message="form.errors.email" />
                            </section>
                            <section v-if="isSuperAdmin" class="space-y-2">
                                <Label>Role</Label>
                                <Select v-model="form.role">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select role..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="admin">Admin</SelectItem>
                                        <SelectItem value="staff">Staff</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.role" />
                            </section>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <section class="space-y-2">
                                <Label>Password</Label>
                                <Input v-model="form.password" type="password" placeholder="Enter password" />
                                <InputError :message="form.errors.password" />
                            </section>
                            <section class="space-y-2">
                                <Label>Confirm Password</Label>
                                <Input v-model="form.password_confirmation" type="password"
                                    placeholder="Confirm password" />
                                <InputError :message="form.errors.password_confirmation" />
                            </section>
                        </div>
                    </CardContent>
                </Card>

                <!-- Permissions Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>Permissions</CardTitle>
                        <CardDescription>Assign permissions to this user</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div
                            v-for="permission in PERMISSIONS"
                            :key="permission.value"
                            class="flex items-center justify-between rounded-lg border p-3"
                        >
                            <Label :for="permission.value" class="cursor-pointer font-normal">
                                {{ permission.label }}
                            </Label>
                            <Checkbox
                                :id="permission.value"
                                :checked="form.permissions.includes(permission.value)"
                                @update:checked="togglePermission(permission.value)"
                            />
                        </div>
                        <InputError :message="form.errors.permissions" />
                    </CardContent>
                </Card>
            </section>
        </form>
    </AppLayout>
</template>