<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

type ShowPreset = {
  id: string
  name: string
  description?: string | null
  department?: { id?: string; name?: string } | null
  steps?: Array<{
    id: string
    title: string
    description?: string | null
    fields?: Array<{ id: string; label: string; type: string }>
  }>
}

const props = defineProps<{
  preset: { data: ShowPreset } | ShowPreset
}>()

const preset = 'data' in props.preset ? props.preset.data : props.preset
</script>

<template>
  <Head :title="`Preset: ${preset.name}`" />

  <AppLayout>
    <div class="flex flex-col gap-4 p-4">
      <Card>
        <CardHeader>
          <CardTitle>{{ preset.name }}</CardTitle>
        </CardHeader>
        <CardContent class="space-y-3">
          <p class="text-sm text-muted-foreground">{{ preset.description || 'No description' }}</p>
          <p class="text-sm">
            Department:
            <span class="font-medium">{{ preset.department?.name || 'Open for anyone' }}</span>
          </p>
          <div class="space-y-2">
            <p class="text-sm font-medium">Steps</p>
            <div v-for="(step, idx) in preset.steps || []" :key="step.id" class="rounded-md border p-3">
              <p class="text-sm font-medium">Step {{ idx + 1 }}: {{ step.title }}</p>
              <p class="text-xs text-muted-foreground">{{ step.description || 'No description' }}</p>
              <div class="mt-2 space-y-1">
                <p v-for="field in step.fields || []" :key="field.id" class="text-xs">
                  {{ field.label }} ({{ field.type }})
                </p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
