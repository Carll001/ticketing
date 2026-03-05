<script setup lang="ts">
import { computed } from 'vue'
import { AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'
import { Switch } from '@/components/ui/switch'

import {
  Trash2,
  GripVertical,
  FileUp,
  MessageSquare,
  Plus,
  PencilLine,
  AlignLeft,
  CheckSquare,
} from 'lucide-vue-next'
import { StepField, TaskStep } from '@/types/task-builder'


const props = defineProps<{
  index: number
  total: number
}>()

const step = defineModel<TaskStep>('step', { required: true })

const emit = defineEmits<{
  (e: 'move', toIndex: number): void
  (e: 'remove'): void
  (e: 'add-field'): void
  (e: 'remove-field', fieldId: string): void
}>()

const canRemove = computed(() => props.total > 1)

type FieldType = StepField['type']
const fieldTypeMeta = (t: FieldType) => {
  if (t === 'input') return { label: 'Input', icon: PencilLine }
  if (t === 'textarea') return { label: 'Textarea', icon: AlignLeft }
  return { label: 'Checkbox', icon: CheckSquare }
}

const proofLabel = computed(() => (step.value.allow_proof ? 'Allowed' : 'None'))
</script>

<template>
  <AccordionItem :value="step.id" class="rounded-lg border">
    <div class="flex items-center justify-between px-3">
      <AccordionTrigger class="flex-1">
        <div class="flex w-full items-center gap-3">
          <div class="flex items-center gap-2 text-muted-foreground">
            <GripVertical class="h-4 w-4" />
            <Badge variant="secondary">Step {{ props.index + 1 }}</Badge>
          </div>

          <div class="flex min-w-0 flex-1 flex-col items-start">
            <span class="truncate text-left font-medium">
              {{ step.title || 'Untitled step' }}
            </span>
            <span class="truncate text-left text-xs text-muted-foreground">
              {{ step.fields.length }} field(s) • Proof: {{ proofLabel }} • Comments: {{ step.allowComments ? 'On' : 'Off' }}
            </span>
          </div>
        </div>
      </AccordionTrigger>

      <div class="ml-2 flex items-center gap-1">
        <Button size="icon" variant="ghost" type="button" @click.stop="emit('move', props.index - 1)" :disabled="props.index === 0">
          ▲
        </Button>
        <Button size="icon" variant="ghost" type="button" @click.stop="emit('move', props.index + 1)" :disabled="props.index === props.total - 1">
          ▼
        </Button>
        <Button size="icon" variant="ghost" type="button" @click.stop="emit('remove')" :disabled="!canRemove">
          <Trash2 class="h-4 w-4" />
        </Button>
      </div>
    </div>

    <AccordionContent class="px-3 pb-3">
      <div class="grid gap-4 lg:grid-cols-2">
        <!-- Step details -->
        <div class="space-y-3">
          <div class="space-y-2">
            <Label>Step Title</Label>
            <Input v-model="step.title" placeholder="e.g., Upload requirements" />
          </div>

          <div class="space-y-2">
            <Label>Step Description</Label>
            <Textarea v-model="step.description" placeholder="Instructions for this step" class="min-h-[88px]" />
          </div>

          <Separator />

<div class="flex items-center justify-between rounded-md border px-3 py-2">
  <div class="flex items-center gap-2">
    <FileUp class="h-4 w-4 text-muted-foreground" />
    <div class="flex flex-col">
      <span class="text-sm font-medium">Allow proof</span>
      <span class="text-xs text-muted-foreground">
        Assignee may attach proof (text, image, or file)
      </span>
    </div>
  </div>
  <Switch v-model:checked="step.allow_proof" />
</div>

<Separator />

<div class="space-y-2">
  <div class="flex items-center justify-between rounded-md border px-3 py-2">
    <div class="flex flex-col">
      <span class="text-sm font-medium">Has cost</span>
      <span class="text-xs text-muted-foreground">This step requires a fee</span>
    </div>
    <Switch v-model:checked="step.has_cost" />
  </div>

  <div v-if="step.has_cost" class="space-y-2">
    <Label>Cost</Label>
    <Input
      type="number"
      min="0"
      step="0.01"
      :model-value="step.cost ?? ''"
      @update:model-value="(v) => (step.cost = v === '' ? undefined : Number(v))"
      placeholder="0.00"
    />
  </div>
</div>

          <Separator />

          <div class="flex items-center justify-between rounded-md border px-3 py-2">
            <div class="flex items-center gap-2">
              <MessageSquare class="h-4 w-4 text-muted-foreground" />
              <div class="flex flex-col">
                <span class="text-sm font-medium">Allow discussion</span>
                <span class="text-xs text-muted-foreground">Creator & user can comment on this step</span>
              </div>
            </div>
            <Switch v-model:checked="step.allowComments" />
          </div>
        </div>

        <!-- Fields -->
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium">Fields</p>
              <p class="text-xs text-muted-foreground">What the user must answer in this step.</p>
            </div>

            <Button variant="secondary" size="sm" type="button" @click="emit('add-field')">
              <Plus class="mr-2 h-4 w-4" />
              Add Field
            </Button>
          </div>

          <div v-if="step.fields.length === 0" class="rounded-lg border border-dashed p-4 text-sm text-muted-foreground">
            No fields yet. Add an input, checkbox, or textarea.
          </div>

          <div v-else class="space-y-2">
            <div
              v-for="f in step.fields"
              :key="f.id"
              class="flex items-start justify-between gap-3 rounded-lg border p-3"
            >
              <div class="flex min-w-0 items-start gap-3">
                <component :is="fieldTypeMeta(f.type).icon" class="mt-0.5 h-4 w-4 text-muted-foreground" />
                <div class="min-w-0">
                  <div class="flex flex-wrap items-center gap-2">
                    <p class="truncate text-sm font-medium">{{ f.label }}</p>
                    <Badge variant="secondary" class="text-xs">{{ fieldTypeMeta(f.type).label }}</Badge>
                    <Badge v-if="f.required" class="text-xs">Required</Badge>
                  </div>
                  <p v-if="f.placeholder" class="truncate text-xs text-muted-foreground">
                    Placeholder: {{ f.placeholder }}
                  </p>
                </div>
              </div>

              <Button size="icon" variant="ghost" type="button" @click="emit('remove-field', f.id)">
                <Trash2 class="h-4 w-4" />
              </Button>
            </div>
          </div>
        </div>
      </div>
    </AccordionContent>
  </AccordionItem>
</template>