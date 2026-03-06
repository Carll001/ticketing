<script setup lang="ts">
import { computed, reactive, ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Plus } from 'lucide-vue-next'

import StepsBuilder from '@/components/task-components/builder/StepsBuilder.vue'
import FieldDialog from '@/components/task-components/builder/FieldDialog.vue'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

import taskPreset from '@/routes/taskPreset'
import type { Department } from '@/types/department'
import { StepField, TaskStep } from '@/types/task-builder'

type NewFieldInput = Omit<StepField, 'id'>
const OPEN_TO_ANYONE = '__public__'

const props = defineProps<{
  departments: { data: Department[] }
}>()

const uid = () =>
  crypto.randomUUID?.() ??
  `${Date.now().toString(36)}-${Math.random().toString(36).slice(2)}`

const form = reactive({
  name: '',
  description: '',
  department_id: OPEN_TO_ANYONE,
})

const steps = ref<TaskStep[]>([
  {
    id: uid(),
    title: 'Step 1',
    description: '',
    fields: [],
    allowComments: false,
    allow_proof: false,
    has_cost: false,
    cost: undefined,
  },
])

const addStep = () => {
  steps.value.push({
    id: uid(),
    title: `Step ${steps.value.length + 1}`,
    description: '',
    fields: [],
    allowComments: false,
    allow_proof: false,
    has_cost: false,
    cost: undefined,
  })
}

const removeStep = (stepId: string) => {
  steps.value = steps.value.filter(s => s.id !== stepId)
}

const moveStep = (from: number, to: number) => {
  if (to < 0 || to >= steps.value.length) return
  const copy = [...steps.value]
  const [item] = copy.splice(from, 1)
  copy.splice(to, 0, item)
  steps.value = copy
}

const removeField = (stepId: string, fieldId: string) => {
  const step = steps.value.find(s => s.id === stepId)
  if (!step) return
  step.fields = step.fields.filter(f => f.id !== fieldId)
}

const canSavePreset = computed(() => {
  if (!form.name.trim()) return false
  if (steps.value.length === 0) return false
  return steps.value.every(s => s.title.trim().length > 0)
})

const fieldDialogOpen = ref(false)
const activeStepId = ref<string | null>(null)

const openAddField = (stepId: string) => {
  activeStepId.value = stepId
  fieldDialogOpen.value = true
}

const addFieldToActiveStep = (field: NewFieldInput) => {
  if (!activeStepId.value) return
  const step = steps.value.find(s => s.id === activeStepId.value)
  if (!step) return
  step.fields.push({ id: uid(), ...field })
}

const addBulkFieldsToActiveStep = (fields: NewFieldInput[]) => {
  if (!activeStepId.value) return
  const step = steps.value.find(s => s.id === activeStepId.value)
  if (!step) return

  fields.forEach((field) => {
    step.fields.push({ id: uid(), ...field })
  })
}

const submit = () => {
  const payload = {
    name: form.name,
    description: form.description || null,
    department_id: form.department_id === OPEN_TO_ANYONE ? null : form.department_id,
    steps: steps.value.map((s) => ({
      title: s.title,
      description: s.description || null,
      allow_proof: s.allow_proof,
      allowComments: s.allowComments,
      has_cost: s.has_cost,
      cost: s.has_cost ? (s.cost ?? null) : null,
      fields: s.fields.map((f) => ({
        label: f.label,
        type: f.type,
        placeholder: f.placeholder ?? null,
        required: false,
      })),
    })),
  }

  router.post(taskPreset.store().url, payload)
}
</script>

<template>
  <Head title="Create Task Preset" />

  <AppLayout>
    <div class="flex flex-col gap-4 p-4">
      <div class="flex items-start justify-between gap-3">
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">Create Task Preset</h1>
          <p class="text-sm text-muted-foreground">
            Build reusable steps and fields for future tasks.
          </p>
        </div>

        <div class="flex items-center gap-2">
          <Button variant="secondary" type="button" @click="addStep">
            <Plus class="mr-2 h-4 w-4" />
            Add Step
          </Button>
          <Button type="button" :disabled="!canSavePreset" @click="submit">
            Save Preset
          </Button>
        </div>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Preset Information</CardTitle>
          <CardDescription>Basic preset details.</CardDescription>
        </CardHeader>
        <CardContent class="grid gap-4 md:grid-cols-2">
          <div class="space-y-2 md:col-span-2">
            <Label>Name</Label>
            <Input v-model="form.name" placeholder="e.g., Student Registration" />
          </div>

          <div class="space-y-2 md:col-span-2">
            <Label>Description</Label>
            <Textarea
              v-model="form.description"
              placeholder="Describe when this preset should be used"
              class="min-h-[96px]"
            />
          </div>

          <div class="space-y-2">
            <div class="space-y-1">
              <Label>Assign Department (Optional)</Label>
              <p class="text-xs text-muted-foreground">If empty, this preset is reusable for any department.</p>
            </div>
            <Select v-model="form.department_id">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Open to Anyone" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem :value="OPEN_TO_ANYONE">Open to Anyone</SelectItem>
                <SelectItem v-for="d in props.departments.data" :key="d.id" :value="String(d.id)">
                  {{ d.name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      <StepsBuilder
        v-model:steps="steps"
        @add-step="addStep"
        @remove-step="removeStep"
        @move-step="moveStep"
        @add-field="openAddField"
        @remove-field="removeField"
      />

      <FieldDialog
        v-model:open="fieldDialogOpen"
        @add="addFieldToActiveStep"
        @add-bulk="addBulkFieldsToActiveStep"
      />
    </div>
  </AppLayout>
</template>
