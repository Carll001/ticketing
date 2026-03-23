<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Sparkles } from 'lucide-vue-next';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

const page = usePage();
const systemName = 'Analytica Ticketing System';
const name = page.props.name ?? systemName;

withDefaults(
    defineProps<{
        title?: string;
        description?: string;
        variant?: 'default' | 'welcome-login';
    }>(),
    {
        variant: 'default',
    },
);
</script>

<template>
    <div
        v-if="variant === 'welcome-login'"
        class="relative h-dvh overflow-hidden bg-[#0A0A0F]"
    >
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(59,130,246,0.18),transparent_28%),radial-gradient(circle_at_bottom_right,rgba(168,85,247,0.14),transparent_30%)]"
        ></div>
        <div
            class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,#000_70%,transparent_100%)] bg-[size:64px_64px]"
        ></div>
        <div
            class="absolute top-20 left-0 h-72 w-72 -translate-x-1/3 rounded-full bg-blue-500/10 blur-3xl"
        ></div>
        <div
            class="absolute right-0 bottom-0 h-80 w-80 translate-x-1/4 translate-y-1/4 rounded-full bg-purple-500/10 blur-3xl"
        ></div>

        <div
            class="relative z-10 flex h-full items-center justify-center px-4 py-4 sm:px-6"
        >
            <div class="w-full max-w-xl">
                <div
                    class="rounded-[2rem] border border-white/10 bg-white/5 p-2 shadow-[0_32px_120px_rgba(2,6,23,0.52)] backdrop-blur-xl"
                >
                    <div
                        class="overflow-hidden rounded-[calc(2rem-6px)] border border-white/10 bg-[#0F111A]/90"
                    >
                        <div
                            class="h-1.5 bg-gradient-to-r from-blue-600 via-violet-500 to-fuchsia-500"
                        ></div>

                        <div class="px-6 py-6 sm:px-10 sm:py-10">
                            <div class="mb-8 space-y-4">
                                <div
                                    class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold tracking-[0.12em] text-gray-300"
                                >
                                    <Sparkles
                                        class="h-3.5 w-3.5 text-blue-600"
                                    />
                                    <span>{{ systemName }}</span>
                                </div>

                                <div class="space-y-3">
                                    <h1
                                        v-if="title"
                                        class="text-4xl leading-tight font-semibold tracking-tight text-white"
                                    >
                                        {{ title }}
                                    </h1>
                                    <p
                                        v-if="description"
                                        class="max-w-lg text-base leading-7 text-gray-400"
                                    >
                                        {{ description }}
                                    </p>
                                </div>
                            </div>

                            <slot />
                        </div>
                    </div>
                </div>

                <p class="mt-4 text-center text-xs text-gray-500">
                    Secure access to tickets, updates, and team workflows.
                </p>
            </div>
        </div>
    </div>

    <div
        v-else
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0"
    >
        <div
            class="relative hidden h-full flex-col bg-muted p-10 text-white lg:flex dark:border-r"
        >
            <div class="absolute inset-0 bg-zinc-900" />
            <Link
                :href="home()"
                class="relative z-20 flex items-center text-lg font-medium"
            >
                <AppLogoIcon class="mr-2 size-8 fill-current text-white" />
                {{ name }}
            </Link>
        </div>
        <div class="lg:p-8">
            <div
                class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]"
            >
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="title">
                        {{ title }}
                    </h1>
                    <p class="text-sm text-muted-foreground" v-if="description">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
