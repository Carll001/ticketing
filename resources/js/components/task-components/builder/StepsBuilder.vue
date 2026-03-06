<script setup lang="ts">
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Accordion } from '@/components/ui/accordion'
import { Plus } from 'lucide-vue-next'

import StepEditor from './StepEditor.vue'
import { TaskStep } from '@/types/task-builder'

const steps = defineModel<TaskStep[]>('steps', { required: true })

const emit = defineEmits<{
  (e: 'add-step'): void
  (e: 'remove-step', stepId: string): void
  (e: 'move-step', from: number, to: number): void
  (e: 'add-field', stepId: string): void
  (e: 'remove-field', stepId: string, fieldId: string): void
}>()
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Task Steps</CardTitle>
      <CardDescription>Reorder steps and configure per-step fields, proof, and comments.</CardDescription>
    </CardHeader>

    <CardContent class="space-y-3">
      <Accordion type="multiple" class="w-full space-y-4">
        <StepEditor
          v-for="(step, idx) in steps"
          :key="step.id"
          v-model:step="steps[idx]"
          :index="idx"
          :total="steps.length"
          @move="(to) => emit('move-step', idx, to)"
          @remove="() => emit('remove-step', step.id)"
          @add-field="() => emit('add-field', step.id)"
          @remove-field="(fieldId) => emit('remove-field', step.id, fieldId)"
        />
      </Accordion>
    </CardContent>

    <CardFooter class="justify-between">
      <Button variant="secondary" type="button" @click="emit('add-step')">
        <Plus class="mr-2 h-4 w-4" />
        Add Step
      </Button>
      <div class="text-xs text-muted-foreground">Tip: Use ▲ ▼ to reorder steps.</div>
    </CardFooter>
  </Card>
</template>