<script setup lang="ts">
import { computed, ref } from 'vue';

import Heading from '@/components/Heading.vue';

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
import { Textarea } from '@/components/ui/textarea';

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

import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command';

import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

import { Checkbox } from '@/components/ui/checkbox';

import InputError from '../InputError.vue';
import Separator from '../ui/separator/Separator.vue';

import { cn } from '@/lib/utils';

import {
  ChevronDownIcon,
  Check,
  ChevronsUpDown,
  Plus,
  Trash2Icon,
  X,
} from 'lucide-vue-next';

/** -------------------------
 *  UI toggles
 *  ------------------------- */
const showStepForm = ref(false);

function openStepForm() {
  showStepForm.value = true;
}
function closeStepForm() {
  showStepForm.value = false;
}

/** -------------------------
 *  Dummy data
 *  ------------------------- */
type Department = { id: string; name: string };
type User = { id: string; name: string; departmentId?: string };
type Preset = { id: string; name: string; type: 'preset' | 'custom' };

const departments = ref<Department[]>([
  { id: 'dep_any', name: 'Anyone' },
  { id: 'dep_hr', name: 'HR' },
  { id: 'dep_fin', name: 'Finance' },
  { id: 'dep_it', name: 'IT' },
  { id: 'dep_ops', name: 'Operations' },
]);

const users = ref<User[]>([
  { id: 'u_any', name: 'Anyone' },
  { id: 'u_1', name: 'Rommel Aniciete', departmentId: 'dep_it' },
  { id: 'u_2', name: 'Alyssa Cruz', departmentId: 'dep_hr' },
  { id: 'u_3', name: 'Marco Reyes', departmentId: 'dep_fin' },
  { id: 'u_4', name: 'Jessa Lim', departmentId: 'dep_ops' },
]);

const presets = ref<Preset[]>([
  { id: 'p_1', name: 'Onboarding Checklist', type: 'preset' },
  { id: 'p_2', name: 'Device Request Flow', type: 'preset' },
  { id: 'p_3', name: 'Simple Custom Step', type: 'custom' },
]);

/** -------------------------
 *  Task form state
 *  ------------------------- */
const taskTitle = ref('');
const taskDescription = ref('');
const dueDate = ref<any>(null);

const stepOrder = ref<'sequential' | 'random' | ''>('');

/** TASK additional details now contains these: */
const taskType = ref<'preset' | 'custom'>('preset');
const selectedPresetId = ref<string>('');

/** assign department */
const deptOpen = ref(false);
const selectedDepartmentId = ref<string>('dep_any');
const departmentQuery = ref('');

const selectedDepartment = computed(() => {
  return departments.value.find((d) => d.id === selectedDepartmentId.value) ?? null;
});

const filteredDepartments = computed(() => {
  const q = departmentQuery.value.trim().toLowerCase();
  if (!q) return departments.value;
  return departments.value.filter((d) => d.name.toLowerCase().includes(q));
});

function selectDepartment(id: string) {
  selectedDepartmentId.value = id;
  deptOpen.value = false;
}

/** assign user (moved to TASK additional details) */
const userOpen = ref(false);
const selectedUserId = ref<string>('u_any');
const userQuery = ref('');

const assignedUser = computed(() => {
  return users.value.find((u) => u.id === selectedUserId.value) ?? null;
});

const filteredUsers = computed(() => {
  const q = userQuery.value.trim().toLowerCase();
  if (!q) return users.value;
  return users.value.filter((u) => u.name.toLowerCase().includes(q));
});

function selectUser(id: string) {
  selectedUserId.value = id;
  userOpen.value = false;
}

/** add another (moved to TASK additional details) */
const addAnother = ref(false);

/** -------------------------
 *  Step form state
 *  ------------------------- */
const stepTitle = ref('');
const stepDescription = ref('');

/** has cost remains in STEP additional details */
const hasCost = ref(false);
const costAmount = ref<number | ''>('');

type FieldType = 'Textarea' | 'Checkbox' | 'SmallInput';
type StepField = { id: string; type: FieldType; label: string };

const stepFields = ref<StepField[]>([]);

function uid() {
  return Math.random().toString(36).slice(2, 10);
}

function addField(type: FieldType) {
  stepFields.value.push({
    id: uid(),
    type,
    label:
      type === 'Checkbox'
        ? 'Confirm / Approve?'
        : type === 'SmallInput'
          ? 'Enter value'
          : 'Provide details',
  });
}

function removeField(id: string) {
  stepFields.value = stepFields.value.filter((f) => f.id !== id);
}

/** -------------------------
 *  Actions (dummy submit)
 *  ------------------------- */
function discardTask() {
  taskTitle.value = '';
  taskDescription.value = '';
  dueDate.value = null;

  stepOrder.value = '';
  taskType.value = 'preset';
  selectedPresetId.value = '';

  selectedDepartmentId.value = 'dep_any';
  departmentQuery.value = '';

  selectedUserId.value = 'u_any';
  userQuery.value = '';

  addAnother.value = false;
}

function onTaskSubmit() {
  console.log('TASK SUBMIT', {
    taskTitle: taskTitle.value,
    taskDescription: taskDescription.value,
    dueDate: dueDate.value,
    stepOrder: stepOrder.value,

    taskType: taskType.value,
    selectedPresetId: selectedPresetId.value,

    department: selectedDepartment.value,
    assignedUser: assignedUser.value,
    addAnother: addAnother.value,
  });
}

function onStepSubmit() {
  console.log('STEP SUBMIT', {
    stepTitle: stepTitle.value,
    stepDescription: stepDescription.value,
    stepFields: stepFields.value,
    hasCost: hasCost.value,
    costAmount: costAmount.value,
  });

  if (addAnother.value) {
    // keep open, reset step only
    stepTitle.value = '';
    stepDescription.value = '';
    stepFields.value = [];
    hasCost.value = false;
    costAmount.value = '';
    return;
  }

  closeStepForm();
}

const dueDateLabel = computed(() => {
  if (!dueDate.value) return 'Pick a date';
  try {
    if (dueDate.value instanceof Date) return dueDate.value.toDateString();
    if (typeof dueDate.value?.toDate === 'function')
      return dueDate.value.toDate('UTC').toDateString();
    return String(dueDate.value);
  } catch {
    return String(dueDate.value);
  }
});
</script>

<template>
  <div class="space-y-6">
    <!-- TASK FORM -->
    <form @submit.prevent="onTaskSubmit" class="space-y-4">
      <section class="flex justify-between">
        <div class="flex gap-2">
          <Heading title="Add Task" />
        </div>

        <div class="flex items-center gap-2">
          <Button size="sm" type="button" variant="destructive" @click="discardTask">
            Discard
          </Button>
          <Button size="sm" type="submit">Create</Button>
        </div>
      </section>

      <section class="grid grid-cols-[3fr_2fr] gap-4">
        <!-- LEFT: Task details -->
        <Card>
          <CardHeader>
            <CardTitle>Task Details</CardTitle>
            <CardDescription>Description of task</CardDescription>
          </CardHeader>

          <CardContent>
            <div class="space-y-4">
              <section class="flex items-center gap-4">
                <div class="w-full">
                  <Label for="task-title">
                    Task title <span class="text-lg text-red-500">*</span>
                  </Label>

                  <Input id="task-title" v-model="taskTitle" placeholder="task title" />
                  <InputError />
                </div>

                <div class="flex flex-col gap-3">
                  <Label for="date" class="px-1">
                    Due date <span class="text-xs text-muted-foreground">(Optional)</span>
                  </Label>

                  <Popover>
                    <PopoverTrigger as-child>
                      <Button
                        id="date"
                        variant="outline"
                        class="w-48 justify-between font-normal"
                        type="button"
                      >
                        <span class="truncate">{{ dueDateLabel }}</span>
                        <ChevronDownIcon />
                      </Button>
                    </PopoverTrigger>

                    <PopoverContent class="w-auto overflow-hidden p-0" align="start">
                      <Calendar v-model="dueDate" layout="month-and-year" />
                    </PopoverContent>
                  </Popover>
                </div>
              </section>

              <section class="space-y-4">
                <Label for="task-description">
                  Task description <span class="text-xs text-muted-foreground">(Optional)</span>
                </Label>

                <Textarea
                  id="task-description"
                  v-model="taskDescription"
                  placeholder="task description"
                />
                <InputError />
              </section>
            </div>
          </CardContent>
        </Card>

        <!-- RIGHT: Task additional details (moved here) -->
        <Card class="h-fit">
          <CardHeader>
            <CardTitle>Additional details</CardTitle>
            <CardDescription>additional details</CardDescription>
          </CardHeader>

          <CardContent class="space-y-4">
            <!-- step order -->
            <div class="space-y-2">
              <Label for="step-order">
                Select a order <span class="text-lg text-red-500">*</span>
              </Label>

              <Select v-model="stepOrder">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="Select a order" />
                </SelectTrigger>

                <SelectContent>
                  <SelectGroup>
                    <SelectLabel>Task order</SelectLabel>
                    <SelectItem value="sequential">Sequential</SelectItem>
                    <SelectItem value="random">Random</SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </div>

            <Separator class="my-2" />

            <!-- task type -->
            <div class="space-y-2">
              <Label for="task-type">Task type</Label>

              <Select v-model="taskType">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="Select a Type" />
                </SelectTrigger>

                <SelectContent>
                  <SelectItem value="preset">Preset</SelectItem>
                  <SelectItem value="custom">Custom</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <!-- preset -->
            <div
              v-if="taskType === 'preset'"
              class="animate-in space-y-2 fade-in slide-in-from-top-1"
            >
              <Label for="preset-selection">Choose Preset</Label>

              <Select v-model="selectedPresetId" class="w-full">
                <SelectTrigger class="w-full">
                  <SelectValue placeholder="Select a template..." />
                </SelectTrigger>

                <SelectContent>
                  <SelectItem
                    v-for="p in presets.filter((x) => x.type === 'preset')"
                    :key="p.id"
                    :value="p.id"
                  >
                    {{ p.name }}
                  </SelectItem>
                </SelectContent>
              </Select>

              <p class="text-[10px] text-zinc-500 italic">
                Selecting a preset will populate the input fields automatically.
              </p>
            </div>

            <Separator class="my-2" />

            <!-- assign department -->
            <div class="space-y-2">
              <Label>Assign department</Label>

              <Popover v-model:open="deptOpen">
                <PopoverTrigger as-child>
                  <Button type="button" variant="outline" role="combobox" class="w-full justify-between">
                    <span class="truncate">{{ selectedDepartment?.name ?? 'Anyone' }}</span>
                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                  </Button>
                </PopoverTrigger>

                <PopoverContent class="w-full p-0">
                  <Command>
                    <CommandInput v-model="departmentQuery" placeholder="Search department..." />
                    <CommandEmpty>No department found.</CommandEmpty>

                    <CommandList>
                      <CommandGroup>
                        <CommandItem
                          v-for="dep in filteredDepartments"
                          :key="dep.id"
                          :value="dep.name"
                          @select="() => selectDepartment(dep.id)"
                        >
                          <Check
                            :class="cn(
                              'mr-2 h-4 w-4',
                              selectedDepartmentId === dep.id ? 'opacity-100' : 'opacity-0'
                            )"
                          />
                          {{ dep.name }}
                        </CommandItem>
                      </CommandGroup>
                    </CommandList>
                  </Command>
                </PopoverContent>
              </Popover>
            </div>

            <!-- assign user -->
            <div class="space-y-2">
              <Label>Assign to</Label>

              <Popover v-model:open="userOpen">
                <PopoverTrigger as-child>
                  <Button type="button" variant="outline" role="combobox" class="w-full justify-between">
                    <span class="truncate">{{ assignedUser?.name ?? 'Anyone' }}</span>
                    <ChevronsUpDown class="opacity-50" />
                  </Button>
                </PopoverTrigger>

                <PopoverContent class="w-full p-0">
                  <Command>
                    <CommandInput v-model="userQuery" class="h-9" placeholder="Search user..." />
                    <CommandList>
                      <CommandEmpty>No user found.</CommandEmpty>

                      <CommandGroup>
                        <CommandItem
                          v-for="u in filteredUsers"
                          :key="u.id"
                          :value="u.name"
                          @select="() => selectUser(u.id)"
                        >
                          <Check
                            :class="cn(
                              'mr-2 h-4 w-4',
                              selectedUserId === u.id ? 'opacity-100' : 'opacity-0'
                            )"
                          />
                          {{ u.name }}
                        </CommandItem>
                      </CommandGroup>
                    </CommandList>
                  </Command>
                </PopoverContent>
              </Popover>
            </div>

            <Separator class="my-2" />

            <!-- add another (moved here) -->
            <div class="space-y-2">
              <div class="flex items-center gap-3">
                <Checkbox id="add-new" v-model:checked="addAnother" />
                <Label for="add-new">Add another</Label>
              </div>
              <p class="text-sm text-zinc-500">
                Keep checked to stay on this form and quickly add another step.
              </p>
            </div>
          </CardContent>
        </Card>
      </section>
    </form>

    <!-- STEP SECTION (HIDDEN UNTIL CLICK) -->
    <section class="space-y-3">
      <div class="flex items-center justify-between">
        <Heading title="Task Steps" />

        <Button type="button" size="sm" class="gap-2" @click="openStepForm">
          <Plus class="h-4 w-4" /> Add step
        </Button>
      </div>

      <!-- STEP FORM -->
      <div v-if="showStepForm" class="space-y-2">
        <form class="space-y-2" @submit.prevent="onStepSubmit">
          <section class="grid grid-cols-[2fr_1fr] gap-4">
            <!-- LEFT: Step details -->
            <Card>
              <CardHeader>
                <CardTitle>Step Details</CardTitle>
                <CardDescription>Description of step</CardDescription>
              </CardHeader>

              <CardContent>
                <div class="space-y-4">
                  <section class="flex items-start gap-4">
                    <div class="w-full">
                      <Label for="step-title">
                        Step title <span class="text-lg text-red-500">*</span>
                      </Label>

                      <Input id="step-title" v-model="stepTitle" placeholder="step title" />
                      <InputError />
                    </div>
                  </section>

                  <section class="space-y-4">
                    <Label for="step-description">Step description</Label>
                    <Textarea
                      id="step-description"
                      v-model="stepDescription"
                      placeholder="step description"
                    />
                    <InputError />
                  </section>
                </div>

                <!-- fields builder -->
                <div class="mt-6 space-y-6">
                  <div class="flex items-center justify-between border-b pb-2">
                    <div>
                      <Label class="text-base font-semibold">User Input Fields</Label>
                      <p class="text-xs text-zinc-500">
                        Define what information the User must provide.
                      </p>
                    </div>

                    <DropdownMenu>
                      <DropdownMenuTrigger as-child>
                        <Button type="button" size="sm" variant="outline" class="gap-2">
                          <Plus class="h-4 w-4" /> Add Field
                        </Button>
                      </DropdownMenuTrigger>

                      <DropdownMenuContent align="end">
                        <DropdownMenuItem @click="addField('Textarea')">
                          Textarea (Description)
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="addField('Checkbox')">
                          Checkbox (Confirmation)
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="addField('SmallInput')">
                          Small Input (Text/Number)
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </div>

                  <div
                    v-if="stepFields.length === 0"
                    class="rounded-lg border-2 border-dashed border-zinc-800 p-6 text-center"
                  >
                    <p class="text-sm text-zinc-500">
                      No input fields added. The User will just mark this step as complete.
                    </p>
                  </div>

                  <div v-else class="space-y-4">
                    <div
                      v-for="field in stepFields"
                      :key="field.id"
                      class="relative space-y-3 rounded-xl border border-zinc-300 p-4"
                    >
                      <div class="flex items-center justify-between">
                        <div class="mr-4 flex-1">
                          <Label class="mb-1 block text-xs font-bold uppercase">
                            Field Label / Question
                          </Label>
                          <Input
                            v-model="field.label"
                            placeholder="e.g. Check if confirmed / Enter details"
                          />
                        </div>

                        <Button
                          type="button"
                          variant="ghost"
                          size="icon"
                          class="h-8 w-8 shrink-0 text-zinc-500 hover:text-red-500"
                          @click="removeField(field.id)"
                        >
                          <Trash2Icon class="h-4 w-4" />
                        </Button>
                      </div>

                      <div class="mt-4 border-t pt-4 opacity-80">
                        <p class="mb-2 text-xs font-bold uppercase">User Response Preview</p>

                        <div
                          :class="cn(
                            'flex gap-3',
                            field.type === 'Checkbox'
                              ? 'flex-row items-center'
                              : 'flex-col items-start'
                          )"
                        >
                          <div :class="cn(field.type === 'Checkbox' ? 'w-auto' : 'w-full')">
                            <Checkbox v-if="field.type === 'Checkbox'" class="rounded-sm" />

                            <Input
                              v-else-if="field.type === 'SmallInput'"
                              disabled
                              :placeholder="`User will enter ${field.label || 'data'}...`"
                              class="h-8 text-xs"
                            />

                            <Textarea
                              v-else
                              disabled
                              placeholder="User will provide..."
                              class="min-h-[60px] resize-none text-xs"
                            />
                          </div>

                          <span class="text-xs text-zinc-500">Type: {{ field.type }}</span>
                        </div>
                      </div>

                      <InputError />
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- RIGHT: Step additional details (ONLY has cost remains) -->
            <Card class="h-fit">
              <CardHeader>
                <CardTitle>Additional details</CardTitle>
                <CardDescription>additional details</CardDescription>
              </CardHeader>

              <CardContent>
                <section class="space-y-4">
                  <div class="space-y-2">
                    <div class="flex items-center gap-3">
                      <Checkbox id="has-cost" v-model:checked="hasCost" />
                      <Label for="has-cost">has cost</Label>
                    </div>
                    <p class="text-sm text-zinc-500">
                      Check this if the step has a cost; you can enter an amount below.
                    </p>

                    <div v-if="hasCost" class="space-y-2">
                      <Label class="mb-1 block text-[10px] font-bold text-zinc-500 uppercase">
                        Cost amount
                      </Label>
                      <Input
                        v-model="costAmount"
                        type="number"
                        placeholder="Enter cost amount..."
                        class="h-8 text-xs"
                      />
                    </div>
                  </div>
                </section>
              </CardContent>
            </Card>
          </section>
        </form>
      </div>
    </section>
  </div>
</template>