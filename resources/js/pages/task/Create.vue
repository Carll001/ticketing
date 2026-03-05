<script setup lang="ts">
import { computed, reactive, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Plus } from 'lucide-vue-next'

import TaskInfoCard from '@/components/task-components/builder/TaskInfoCard.vue'
import StepsBuilder from '@/components/task-components/builder/StepsBuilder.vue'
import LivePreview from '@/components/task-components/builder/LivePreview.vue'
import FieldDialog from '@/components/task-components/builder/FieldDialog.vue'

import type { Department } from '@/types/department'
import { StepField, StepOrder, TaskStep } from '@/types/task-builder'

const props = defineProps<{
  departments: { data: Department[] }
}>()

const uid = () =>
  crypto.randomUUID?.() ??
  `${Date.now().toString(36)}-${Math.random().toString(36).slice(2)}`

const form = reactive({
  title: '',
  description: '',
  department_id: '',
  step_order: 'sequential' as StepOrder,
})

const steps = ref<TaskStep[]>([
  {
    id: uid(),
    title: 'Step 1',
    description: '',
    fields: [{ id: uid(), type: 'input', label: 'Student ID', required: true, placeholder: 'Enter student ID' }],
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
  if (!form.department_id) return false
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

const addFieldToActiveStep = (field: Omit<StepField, 'id'>) => {
  if (!activeStepId.value) return
  const step = steps.value.find(s => s.id === activeStepId.value)
  if (!step) return
  step.fields.push({ id: uid(), ...field })
}

const submit = () => {
  const payload = {
    ...form,
    steps: steps.value.map((s, idx) => ({ ...s, order: idx + 1 })),
  }
  console.log(payload)
  // router.post(route('tasks.store'), payload)
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

      <div class="grid gap-4 lg:grid-cols-[1fr_420px]">
        <div class="space-y-4">
          <TaskInfoCard
            v-model:title="form.title"
            v-model:description="form.description"
            v-model:departmentId="form.department_id"
            v-model:stepOrder="form.step_order"
            :departments="props.departments.data"
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

        <LivePreview
          :title="form.title"
          :description="form.description"
          :department-id="form.department_id"
          :steps="steps"
          :can-save="canSaveTask"
          @save="submit"
        />
      </div>

      <FieldDialog
        v-model:open="fieldDialogOpen"
        @add="addFieldToActiveStep"
      />
    </div>
  </AppLayout>
</template>