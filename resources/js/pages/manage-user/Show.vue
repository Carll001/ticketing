<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import user from '@/routes/user'
import { BreadcrumbItem, User } from '@/types'
import { Department } from '@/types/department'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { 
    User as UserIcon, 
    Mail, 
    ShieldCheck, 
    Calendar, 
    ChevronLeft, 
    Pencil, 
    Building2, 
    KeyRound, 
    Clock,
    CheckCircle2,
    XCircle
} from 'lucide-vue-next'

type UserPermission = {
    name: string
}

type ShowUser = User & {
    departments?: Department[]
    permissions?: UserPermission[]
}

const props = defineProps<{
    user: { data: ShowUser } | ShowUser
}>()

const currentUser = computed<ShowUser>(() => {
    return ('data' in props.user ? props.user.data : props.user) as ShowUser
})

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Users', href: user.index() },
    { title: currentUser.value.name, href: user.show(currentUser.value.id) },
])

const formatDate = (value?: string | null) => {
    if (!value) return 'N/A'
    const date = new Date(value)
    return Number.isNaN(date.getTime()) ? value : date.toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatRelativeDate = (value?: string | null) => {
    if (!value) return 'N/A'
    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return 'N/A'
    const diffMs = Date.now() - date.getTime()
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))
    if (diffDays < 1) return 'Today'
    if (diffDays < 30) return `${diffDays}d ago`
    const diffMonths = Math.floor(diffDays / 30)
    if (diffMonths < 12) return `${diffMonths}mo ago`
    return `${Math.floor(diffMonths / 12)}y ago`
}

const userInitials = computed(() => {
    const name = currentUser.value.name?.trim() || 'User'
    return name.split(' ').filter(Boolean).map(part => part[0]).join('').slice(0, 2).toUpperCase()
})

const isVerified = computed(() => Boolean(currentUser.value.email_verified_at))
</script>

<template>
    <Head :title="`User Profile - ${currentUser.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6 max-w-7xl mx-auto w-full">
            
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary border-2 border-primary/20 text-xl font-bold">
                        {{ userInitials }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-2xl font-bold tracking-tight">{{ currentUser.name }}</h1>
                            <Badge variant="outline" class="bg-primary/5 capitalize">{{ currentUser.role || 'User' }}</Badge>
                            <Badge :variant="isVerified ? 'default' : 'destructive'" class="gap-1">
                                <component :is="isVerified ? CheckCircle2 : XCircle" class="h-3 w-3" />
                                {{ isVerified ? 'Verified' : 'Unverified' }}
                            </Badge>
                        </div>
                        <p class="text-muted-foreground flex items-center gap-1.5 mt-1">
                            <Mail class="h-3.5 w-3.5" />
                            {{ currentUser.email }}
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <Button as-child variant="ghost" size="sm">
                        <Link :href="user.index()"><ChevronLeft class="mr-1 h-4 w-4" /> Back</Link>
                    </Button>
                    <Button as-child size="sm" class="shadow-sm">
                        <Link :href="user.edit(currentUser.id)"><Pencil class="mr-2 h-4 w-4" /> Edit Profile</Link>
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 grid-cols-2 md:grid-cols-4">
                <Card class="bg-muted/30">
                    <CardContent class="p-4 flex items-center gap-4">
                        <div class="p-2 bg-background rounded-lg border shadow-sm"><Building2 class="h-5 w-5 text-muted-foreground" /></div>
                        <div>
                            <p class="text-xs text-muted-foreground font-medium uppercase">Departments</p>
                            <p class="text-xl font-bold">{{ currentUser.departments?.length || 0 }}</p>
                        </div>
                    </CardContent>
                </Card>
                <Card class="bg-muted/30">
                    <CardContent class="p-4 flex items-center gap-4">
                        <div class="p-2 bg-background rounded-lg border shadow-sm"><KeyRound class="h-5 w-5 text-muted-foreground" /></div>
                        <div>
                            <p class="text-xs text-muted-foreground font-medium uppercase">Permissions</p>
                            <p class="text-xl font-bold">{{ currentUser.permissions?.length || 0 }}</p>
                        </div>
                    </CardContent>
                </Card>
                <Card class="bg-muted/30 col-span-2 md:col-span-2">
                    <CardContent class="p-4 flex items-center gap-4">
                        <div class="p-2 bg-background rounded-lg border shadow-sm"><Calendar class="h-5 w-5 text-muted-foreground" /></div>
                        <div>
                            <p class="text-xs text-muted-foreground font-medium uppercase">Member Since</p>
                            <p class="text-sm font-bold">{{ formatDate(currentUser.created_at).split(',')[0] }} ({{ formatRelativeDate(currentUser.created_at) }})</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <Card class="xl:col-span-2">
                    <CardHeader>
                        <CardTitle class="text-lg flex items-center gap-2">
                            <UserIcon class="h-5 w-5 text-primary" />
                            Account Details
                        </CardTitle>
                        <CardDescription>System record information for this user account.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <span class="text-sm text-muted-foreground font-medium">Full Name</span>
                                    <p class="text-sm font-semibold border-b pb-2">{{ currentUser.name }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-sm text-muted-foreground font-medium">Account Role</span>
                                    <p class="text-sm font-semibold border-b pb-2 capitalize">{{ currentUser.role || 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <span class="text-sm text-muted-foreground font-medium">Email Address</span>
                                <div class="flex items-center gap-2 border-b pb-2">
                                    <p class="text-sm font-semibold">{{ currentUser.email }}</p>
                                    <Badge v-if="isVerified" variant="outline" class="h-5 text-[10px] text-green-600 bg-green-50">Verified</Badge>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <span class="text-sm text-muted-foreground font-medium flex items-center gap-1">
                                        <ShieldCheck class="h-3.5 w-3.5" /> Verification Date
                                    </span>
                                    <p class="text-sm font-semibold">{{ currentUser.email_verified_at ? formatDate(currentUser.email_verified_at) : 'Not verified' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-sm text-muted-foreground font-medium flex items-center gap-1">
                                        <Clock class="h-3.5 w-3.5" /> Last Modified
                                    </span>
                                    <p class="text-sm font-semibold">{{ formatDate(currentUser.updated_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="space-y-6">
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base flex items-center gap-2">
                                <Building2 class="h-4 w-4" /> Departments
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="currentUser.departments?.length" class="flex flex-wrap gap-2">
                                <Badge 
                                    v-for="dept in currentUser.departments" 
                                    :key="dept.id" 
                                    variant="secondary"
                                    class="rounded-md font-normal"
                                >
                                    {{ dept.name }}
                                </Badge>
                            </div>
                            <div v-else class="text-sm text-muted-foreground italic py-2">
                                No departments assigned.
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base flex items-center gap-2">
                                <KeyRound class="h-4 w-4" /> Direct Permissions
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="currentUser.permissions?.length" class="space-y-1.5">
                                <div 
                                    v-for="perm in currentUser.permissions" 
                                    :key="perm.name"
                                    class="text-xs font-mono bg-muted/50 p-2 rounded border flex items-center gap-2"
                                >
                                    <div class="h-1 w-1 rounded-full bg-primary" />
                                    {{ perm.name }}
                                </div>
                            </div>
                            <div v-else class="text-sm text-muted-foreground italic py-2">
                                No direct permissions.
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>