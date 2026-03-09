<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import task from '@/routes/task';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Users, ListChecks, Shield, FileText, Handshake, Sparkles, ArrowRight } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

const page = usePage();
const auth = computed(() => page.props.auth);
const isVisible = ref(false);

const canViewDashboard = computed(() => {
    return auth.value?.permissions?.includes('can view dashboard') ?? false;
});

onMounted(() => {
    // Trigger animations after mount
    setTimeout(() => {
        isVisible.value = true;
    }, 100);
});
</script>

<template>
    <Head title="Welcome" />
    
    <div class="min-h-screen bg-[#0A0A0F] relative overflow-hidden">
        <!-- Animated Background Grid -->
        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:64px_64px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,#000_70%,transparent_100%)]"></div>
        
        <!-- Floating Orbs -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl animate-pulse animation-delay-2000"></div>
        
        <div class="relative z-10 grid lg:grid-cols-2 min-h-screen">
            <!-- Left Panel -->
            <div class="flex items-center justify-center p-6 lg:p-12" :class="{ 'opacity-100 translate-y-0': isVisible, 'opacity-0 translate-y-4': !isVisible }" style="transition: all 0.6s ease-out">
                <div class="w-full max-w-lg space-y-8">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 bg-white/5 backdrop-blur-sm rounded-full px-4 py-2 border border-white/10 w-fit">
                        <Sparkles class="h-4 w-4 text-blue-400" />
                        <span class="text-sm font-medium text-gray-300">Enterprise Task Management</span>
                    </div>

                    <!-- Hero Text -->
                    <div class="space-y-4">
                        <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">
                            Streamline Your
                            <span class="bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">Team Workflow</span>
                        </h1>
                        <p class="text-lg text-gray-400 leading-relaxed max-w-md">
                           Empower your organization with powerful task management, real-time collaboration, and insights that help teams stay organized and productive.
                        </p>
                    </div>

                    <!-- Features Grid -->
                    <div class="grid sm:grid-cols-2 gap-4 pt-4">
                        <!-- Team Collaboration -->
                        <div class="group p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300 hover:scale-105">
                            <div class="flex items-start gap-3">
                                <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg p-2.5 group-hover:scale-110 transition-transform">
                                    <Users class="h-5 w-5 text-blue-400" />
                                </div>
                                <div>
                                    <h3 class="text-white font-semibold text-sm mb-1">Team Collaborations</h3>
                                    <p class="text-gray-400 text-xs leading-relaxed">
                                       Coordinate effortlessly with your team and keep everyone aligned on shared goals.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Task Management -->
                        <div class="group p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300 hover:scale-105">
                            <div class="flex items-start gap-3">
                                <div class="bg-gradient-to-br from-green-500/20 to-green-600/20 rounded-lg p-2.5 group-hover:scale-110 transition-transform">
                                    <ListChecks class="h-5 w-5 text-green-400" />
                                </div>
                                <div>
                                    <h3 class="text-white font-semibold text-sm mb-1">Task Management</h3>
                                    <p class="text-gray-400 text-xs leading-relaxed">
                                        Create, assign, prioritize, and track tasks with automated workflows.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Secure & Compliant -->
                        <div class="group p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300 hover:scale-105">
                            <div class="flex items-start gap-3">
                                <div class="bg-gradient-to-br from-cyan-500/20 to-cyan-600/20 rounded-lg p-2.5 group-hover:scale-110 transition-transform">
                                    <Shield class="h-5 w-5 text-cyan-400" />
                                </div>
                                <div>
                                    <h3 class="text-white font-semibold text-sm mb-1">Secure & Compliant</h3>
                                    <p class="text-gray-400 text-xs leading-relaxed">
                                        Enterprise-grade security with role-based to protect your data.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Administrative Tools -->
                        <div class="group p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300 hover:scale-105">
                            <div class="flex items-start gap-3">
                                <div class="bg-gradient-to-br from-amber-500/20 to-amber-600/20 rounded-lg p-2.5 group-hover:scale-110 transition-transform">
                                    <FileText class="h-5 w-5 text-amber-400" />
                                </div>
                                <div>
                                    <h3 class="text-white font-semibold text-sm mb-1">Admin Tools</h3>
                                    <p class="text-gray-400 text-xs leading-relaxed">
                                        Comprehensive reporting and department management
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Section -->
                    <div class="pt-4 space-y-4">
                        <div class="flex flex-wrap items-center gap-4">
                            <Link
                                v-if="$page.props.auth.user && canViewDashboard"
                                :href="dashboard()"
                                class="group relative inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg shadow-blue-600/25"
                            >
                                <span>Go to Dashboard</span>
                                <ArrowRight class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                            </Link>
                            <Link
                                v-else-if="$page.props.auth.user && !canViewDashboard"
                                :href="task.index()"
                                class="group relative inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg shadow-blue-600/25"
                            >
                                <span>Get Started</span>
                                <ArrowRight class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                            </Link>
                            <Link
                                v-else-if="!$page.props.auth.user"
                                :href="login()"
                                class="group relative inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg shadow-blue-600/25"
                            >
                                <span>Get Started</span>
                                <ArrowRight class="h-4 w-4 group-hover:translate-x-1 transition-transform" />
                            </Link>
                            
                            <Link
                                v-if="!$page.props.auth.user"
                                :href="register()"
                                class="inline-flex items-center px-6 py-3 text-sm font-semibold text-gray-300 hover:text-white transition-colors"
                            >
                                Create account
                            </Link>
                        </div>
                        
                        <!-- Trust Indicators -->
                        <div class="flex items-center gap-4 text-xs text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                99.9% Uptime
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                SOC 2 Type II
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                24/7 Support
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Enhanced Visual -->
            <div class="hidden lg:flex items-center justify-center p-6 relative" :class="{ 'opacity-100 scale-100': isVisible, 'opacity-0 scale-95': !isVisible }" style="transition: all 0.6s ease-out 0.2s">
                <div class="w-full h-[85vh] rounded-2xl bg-gradient-to-br from-[#4F46E5] via-[#7C3AED] to-[#EC4899] p-1 relative overflow-hidden group">
                    <!-- Animated Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1500 ease-in-out"></div>
                    
                    <!-- Main Content Container -->
                    <div class="relative w-full h-full rounded-xl bg-gradient-to-br from-[#4F46E5] via-[#7C3AED] to-[#EC4899] p-10 flex flex-col items-center justify-center backdrop-blur-3xl">
                        <!-- Decorative Elements -->
                        <div class="absolute inset-0 overflow-hidden">
                            <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-white/10 blur-3xl"></div>
                            <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full bg-purple-500/20 blur-3xl"></div>
                            
                            <!-- Floating Particles -->
                            <div class="absolute top-1/4 left-1/4 w-2 h-2 rounded-full bg-white/30 animate-ping"></div>
                            <div class="absolute bottom-1/3 right-1/4 w-2 h-2 rounded-full bg-white/30 animate-ping animation-delay-1000"></div>
                        </div>

                        <!-- Icon Circle -->
                        <div class="relative z-10 mb-8">
                            <div class="w-28 h-28 rounded-full bg-white/10 backdrop-blur-xl flex items-center justify-center animate-pulse">
                                <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center">
                                    <Handshake class="h-10 w-10 text-white" />
                                </div>
                            </div>
                            
                            <!-- Orbiting Rings -->
                            <div class="absolute -inset-4 rounded-full border border-white/20 animate-spin-slow"></div>
                            <div class="absolute -inset-8 rounded-full border border-white/10 animate-spin-slow animation-delay-500"></div>
                        </div>

                        <!-- Content with Animation -->
                        <div class="relative z-10 text-center space-y-4">
                            <h2 class="text-3xl font-bold text-white mb-2">
                                TaskFlow Enterprise
                            </h2>
                            <p class="text-lg text-white/90 mb-2">
                                 Smart Ticketing System
                            </p>
                            
                            <!-- Stats Cards -->
                            <div class="grid grid-cols-3 gap-3 mt-8">
                                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 text-center">
                                    <div class="text-2xl font-bold text-white">99%</div>
                                    <div class="text-xs text-white/70">Faster Issue Resolution</div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 text-center">
                                    <div class="text-2xl font-bold text-white">24/7</div>
                                    <div class="text-xs text-white/70">Ticket Monitoring & Tracking</div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 text-center">
                                    <div class="text-2xl font-bold text-white">100+</div>
                                    <div class="text-xs text-white/70">Workflow Integrations</div>
                                </div>
                            </div>
                            
                            <p class="text-sm text-white/80 mt-6 max-w-xs mx-auto">
                                Streamlining Support, Empowering Teams
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@keyframes spin-slow {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin-slow {
    animation: spin-slow 8s linear infinite;
}

.animation-delay-500 {
    animation-delay: 500ms;
}

.animation-delay-1000 {
    animation-delay: 1000ms;
}

.animation-delay-2000 {
    animation-delay: 2000ms;
}

.duration-1500 {
    transition-duration: 1500ms;
}
</style>