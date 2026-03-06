<script setup lang="ts">
import { computed } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'
import { ListChecks } from 'lucide-vue-next'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

import type { Department } from '@/types/department'
import { TaskStep } from '@/types/task-builder'
import type { StepOrder } from '@/types/task-builder'

const PUBLIC_DEPARTMENT_VALUE = '__public__'
const NO_PRESET_VALUE = '__none__'

type TaskPresetOption = {
  id: string
  name: string
}

const props = withDefaults(defineProps<{
  departments: Department[]
  steps: TaskStep[]
  presets?: TaskPresetOption[]
}>(), {
  presets: () => [],
})

const title = defineModel<string>('title', { required: true })
const description = defineModel<string>('description', { required: true })
const departmentId = defineModel<string>('departmentId', { required: true })
const stepOrder = defineModel<StepOrder>('stepOrder', { required: true })
const presetId = defineModel<string>('presetId', { required: true })

const totalFields = computed(() => props.steps.reduce((sum, s) => sum + s.fields.length, 0))
const departmentModel = computed({
  get: () => departmentId.value || PUBLIC_DEPARTMENT_VALUE,
  set: (value: string) => {
    departmentId.value = value === PUBLIC_DEPARTMENT_VALUE ? '' : value
  },
})
const presetModel = computed({
  get: () => presetId.value || NO_PRESET_VALUE,
  set: (value: string) => {
    presetId.value = value === NO_PRESET_VALUE ? '' : value
  },
})
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle class="flex items-center gap-2">
        <ListChecks class="h-5 w-5" />
        Task Information
      </CardTitle>
      <CardDescription>
        Basic details. Steps, fields, proof and comments are configured below.
      </CardDescription>
    </CardHeader>

    <CardContent class="grid gap-4 md:grid-cols-2">
      <div class="space-y-2 md:col-span-2">
        <Label>Title</Label>
        <Input v-model="title" placeholder="Enter task title" />
      </div>

      <div class="space-y-2 md:col-span-2">
        <Label>Description</Label>
        <Textarea v-model="description" placeholder="Describe what this task is for" class="min-h-[96px]" />
      </div>
      <div class="grid grid-cols-4 w-full col-span-2 gap-4">
        <div class="space-y-2 w-full">
          <div class="space-y-1">
            <Label>Assign Department (Optional)</Label>
            <p class="text-xs text-muted-foreground">If empty, this task is open to anyone.</p>
          </div>
          <Select v-model="departmentModel">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Select department" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem :value="PUBLIC_DEPARTMENT_VALUE">
                Open to Anyone
              </SelectItem>
              <SelectItem v-for="d in props.departments" :key="d.id" :value="String(d.id)">
                {{ d.name }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>
        <div class="space-y-2 w-full">
          <div class="space-y-1">
            <Label>Preset (Optional)</Label>
            <p class="text-xs text-muted-foreground">Use a reusable task preset if available.</p>
          </div>
          <Select v-model="presetModel">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Select preset" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem :value="NO_PRESET_VALUE">
                No Preset
              </SelectItem>
              <SelectItem v-for="preset in props.presets" :key="preset.id" :value="String(preset.id)">
                {{ preset.name }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>
        <div class="space-y-2 w-full">
          <Label>Step Order</Label>
          <Select v-model="stepOrder">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Select step order" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="sequential">Sequential</SelectItem>
              <SelectItem value="free">Free</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div class="space-y-2 w-full">
          <Label>Summary</Label>
          <div class="flex items-center gap-2 rounded-md border px-3 py-2 text-sm">
            <span class="text-muted-foreground">Steps</span>
            <span class="font-medium">{{ props.steps.length }}</span>
            <Separator orientation="vertical" class="mx-2 h-4" />
            <span class="text-muted-foreground">Fields</span>
            <span class="font-medium">{{ totalFields }}</span>
          </div>
        </div>

      </div>
    </CardContent>
  </Card>
</template>
