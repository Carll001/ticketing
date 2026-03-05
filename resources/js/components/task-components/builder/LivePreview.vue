<script setup lang="ts">
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Separator } from '@/components/ui/separator'
import { TaskStep } from '@/types/task-builder'


const props = defineProps<{
  title: string
  description: string
  departmentId: string
  steps: TaskStep[]
  canSave: boolean
}>()

const emit = defineEmits<{ (e: 'save'): void }>()
</script>

<template>
  <Card class="h-fit">
    <CardHeader>
      <CardTitle>Live Preview</CardTitle>
      <CardDescription>How the assignee will complete this task.</CardDescription>
    </CardHeader>

    <CardContent class="space-y-4">
      <div class="rounded-lg border p-3">
        <p class="text-sm font-medium">{{ props.title || 'Untitled Task' }}</p>
        <p class="mt-1 text-xs text-muted-foreground">{{ props.description || 'No description yet.' }}</p>
        <p class="mt-2 text-xs text-muted-foreground">
          Department: <span class="font-medium">{{ props.departmentId || 'Not selected' }}</span>
        </p>
      </div>

      <div class="space-y-3">
        <div v-for="(s, i) in props.steps" :key="s.id" class="rounded-lg border p-3">
          <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
              <p class="truncate text-sm font-medium">Step {{ i + 1 }}: {{ s.title || 'Untitled' }}</p>
              <p class="mt-1 text-xs text-muted-foreground">{{ s.description || 'No instructions.' }}</p>
            </div>
            <Badge variant="secondary">{{ s.fields.length }} fields</Badge>
          </div>

          <Separator class="my-3" />

          <div class="space-y-2">
            <div v-for="f in s.fields" :key="f.id" class="space-y-1">
              <div class="flex items-center gap-2">
                <span class="text-xs font-medium">{{ f.label }}</span>
                <span v-if="f.required" class="text-[10px] text-muted-foreground">(required)</span>
              </div>

              <Input v-if="f.type === 'input'" :placeholder="f.placeholder || 'Answer here...'" disabled />
              <Textarea v-else-if="f.type === 'textarea'" :placeholder="f.placeholder || 'Answer here...'" disabled class="min-h-[72px]" />
              <div v-else class="flex items-center gap-2 rounded-md border px-3 py-2 text-sm text-muted-foreground">
                <span class="inline-flex h-4 w-4 items-center justify-center rounded border"></span>
                <span>Checkbox</span>
              </div>
            </div>
          </div>

          <Separator class="my-3" />

          <div class="space-y-2">
            <p class="text-xs font-medium text-muted-foreground">Proof</p>
            <div class="flex flex-wrap gap-2">
              <Badge :variant="s.allow_proof ? 'default' : 'secondary'">
                {{ s.allow_proof ? 'Assignee may submit proof' : 'No proof' }}
              </Badge>
            </div>
          </div>

          <div class="mt-3 flex items-center justify-between">
            <p class="text-xs text-muted-foreground">Comments</p>
            <Badge :variant="s.allowComments ? 'default' : 'secondary'">
              {{ s.allowComments ? 'Enabled' : 'Disabled' }}
            </Badge>
          </div>

          <Button class="mt-3 w-full" type="button" disabled>Submit Step</Button>
        </div>
      </div>
    </CardContent>

    <CardFooter>
      <Button class="w-full" type="button" :disabled="!props.canSave" @click="emit('save')">
        Save Task
      </Button>
    </CardFooter>
  </Card>
</template>