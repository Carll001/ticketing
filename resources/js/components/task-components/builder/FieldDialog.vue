<script setup lang="ts">
import { reactive, watch } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Switch } from '@/components/ui/switch'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'

type FieldType = 'input' | 'textarea' | 'checkbox'

const open = defineModel<boolean>('open', { required: true })

const emit = defineEmits<{
  (e: 'add', field: { type: FieldType; label: string; placeholder?: string; required: boolean }): void
}>()

const draft = reactive({
  type: 'input' as FieldType,
  label: '',
  placeholder: '',
  required: false,
})

watch(open, (v) => {
  if (!v) return
  draft.type = 'input'
  draft.label = ''
  draft.placeholder = ''
  draft.required = false
})

const add = () => {
  const label = draft.label.trim()
  if (!label) return

  emit('add', {
    type: draft.type,
    label,
    required: draft.required,
    placeholder: draft.type === 'checkbox' ? undefined : (draft.placeholder.trim() || undefined),
  })

  open.value = false
}
</script>

<template>
  <Dialog v-model:open="open">
    <DialogContent class="sm:max-w-[520px]">
      <DialogHeader>
        <DialogTitle>Add Field</DialogTitle>
        <DialogDescription>Add a field the user must answer for this step.</DialogDescription>
      </DialogHeader>

      <div class="grid gap-4 py-2">
        <div class="space-y-2">
          <Label>Field Type</Label>
          <Select v-model="draft.type">
            <SelectTrigger>
              <SelectValue placeholder="Select type" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="input">Input</SelectItem>
              <SelectItem value="textarea">Textarea</SelectItem>
              <SelectItem value="checkbox">Checkbox</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div class="space-y-2">
          <Label>Label</Label>
          <Input v-model="draft.label" placeholder="e.g., Student Name" />
        </div>

        <div v-if="draft.type !== 'checkbox'" class="space-y-2">
          <Label>Placeholder</Label>
          <Input v-model="draft.placeholder" placeholder="e.g., Enter your answer..." />
        </div>

        <div class="flex items-center justify-between rounded-md border px-3 py-2">
          <div class="flex flex-col">
            <span class="text-sm font-medium">Required</span>
            <span class="text-xs text-muted-foreground">User must provide an answer</span>
          </div>
          <Switch v-model:checked="draft.required" />
        </div>
      </div>

      <DialogFooter class="gap-2 sm:gap-0">
        <Button variant="secondary" type="button" @click="open = false">Cancel</Button>
        <Button type="button" @click="add" :disabled="!draft.label.trim()">Add Field</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>