<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
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
import { Trash2 } from 'lucide-vue-next'

type FieldType = 'input' | 'textarea' | 'checkbox'
type FieldInput = { type: FieldType; label: string; placeholder?: string; required: boolean }
type BulkRow = { id: string; label: string; type: FieldType; placeholder: string }

const open = defineModel<boolean>('open', { required: true })

const emit = defineEmits<{
  (e: 'add', field: FieldInput): void
  (e: 'add-bulk', fields: FieldInput[]): void
}>()

const mode = ref<'single' | 'bulk'>('single')
const draft = reactive({
  type: 'input' as FieldType,
  label: '',
  placeholder: '',
})
const bulkRows = ref<BulkRow[]>([])
const bulkCountInput = ref('5')
const bulkTypeToAdd = ref<FieldType>('input')

const uid = () =>
  crypto.randomUUID?.() ??
  `${Date.now().toString(36)}-${Math.random().toString(36).slice(2)}`

const createBulkRow = (type: FieldType = 'input'): BulkRow => ({
  id: uid(),
  label: '',
  type,
  placeholder: '',
})

watch(open, (v) => {
  if (!v) return
  mode.value = 'single'
  draft.type = 'input'
  draft.label = ''
  draft.placeholder = ''
  bulkRows.value = [createBulkRow()]
  bulkCountInput.value = '5'
  bulkTypeToAdd.value = 'input'
})

const parsedBulk = computed(() => {
  const valid = bulkRows.value
    .map((row) => {
      const label = row.label.trim()
      if (!label) return null
      return {
        type: row.type,
        label,
        required: false,
        placeholder: row.type === 'checkbox' ? undefined : (row.placeholder.trim() || undefined),
      } satisfies FieldInput
    })
    .filter((item): item is FieldInput => item !== null)

  return {
    valid,
    total: bulkRows.value.length,
    skipped: bulkRows.value.length - valid.length,
  }
})

const addBulkRow = () => {
  bulkRows.value.push(createBulkRow(bulkTypeToAdd.value))
}

const removeBulkRow = (rowId: string) => {
  if (bulkRows.value.length === 1) {
    bulkRows.value = [createBulkRow(bulkTypeToAdd.value)]
    return
  }
  bulkRows.value = bulkRows.value.filter(row => row.id !== rowId)
}

const generateBulkRows = () => {
  const count = Number.parseInt(bulkCountInput.value, 10)
  if (Number.isNaN(count)) return
  const safeCount = Math.max(1, Math.min(count, 50))
  const generated = Array.from({ length: safeCount }, () => createBulkRow(bulkTypeToAdd.value))
  bulkRows.value = [...bulkRows.value, ...generated]
}

const addSingle = () => {
  const label = draft.label.trim()
  if (!label) return

  emit('add', {
    type: draft.type,
    label,
    required: false,
    placeholder: draft.type === 'checkbox' ? undefined : (draft.placeholder.trim() || undefined),
  })

  open.value = false
}

const addBulk = () => {
  if (parsedBulk.value.valid.length === 0) return
  emit('add-bulk', parsedBulk.value.valid)
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
          <Label>Mode</Label>
          <div class="flex items-center gap-2">
            <Button
              type="button"
              size="sm"
              :variant="mode === 'single' ? 'default' : 'outline'"
              @click="mode = 'single'"
            >
              Single
            </Button>
            <Button
              type="button"
              size="sm"
              :variant="mode === 'bulk' ? 'default' : 'outline'"
              @click="mode = 'bulk'"
            >
              Bulk
            </Button>
          </div>
        </div>

        <template v-if="mode === 'single'">
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

        </template>

        <template v-else>
          <div class="flex items-end gap-2">
            <div class="space-y-2">
              <Label>Rows</Label>
              <Input v-model="bulkCountInput" type="number" min="1" max="50" class="w-24" />
            </div>
            <div class="space-y-2">
              <Label>Type</Label>
              <Select v-model="bulkTypeToAdd">
                <SelectTrigger class="w-36">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="input">Input</SelectItem>
                  <SelectItem value="textarea">Textarea</SelectItem>
                  <SelectItem value="checkbox">Checkbox</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <Button type="button" variant="secondary" @click="generateBulkRows">Generate</Button>
            <Button type="button" variant="outline" @click="addBulkRow">Add Row</Button>
          </div>

          <div class="space-y-2">
            <div class="grid grid-cols-[1.4fr_1fr_1.4fr_auto] gap-2 px-1 text-xs font-medium text-muted-foreground">
              <span>Label</span>
              <span>Type</span>
              <span>Placeholder</span>
              <span></span>
            </div>

            <div
              v-for="row in bulkRows"
              :key="row.id"
              class="grid grid-cols-[1.4fr_1fr_1.4fr_auto] items-center gap-2 rounded-md border p-2"
            >
              <Input v-model="row.label" placeholder="e.g., Student Name" />

              <Input :model-value="row.type" disabled />

              <Input
                v-model="row.placeholder"
                placeholder="e.g., Enter value..."
                :disabled="row.type === 'checkbox'"
              />

              <Button type="button" variant="ghost" size="sm" @click="removeBulkRow(row.id)">
                <Trash2/>
              </Button>
            </div>
          </div>

          <div class="rounded-md border px-3 py-2 text-sm">
            <span class="font-medium">{{ parsedBulk.total }}</span> row(s)
            <span class="mx-2 text-muted-foreground">|</span>
            <span class="font-medium">{{ parsedBulk.valid.length }}</span> valid
            <span class="mx-2 text-muted-foreground">|</span>
            <span class="font-medium">{{ parsedBulk.skipped }}</span> skipped (empty label)
          </div>
        </template>
      </div>

      <DialogFooter class="gap-2 sm:gap-0">
        <Button variant="secondary" type="button" @click="open = false">Cancel</Button>
        <Button
          v-if="mode === 'single'"
          type="button"
          @click="addSingle"
          :disabled="!draft.label.trim()"
        >
          Add Field
        </Button>
        <Button
          v-else
          type="button"
          @click="addBulk"
          :disabled="parsedBulk.valid.length === 0"
        >
          Add Fields
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
