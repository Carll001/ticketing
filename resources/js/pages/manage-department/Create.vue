<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { router, useForm } from '@inertiajs/vue3'
import department from '@/routes/department'
import InputError from '@/components/InputError.vue'
import { ref } from 'vue'
const open = ref(false)

const form = useForm({
  name: '',
})

const submit = () => {
  form.post(department.store().url, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      open.value = false
    },
  })
}

</script>

<template>
  <Dialog v-model:open="open">
    <DialogTrigger as-child>
      <Button class="ml-auto">Create Department</Button>
    </DialogTrigger>

    <DialogContent class="space-y-4">
      <DialogHeader>
        <DialogTitle>Create Department</DialogTitle>
      </DialogHeader>

      <form class="space-y-4" @submit.prevent="submit">
        <div class="space-y-2">
          <Label>Name</Label>
          <Input v-model="form.name" placeholder="Enter department name" />
          <InputError :message="form.errors.name"/>
        </div>

        <div class="flex justify-end">
          <Button type="submit" :disabled="form.processing">
            Create
          </Button>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>