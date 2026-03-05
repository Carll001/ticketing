<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import { Search } from 'lucide-vue-next';


import AppLayout from '@/layouts/AppLayout.vue';    

</script>
<template>

    <Head title="Task" />
    <AppLayout >
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex justify-between gap-4">
                <div class="relative w-120">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input class="pl-10" placeholder="Search..." />
                </div>
                <div>
                    <Button>Create</Button>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <TaskCard />
            </div>
            <!-- pagination -->
            <div class="border-t px-6 py-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm">
                        tasks</p>
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" 
                           >Previous</Button>
                        <Button variant="outline" size="sm"
                           >Next</Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>