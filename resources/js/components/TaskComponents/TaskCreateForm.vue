<script setup lang='ts'>
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import task from '@/routes/task';
import { useForm } from '@inertiajs/vue3';
import type { DateValue } from '@internationalized/date';
import { ChevronDownIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Separator from '../ui/separator/Separator.vue';

type Department = {
    id: string;
    name: string;
};

const props = defineProps<{
    departments: Department[];
}>();

const selectedDueDate = ref<DateValue | undefined>();

const form = useForm({
    title: '',
    description: '',
    due_date: null as string | null,
    step_order: 'sequential' as 'sequential' | 'random',
    department_assigned_id: null as string | null,
});

const departmentModel = computed({
    get: () => form.department_assigned_id ?? 'anyone',
    set: (value: string) => {
        form.department_assigned_id = value === 'anyone' ? null : value;
    },
});

const dueDateLabel = computed(() => selectedDueDate.value?.toString() ?? 'Pick a due date');

const submit = () => {
    form.due_date = selectedDueDate.value ? selectedDueDate.value.toString() : null;

    form.post(task.store.url(), {
        preserveScroll: true,
    });
};

const discard = () => {
    form.reset();
    selectedDueDate.value = undefined;
};
</script>

<template>
    <div class='space-y-4'>
        <form @submit.prevent='submit'>
            <section class='flex justify-between'>
                <div class='flex gap-2'>
                    <Heading title='Add Task' />
                </div>
                <div class='flex items-center gap-2'>
                    <Button size='sm' type='button' variant='destructive' @click='discard'>
                        Discard
                    </Button>
                    <Button size='sm' type='submit' :disabled='form.processing'>
                        Create
                    </Button>
                </div>
            </section>

            <section class='grid grid-cols-[3fr_2fr] gap-4'>
                <Card>
                    <CardHeader>
                        <CardTitle>Task Details</CardTitle>
                        <CardDescription>Description of task</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class='space-y-4'>
                            <section class='flex items-center gap-4'>
                                <div class='w-full'>
                                    <Label for='task-title'>
                                        Task title
                                        <span class='text-lg text-red-500'>*</span>
                                    </Label>
                                    <Input
                                        id='task-title'
                                        v-model='form.title'
                                        placeholder='Task title'
                                    />
                                    <InputError :message='form.errors.title' />
                                </div>
                                <div class='flex flex-col gap-3'>
                                    <Label for='date' class='px-1'>
                                        Due date
                                        <span class='text-muted-foreground text-xs'>(Optional)</span>
                                    </Label>
                                    <Popover>
                                        <PopoverTrigger as-child>
                                            <Button
                                                id='date'
                                                variant='outline'
                                                class='w-48 justify-between font-normal'
                                            >
                                                <span class='truncate text-left'>{{ dueDateLabel }}</span>
                                                <ChevronDownIcon class='h-4 w-4 shrink-0' />
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent
                                            class='w-auto overflow-hidden p-0'
                                            align='start'
                                        >
                                            <Calendar
                                                v-model='selectedDueDate'
                                                layout='month-and-year'
                                            />
                                        </PopoverContent>
                                    </Popover>
                                </div>
                            </section>
                            <section class='space-y-4'>
                                <Label for='task-description'>
                                    Task description
                                    <span class='text-muted-foreground text-xs'>(Optional)</span>
                                </Label>
                                <Textarea
                                    id='task-description'
                                    v-model='form.description'
                                    placeholder='Task description'
                                />
                                <InputError :message='form.errors.description' />
                            </section>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Additional details</CardTitle>
                        <CardDescription>Additional details</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class='space-y-4'>
                            <Label for='step-order'>
                                Step order
                                <span class='text-lg text-red-500'>*</span>
                            </Label>
                            <Select id='step-order' v-model='form.step_order'>
                                <SelectTrigger class='w-full'>
                                    <SelectValue placeholder='Select an order' />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Task order</SelectLabel>
                                        <SelectItem value='sequential'>Sequential</SelectItem>
                                        <SelectItem value='random'>Random</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message='form.errors.step_order' />
                        </div>

                        <Separator class='my-4' />

                        <div class='space-y-4'>
                            <Label for='department_assigned_id'>Assign department</Label>
                            <Select id='department_assigned_id' v-model='departmentModel'>
                                <SelectTrigger class='w-full'>
                                    <SelectValue placeholder='Select a department' />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Department</SelectLabel>
                                        <SelectItem value='anyone'>Anyone</SelectItem>
                                        <SelectItem
                                            v-for='department in props.departments'
                                            :key='department.id'
                                            :value='department.id'
                                        >
                                            {{ department.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message='form.errors.department_assigned_id' />
                        </div>
                    </CardContent>
                </Card>
            </section>
        </form>
    </div>
</template>
