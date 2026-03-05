<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type RelatedUser = {
    id: number | string;
    name: string;
};

type RelatedDepartment = {
    id: number | string;
    name: string;
};

type TaskField = {
    id: string;
    label: string;
    key: string;
    type: string;
    required: boolean;
    options: unknown[] | null;
    position: number;
};

type TaskStep = {
    id: string;
    step_details: {
        title: string;
        description: string | null;
        position: number;
        status: string;
    };
    additional_details: {
        has_cost: boolean;
        cost_amount: number | null;
    };
    fields: TaskField[];
};

type Task = {
    id: number | string;
    title: string;
    description: string | null;
    status: string;
    due_date: string | null;
    step_order?: string | null;
    assigned_to?: RelatedUser | null;
    task_preset_id?: string | null;
    cost_total?: number | null;
    created_at: string;
    updated_at: string;
    task_details?: {
        title: string;
        description: string | null;
        due_date: string | null;
    };
    additional_details?: {
        step_order: string;
        department_assigned?: RelatedDepartment | null;
        assigned_to?: RelatedUser | null;
        task_preset_id?: string | null;
        cost_total?: number | null;
    };
    steps?: TaskStep[];
    creator?: RelatedUser | null;
    department_assigned?: RelatedDepartment | null;
};

const props = defineProps<{
    task: Task;
}>();

const displayTitle = computed(() => props.task.task_details?.title ?? props.task.title);
const displayDescription = computed(
    () => props.task.task_details?.description ?? props.task.description ?? 'No description provided.',
);
const displayDueDate = computed(() => props.task.task_details?.due_date ?? props.task.due_date ?? '-');
const displayStepOrder = computed(
    () => props.task.additional_details?.step_order ?? props.task.step_order ?? '-',
);
const displayDepartment = computed(
    () => props.task.additional_details?.department_assigned?.name ?? props.task.department_assigned?.name ?? '-',
);
const displayAssignedTo = computed(
    () => props.task.additional_details?.assigned_to?.name ?? props.task.assigned_to?.name ?? '-',
);
const displayCostTotal = computed(
    () =>
        props.task.additional_details?.cost_total ?? props.task.cost_total ?? null,
);
const displayTaskPreset = computed(
    () => props.task.additional_details?.task_preset_id ?? props.task.task_preset_id ?? '-',
);
const taskSteps = computed(() => props.task.steps ?? []);
const stepFieldAnswers = ref<Record<string, string | number | boolean | null>>({});
</script>

<template>
    <Head :title="`Task #${props.task.id}`" />

    <AppLayout>
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">{{ displayTitle }}</h1>
                <div class="flex items-center gap-2">
                    <Link :href="`/task/${props.task.id}/edit`" class="rounded-md border px-3 py-2 text-sm">
                        Edit
                    </Link>
                    <Link href="/task" class="rounded-md border px-3 py-2 text-sm">Back</Link>
                </div>
            </div>

            <div class="rounded-lg border p-4">
                <dl class="grid gap-3 text-sm md:grid-cols-2">
                    <div>
                        <dt class="text-muted-foreground">Status</dt>
                        <dd class="font-medium">{{ props.task.status }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Due Date</dt>
                        <dd class="font-medium">{{ displayDueDate }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Creator</dt>
                        <dd class="font-medium">{{ props.task.creator?.name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Department</dt>
                        <dd class="font-medium">{{ displayDepartment }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Step Order</dt>
                        <dd class="font-medium capitalize">{{ displayStepOrder }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Assigned To</dt>
                        <dd class="font-medium">{{ displayAssignedTo }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Preset</dt>
                        <dd class="font-medium">{{ displayTaskPreset }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Cost Total</dt>
                        <dd class="font-medium">{{ displayCostTotal ?? '-' }}</dd>
                    </div>
                </dl>

                <div class="mt-4">
                    <p class="text-sm text-muted-foreground">Description</p>
                    <p class="mt-1 whitespace-pre-wrap">{{ displayDescription }}</p>
                </div>
            </div>

            <div class="rounded-lg border p-4">
                <h2 class="text-lg font-semibold">Task Steps</h2>

                <div v-if="taskSteps.length === 0" class="mt-3 text-sm text-muted-foreground">
                    No steps found for this task.
                </div>

                <div v-else class="mt-4 space-y-4">
                    <div
                        v-for="step in taskSteps"
                        :key="step.id"
                        class="rounded-lg border p-4"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h3 class="text-base font-semibold">
                                {{ step.step_details.position + 1 }}. {{ step.step_details.title }}
                            </h3>
                            <span class="rounded border px-2 py-0.5 text-xs capitalize">
                                {{ step.step_details.status }}
                            </span>
                        </div>

                        <p class="mt-2 whitespace-pre-wrap text-sm">
                            {{ step.step_details.description || 'No step description.' }}
                        </p>

                        <div class="mt-3 grid gap-2 text-sm md:grid-cols-2">
                            <div>
                                <p class="text-muted-foreground">Has Cost</p>
                                <p class="font-medium">{{ step.additional_details.has_cost ? 'Yes' : 'No' }}</p>
                            </div>
                            <div>
                                <p class="text-muted-foreground">Cost Amount</p>
                                <p class="font-medium">{{ step.additional_details.cost_amount ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <p class="text-sm font-medium">Step Fields</p>
                            <div v-if="step.fields.length === 0" class="mt-1 text-sm text-muted-foreground">
                                No fields configured.
                            </div>
                            <div v-else class="mt-2 space-y-2">
                                <div
                                    v-for="field in step.fields"
                                    :key="field.id"
                                    class="rounded border p-2 text-sm"
                                >
                                    <Label class="mb-1 block">
                                        {{ field.label }}
                                        <span v-if="field.required" class="text-red-500">*</span>
                                    </Label>

                                    <Textarea
                                        v-if="field.type === 'textarea' || field.type === 'Textarea'"
                                        v-model="stepFieldAnswers[field.id]"
                                        placeholder="Enter your answer..."
                                        class="min-h-[80px]"
                                    />

                                    <Input
                                        v-else-if="field.type === 'number'"
                                        v-model="stepFieldAnswers[field.id]"
                                        type="number"
                                        placeholder="Enter number..."
                                    />

                                    <Input
                                        v-else-if="field.type === 'date'"
                                        v-model="stepFieldAnswers[field.id]"
                                        type="date"
                                    />

                                    <Select
                                        v-else-if="field.type === 'select'"
                                        :model-value="String(stepFieldAnswers[field.id] ?? '')"
                                        @update:model-value="(value) => (stepFieldAnswers[field.id] = value)"
                                    >
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select an option" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="option in (field.options ?? [])"
                                                    :key="String(option)"
                                                    :value="String(option)"
                                                >
                                                    {{ String(option) }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>

                                    <div v-else-if="field.type === 'checkbox' || field.type === 'Checkbox'" class="flex items-center gap-2">
                                        <Checkbox
                                            :checked="Boolean(stepFieldAnswers[field.id])"
                                            @update:checked="(checked) => (stepFieldAnswers[field.id] = Boolean(checked))"
                                        />
                                        <span class="text-xs text-muted-foreground">Check if applicable</span>
                                    </div>

                                    <Input
                                        v-else-if="field.type === 'file'"
                                        type="file"
                                    />

                                    <Input
                                        v-else
                                        v-model="stepFieldAnswers[field.id]"
                                        type="text"
                                        placeholder="Enter your answer..."
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
