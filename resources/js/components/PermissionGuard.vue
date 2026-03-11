<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    permission: string;
}>();

const page = usePage();

// Frontend-only visibility guard. Backend authorization must still be enforced server-side.
const canRender = computed(() => {
    const permissions = (page.props.auth?.permissions ?? []) as string[];

    return permissions.includes(props.permission);
});
</script>

<template>
    <slot v-if="canRender" />
</template>
