<script setup lang="ts">
import type { ProgressRootProps } from 'reka-ui'
import type { HTMLAttributes } from 'vue'
import { computed } from 'vue'
import { ProgressIndicator, ProgressRoot } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = withDefaults(
    defineProps<
        ProgressRootProps & {
            class?: HTMLAttributes['class']
            indicatorClass?: HTMLAttributes['class']
            modelValue?: number
        }
    >(),
    {
        modelValue: 0,
    },
)

const indicatorStyle = computed(() => ({
    transform: `translateX(-${100 - (props.modelValue ?? 0)}%)`,
}))
</script>

<template>
    <ProgressRoot
        data-slot="progress"
        :model-value="props.modelValue"
        :class="cn('bg-primary/20 relative h-2 w-full overflow-hidden rounded-full', props.class)"
    >
        <ProgressIndicator
            data-slot="progress-indicator"
            :class="cn('bg-primary h-full w-full flex-1 transition-all', props.indicatorClass)"
            :style="indicatorStyle"
        />
    </ProgressRoot>
</template>
