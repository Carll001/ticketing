<script setup lang="ts">
import AuthLayout from '@/layouts/auth/AuthSplitLayout.vue';

defineProps<{
    title?: string;
    description?: string;
    variant?: 'default' | 'welcome-login';
}>();
</script>

<template>
    <AuthLayout :title="title" :description="description" :variant="variant">
        <slot />
    </AuthLayout>
</template>
