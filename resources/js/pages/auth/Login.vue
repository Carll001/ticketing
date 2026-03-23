<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const showPassword = ref(false);
</script>

<template>
    <AuthBase
        title="Welcome back"
        description="Sign in to continue managing tickets, updates, and assignments in Analytica Ticketing System."
        variant="welcome-login"
    >
        <Head title="Log in" />

        <div
            v-if="status"
            class="mb-6 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-200"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-7"
        >
            <div class="grid gap-6">
                <div class="grid gap-2.5">
                    <Label
                        for="email"
                        class="text-sm font-medium text-slate-200"
                        >Email address</Label
                    >
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="email@example.com"
                        class="h-12 rounded-2xl border border-white/10 bg-white/5 px-4 text-white shadow-none placeholder:text-slate-500 focus-visible:border-blue-400/60 focus-visible:ring-4 focus-visible:ring-blue-500/12"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2.5">
                    <div class="flex items-center justify-between">
                        <Label
                            for="password"
                            class="text-sm font-medium text-slate-200"
                            >Password</Label
                        >
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm font-medium text-slate-300 decoration-white/20 hover:text-white hover:decoration-white"
                            :tabindex="5"
                        >
                            Forgot password?
                        </TextLink>
                    </div>
                    <div class="relative">
                        <Input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="Password"
                            class="h-12 rounded-2xl border border-white/10 bg-white/5 px-4 pr-12 text-white shadow-none placeholder:text-slate-500 focus-visible:border-blue-400/60 focus-visible:ring-4 focus-visible:ring-blue-500/12"
                        />
                        <button
                            type="button"
                            class="absolute inset-y-0 right-0 inline-flex items-center px-4 text-slate-400 transition-colors hover:text-white"
                            :aria-label="
                                showPassword ? 'Hide password' : 'Show password'
                            "
                            @click="showPassword = !showPassword"
                        >
                            <component
                                :is="showPassword ? EyeOff : Eye"
                                class="h-4 w-4"
                            />
                            <span class="sr-only">
                                {{
                                    showPassword
                                        ? 'Hide password'
                                        : 'Show password'
                                }}
                            </span>
                        </button>
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <div
                    class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3"
                >
                    <Label for="remember" class="text-sm text-slate-300">
                        <Checkbox
                            id="remember"
                            name="remember"
                            :tabindex="3"
                            class="border-white/20 bg-white/5 text-white data-[state=checked]:border-blue-400 data-[state=checked]:bg-blue-500"
                        />
                        <span>Remember me</span>
                    </Label>
                    <span class="text-xs text-slate-500">Secure session</span>
                </div>

                <Button
                    type="submit"
                    class="mt-2 h-12 w-full rounded-2xl border border-white/10 bg-gradient-to-r from-blue-600 to-purple-600 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition-all duration-300 hover:from-blue-700 hover:to-purple-700"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" />
                    <span>{{ processing ? 'Signing in...' : 'Sign in' }}</span>
                </Button>
            </div>

            <div
                class="border-t border-white/10 pt-5 text-center text-sm text-gray-400"
                v-if="canRegister"
            >
                Don't have an account?
                <TextLink
                    :href="register()"
                    :tabindex="5"
                    class="ml-1 font-medium text-white decoration-white/20 hover:text-blue-200 hover:decoration-blue-300"
                >
                    Sign up
                </TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
