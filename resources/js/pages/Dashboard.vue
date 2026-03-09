<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import type { BreadcrumbItem } from '@/types'
import { Building2, CheckCircle2, CircleDot, Clock3, FileCheck2, Users } from 'lucide-vue-next'
import { computed } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Progress } from '@/components/ui/progress'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'

type DashboardStats = {
    total_users: number
    total_departments: number
    total_presets: number
    total_tasks: number
    open_tasks: number
    in_progress_tasks: number
    completed_tasks: number
}

type TaskByDepartment = {
    department: string
    tasks: number
}

type RecentTask = {
    id: string
    title: string
    department: string
    creator: string | null
    steps_count: number
    status: 'open' | 'in_progress' | 'completed'
    last_step: string | null
    taker: string | null
    created_at: string
}

const props = defineProps<{
    stats: DashboardStats
    tasks_by_department: TaskByDepartment[]
    recent_tasks: RecentTask[]
    is_admin: boolean
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
]

const statusLabel = (status: RecentTask['status']) => {
    if (status === 'completed') return 'Completed'
    if (status === 'in_progress') return 'In Progress'
    return 'Open'
}

const statusClass = (status: RecentTask['status']) => {
    if (status === 'completed') return 'bg-emerald-100 text-emerald-700'
    if (status === 'in_progress') return 'bg-blue-100 text-blue-700'
    return 'bg-amber-100 text-amber-700'
}

const kpis = [
    {
        label: 'Total Users',
        value: props.stats.total_users,
        icon: Users,
        accent: 'from-sky-500/80 to-cyan-400/80',
        iconTone: 'bg-sky-500/15 text-sky-400 ring-1 ring-sky-500/30',
        valueTone: 'text-sky-100',
    },
    {
        label: 'Departments',
        value: props.stats.total_departments,
        icon: Building2,
        accent: 'from-violet-500/80 to-indigo-400/80',
        iconTone: 'bg-violet-500/15 text-violet-400 ring-1 ring-violet-500/30',
        valueTone: 'text-violet-100',
    },
    {
        label: 'Task Presets',
        value: props.stats.total_presets,
        icon: FileCheck2,
        accent: 'from-fuchsia-500/80 to-pink-400/80',
        iconTone: 'bg-fuchsia-500/15 text-fuchsia-400 ring-1 ring-fuchsia-500/30',
        valueTone: 'text-fuchsia-100',
    },
]

const taskStatusData = computed(() => [
    { name: 'Open', value: props.stats.open_tasks, color: '#f59e0b' },
    { name: 'In Progress', value: props.stats.in_progress_tasks, color: '#3b82f6' },
    { name: 'Completed', value: props.stats.completed_tasks, color: '#10b981' },
])

const totalTasksInChart = computed(() =>
    taskStatusData.value.reduce((sum, item) => sum + item.value, 0),
)

const taskStatusWithPercent = computed(() =>
    taskStatusData.value.map((item) => ({
        ...item,
        percentage:
            totalTasksInChart.value > 0
                ? Math.round((item.value / totalTasksInChart.value) * 100)
                : 0,
    })),
)

const donutBackground = computed(() => {
    if (totalTasksInChart.value <= 0) {
        return 'conic-gradient(#e5e7eb 0deg 360deg)'
    }

    let current = 0
    const slices = taskStatusData.value.map((item) => {
        const start = (current / totalTasksInChart.value) * 360
        current += item.value
        const end = (current / totalTasksInChart.value) * 360
        return `${item.color} ${start}deg ${end}deg`
    })
    return `conic-gradient(${slices.join(', ')})`
})
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">
                    Operations Dashboard
                </p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">Task and Ticket Overview</h1>
            </div>

            <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
                <Card
                    v-for="item in kpis"
                    :key="item.label"
                    class="group relative overflow-hidden rounded-2xl border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-black/20"
                >
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r" :class="item.accent" />
                    <CardHeader class="flex flex-row items-start justify-between pb-2">
                        <div class="space-y-1">
                            <CardTitle class="text-sm font-medium text-muted-foreground">{{ item.label }}</CardTitle>
                        </div>
                        <span
                            class="rounded-xl p-2.5 transition-transform duration-200 group-hover:scale-105"
                            :class="item.iconTone"
                        >
                            <component :is="item.icon" class="size-4" />
                        </span>
                    </CardHeader>
                    <CardContent class="pt-0">
                        <p class="text-3xl font-semibold tracking-tight" :class="item.valueTone">
                            {{ item.value }}
                        </p>
                    </CardContent>
                </Card>
            </section>

            <section class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Task Status</CardTitle>
                        <CardDescription>Open, in progress, and completed distribution</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-5">
                        <div class="flex justify-center">
                            <div class="relative flex size-48 items-center justify-center rounded-full" :style="{ background: donutBackground }">
                                <div class="flex size-28 flex-col items-center justify-center rounded-full border bg-background">
                                    <p class="text-2xl font-semibold leading-none">{{ totalTasksInChart }}</p>
                                    <p class="mt-1 text-xs text-muted-foreground">Total Tasks</p>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                            <div
                                v-for="item in taskStatusWithPercent"
                                :key="item.name"
                                class="space-y-2 rounded-md border p-3"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block size-2.5 rounded-full" :style="{ backgroundColor: item.color }" />
                                        <span>{{ item.name }}</span>
                                    </div>
                                    <span class="font-medium">{{ item.value }} ({{ item.percentage }}%)</span>
                                </div>
                                <Progress
                                    :model-value="item.percentage"
                                    :indicator-class="item.name === 'Open'
                                        ? 'bg-amber-500'
                                        : item.name === 'In Progress'
                                            ? 'bg-blue-500'
                                            : 'bg-emerald-500'"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Tasks By Department</CardTitle>
                        <CardDescription>Task volume by team</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div
                            v-for="(row, index) in props.tasks_by_department"
                            :key="row.department"
                            class="space-y-2 rounded-md border p-3"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex size-5 items-center justify-center rounded-full bg-primary/10 text-[10px] font-semibold text-primary">
                                        {{ index + 1 }}
                                    </span>
                                    <p class="text-sm font-medium">{{ row.department }}</p>
                                </div>
                                <p class="text-sm">{{ row.tasks }}</p>
                            </div>
                            <Progress
                                :model-value="props.stats.total_tasks > 0 ? Math.round((row.tasks / props.stats.total_tasks) * 100) : 0"
                            />
                        </div>
                        <p v-if="props.tasks_by_department.length === 0" class="text-center text-sm text-muted-foreground">
                            No data
                        </p>
                    </CardContent>
                </Card>

                <Card class="xl:col-span-2">
                    <CardHeader>
                        <CardTitle>Recent Tasks</CardTitle>
                        <CardDescription>Latest tasks from real records</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Department</TableHead>
                                    <TableHead>Steps</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Last Step</TableHead>
                                    <TableHead v-if="props.is_admin">Taker</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="task in props.recent_tasks" :key="task.id">
                                    <TableCell class="font-medium">{{ task.title }}</TableCell>
                                    <TableCell>{{ task.department }}</TableCell>
                                    <TableCell>{{ task.steps_count }}</TableCell>
                                    <TableCell>
                                        <Badge :class="statusClass(task.status)">
                                            {{ statusLabel(task.status) }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>{{ task.last_step || '-' }}</TableCell>
                                    <TableCell v-if="props.is_admin">{{ task.taker || 'Unclaimed' }}</TableCell>
                                </TableRow>
                                <TableRow v-if="props.recent_tasks.length === 0">
                                    <TableCell :colspan="props.is_admin ? 6 : 5" class="text-center text-muted-foreground">
                                        No recent tasks
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </section>
        </div>
    </AppLayout>
</template>
