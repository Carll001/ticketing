<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Plus } from 'lucide-vue-next'

import TaskInfoCard from '@/components/task-components/builder/TaskInfoCard.vue'
import StepsBuilder from '@/components/task-components/builder/StepsBuilder.vue'
import FieldDialog from '@/components/task-components/builder/FieldDialog.vue'
import task from '@/routes/task'

import type { Department } from '@/types/department'
import { StepField, StepOrder, TaskStep } from '@/types/task-builder'

type NewFieldInput = Omit<StepField, 'id'>
type TaskPresetFieldOption = {
  id: string
  label: string
  type: 'text' | 'textarea' | 'checkbox'
  required?: boolean
  placeholder?: string | null
}
type TaskPresetStepOption = {
  id: string
  title: string
  description?: string | null
  allow_proof?: boolean
  allow_comments?: boolean
  has_cost?: boolean
  expected_cost?: number | string | null
  fields?: TaskPresetFieldOption[]
}
type TaskPresetOption = {
  id: string
  name: string
  description?: string | null
  department_id?: string | null
  steps?: TaskPresetStepOption[]
}

const props = defineProps<{
  departments: { data: Department[] }
  presets?: { data: TaskPresetOption[] }
}>()

const uid = () =>
  crypto.randomUUID?.() ??
  `${Date.now().toString(36)}-${Math.random().toString(36).slice(2)}`

const form = reactive({
  title: '',
  description: '',
  department_id: '',
  task_preset_id: '',
  step_order: 'sequential' as StepOrder,
})

const steps = ref<TaskStep[]>([
  {
    id: uid(),
    title: 'Step 1',
    description: '',
    fields: [],
    allowComments: true,

    allow_proof: true,
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
    allowComments: true,

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

const canSaveTask = computed(() => {
  if (!form.title.trim()) return false
  if (steps.value.length === 0) return false

  return steps.value.every(s => {
    if (!s.title.trim()) return false
    if (s.has_cost) {
      if (s.cost == null) return false
      if (Number.isNaN(s.cost)) return false
      if (s.cost < 0) return false
    }
    return true
  })
})

/** Field Dialog state */
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

const mapPresetFieldType = (type: TaskPresetFieldOption['type']): StepField['type'] => {
  if (type === 'textarea') return 'textarea'
  if (type === 'checkbox') return 'checkbox'
  return 'input'
}

watch(
  () => form.task_preset_id,
  (presetId) => {
    if (!presetId) return
    const preset = props.presets?.data?.find((item) => item.id === presetId)
    if (!preset) return

    form.title = preset.name || form.title
    form.description = preset.description || ''
    form.department_id = preset.department_id || ''

    const presetSteps = preset.steps ?? []
    steps.value = presetSteps.length > 0
      ? presetSteps.map((presetStep, index) => ({
          id: uid(),
          title: presetStep.title || `Step ${index + 1}`,
          description: presetStep.description || '',
          fields: (presetStep.fields ?? []).map((field) => ({
            id: uid(),
            label: field.label,
            type: mapPresetFieldType(field.type),
            required: Boolean(field.required),
            placeholder: field.placeholder ?? undefined,
          })),
          allowComments: presetStep.allow_comments !== false,
          allow_proof: Boolean(presetStep.allow_proof),
          has_cost: Boolean(presetStep.has_cost),
          cost: presetStep.expected_cost == null ? undefined : Number(presetStep.expected_cost),
        }))
      : [{
          id: uid(),
          title: 'Step 1',
          description: '',
          fields: [],
          allowComments: true,
          allow_proof: false,
          has_cost: false,
          cost: undefined,
        }]
  }
)
const payload = computed(() => ({
  ...form,
  steps: steps.value.map((s, idx) => ({ ...s, order: idx + 1 })),
}))

const submit = () => {
  router.post(task.store().url, payload.value)
}
</script>

<template>
  <Head title="Create Task" />

  <AppLayout>
    <div class="flex flex-col gap-4 p-4">
      <!-- Header -->
      <div class="flex items-start justify-between gap-3">
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">Create Task</h1>
          <p class="text-sm text-muted-foreground">
            Build a task with steps, custom fields, proof requirements, and step discussion.
          </p>
        </div>

        <div class="flex items-center gap-2">
          <Button variant="secondary" type="button" @click="addStep">
            <Plus class="mr-2 h-4 w-4" />
            Add Step
          </Button>
          <Button type="button" :disabled="!canSaveTask" @click="submit">
            Save Task
          </Button>
        </div>
      </div>

      <div class="grid gap-4">
        <div class="space-y-4">
          <TaskInfoCard
            v-model:title="form.title"
            v-model:description="form.description"
            v-model:departmentId="form.department_id"
            v-model:presetId="form.task_preset_id"
            v-model:stepOrder="form.step_order"
            :departments="props.departments.data"
            :presets="props.presets?.data ?? []"
            :steps="steps"
          />

          <StepsBuilder
            v-model:steps="steps"
            @add-step="addStep"
            @remove-step="removeStep"
            @move-step="moveStep"
            @add-field="openAddField"
            @remove-field="removeField"
          />
        </div>

        <!-- <LivePreview
          :title="form.title"
          :description="form.description"
          :department-id="form.department_id"
          :steps="steps"
          :can-save="canSaveTask"
          @save="submit"
        /> -->
      </div>

      <FieldDialog
        v-model:open="fieldDialogOpen"
        @add="addFieldToActiveStep"
        @add-bulk="addBulkFieldsToActiveStep"
      />
    </div>

    <!-- <pre>{{ payload }}</pre> -->
  </AppLayout>
</template>
