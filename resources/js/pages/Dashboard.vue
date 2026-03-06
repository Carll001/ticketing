<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import {
    AlertTriangle,
    Building2,
    CheckCircle2,
    CircleDot,
    ClipboardPlus,
    Clock3,
    FileCheck2,
    MoveRight,
    Users,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];

type TaskStatus = 'Open' | 'In Progress' | 'Completed' | 'Overdue';
type TaskPriority = 'Low' | 'Medium' | 'High' | 'Critical';

const kpis = [
    {
        label: 'Total Users',
        value: 128,
        icon: Users,
        helper: '+6 new users this week',
        accent: 'from-sky-500/80 to-cyan-400/80',
        iconTone: 'bg-sky-500/15 text-sky-400 ring-1 ring-sky-500/30',
        valueTone: 'text-sky-100',
    },
    {
        label: 'Total Departments',
        value: 12,
        icon: Building2,
        helper: '2 departments added this quarter',
        accent: 'from-violet-500/80 to-indigo-400/80',
        iconTone: 'bg-violet-500/15 text-violet-400 ring-1 ring-violet-500/30',
        valueTone: 'text-violet-100',
    },
    {
        label: 'Total Presets',
        value: 37,
        icon: FileCheck2,
        helper: '5 presets updated this month',
        accent: 'from-fuchsia-500/80 to-pink-400/80',
        iconTone: 'bg-fuchsia-500/15 text-fuchsia-400 ring-1 ring-fuchsia-500/30',
        valueTone: 'text-fuchsia-100',
    },
    {
        label: 'Open Tasks',
        value: 49,
        icon: CircleDot,
        helper: '12 tasks created in last 24h',
        accent: 'from-amber-500/80 to-yellow-400/80',
        iconTone: 'bg-amber-500/15 text-amber-400 ring-1 ring-amber-500/30',
        valueTone: 'text-amber-100',
    },
    {
        label: 'In Progress Tasks',
        value: 31,
        icon: Clock3,
        helper: 'Average age: 2.4 days',
        accent: 'from-blue-500/80 to-cyan-400/80',
        iconTone: 'bg-blue-500/15 text-blue-400 ring-1 ring-blue-500/30',
        valueTone: 'text-blue-100',
    },
    {
        label: 'Completed Tasks',
        value: 214,
        icon: CheckCircle2,
        helper: 'Completion rate up 9%',
        accent: 'from-emerald-500/80 to-green-400/80',
        iconTone: 'bg-emerald-500/15 text-emerald-400 ring-1 ring-emerald-500/30',
        valueTone: 'text-emerald-100',
    },
    {
        label: 'Overdue Tasks',
        value: 8,
        icon: AlertTriangle,
        helper: 'Needs immediate attention',
        accent: 'from-rose-500 to-red-500',
        iconTone: 'bg-red-500/20 text-red-300 ring-1 ring-red-500/40',
        valueTone: 'text-red-100',
        cardTone:
            'border-red-500/40 bg-gradient-to-br from-red-950/80 via-card to-card shadow-red-900/30',
    },
];

const taskStatusData: { name: TaskStatus; value: number; color: string }[] = [
    { name: 'Open', value: 49, color: '#f59e0b' },
    { name: 'In Progress', value: 31, color: '#3b82f6' },
    { name: 'Completed', value: 214, color: '#10b981' },
    { name: 'Overdue', value: 8, color: '#ef4444' },
];

const tasksByDepartment = [
    { department: 'Engineering', tasks: 62 },
    { department: 'Support', tasks: 55 },
    { department: 'HR', tasks: 22 },
    { department: 'Sales', tasks: 41 },
    { department: 'Operations', tasks: 38 },
];

const recentTasks: {
    id: string;
    title: string;
    department: string;
    preset: string;
    assignedUser: string;
    status: TaskStatus;
    priority: TaskPriority;
    dueDate: string;
}[] = [
    {
        id: 'TASK-1034',
        title: 'Server setup for staging',
        department: 'Engineering',
        preset: 'Infrastructure Checklist',
        assignedUser: 'Juan Dela Cruz',
        status: 'In Progress',
        priority: 'High',
        dueDate: 'Mar 08, 2026',
    },
    {
        id: 'TASK-1035',
        title: 'Q2 onboarding packet update',
        department: 'HR',
        preset: 'Onboarding Checklist',
        assignedUser: 'Ana Reyes',
        status: 'Open',
        priority: 'Medium',
        dueDate: 'Mar 10, 2026',
    },
    {
        id: 'TASK-1036',
        title: 'Design review for customer portal',
        department: 'Engineering',
        preset: 'UI Review Workflow',
        assignedUser: 'Marco Santos',
        status: 'Overdue',
        priority: 'Critical',
        dueDate: 'Mar 04, 2026',
    },
    {
        id: 'TASK-1037',
        title: 'Website hero banner refresh',
        department: 'Sales',
        preset: 'Campaign Launch',
        assignedUser: 'Lia Mendoza',
        status: 'Completed',
        priority: 'Low',
        dueDate: 'Mar 05, 2026',
    },
    {
        id: 'TASK-1038',
        title: 'Incident escalation runbook cleanup',
        department: 'Support',
        preset: 'Incident Management',
        assignedUser: 'Paolo Lim',
        status: 'In Progress',
        priority: 'High',
        dueDate: 'Mar 09, 2026',
    },
];

const recentActivity = [
    {
        text: 'Juan created task "Server Setup"',
        time: '5m ago',
        icon: ClipboardPlus,
    },
    {
        text: 'Task "Design Review" moved to In Progress',
        time: '18m ago',
        icon: MoveRight,
    },
    {
        text: 'Preset "Onboarding Checklist" assigned to HR',
        time: '42m ago',
        icon: FileCheck2,
    },
    {
        text: 'Ana completed task "Website Update"',
        time: '1h ago',
        icon: CheckCircle2,
    },
];

const donutBackground = computed(() => {
    const total = taskStatusData.reduce((sum, item) => sum + item.value, 0);
    let current = 0;
    const slices = taskStatusData.map((item) => {
        const start = (current / total) * 360;
        current += item.value;
        const end = (current / total) * 360;
        return `${item.color} ${start}deg ${end}deg`;
    });
    return `conic-gradient(${slices.join(', ')})`;
});

const totalTasks = computed(() =>
    taskStatusData.reduce((sum, item) => sum + item.value, 0),
);

const taskStatusWithPercent = computed(() =>
    taskStatusData.map((item) => ({
        ...item,
        percentage:
            totalTasks.value > 0
                ? Math.round((item.value / totalTasks.value) * 100)
                : 0,
    })),
);

const topStatus = computed(() =>
    [...taskStatusData].sort((a, b) => b.value - a.value)[0],
);

const maxDepartmentTasks = computed(() =>
    Math.max(...tasksByDepartment.map((item) => item.tasks)),
);

const totalDepartmentTasks = computed(() =>
    tasksByDepartment.reduce((sum, item) => sum + item.tasks, 0),
);

const tasksByDepartmentWithPercent = computed(() =>
    tasksByDepartment.map((item) => ({
        ...item,
        percentage:
            totalDepartmentTasks.value > 0
                ? Math.round((item.tasks / totalDepartmentTasks.value) * 100)
                : 0,
    })),
);

const topDepartment = computed(() =>
    [...tasksByDepartment].sort((a, b) => b.tasks - a.tasks)[0],
);

const departmentsPerPage = 5;
const departmentPage = ref(1);

const totalDepartmentPages = computed(() =>
    Math.max(
        1,
        Math.ceil(tasksByDepartmentWithPercent.value.length / departmentsPerPage),
    ),
);

const departmentStartIndex = computed(
    () => (departmentPage.value - 1) * departmentsPerPage,
);

const paginatedDepartments = computed(() =>
    tasksByDepartmentWithPercent.value.slice(
        departmentStartIndex.value,
        departmentStartIndex.value + departmentsPerPage,
    ),
);

const showingDepartmentFrom = computed(() =>
    tasksByDepartmentWithPercent.value.length === 0
        ? 0
        : departmentStartIndex.value + 1,
);

const showingDepartmentTo = computed(() =>
    Math.min(
        departmentStartIndex.value + departmentsPerPage,
        tasksByDepartmentWithPercent.value.length,
    ),
);

const canGoPrevDepartmentPage = computed(() => departmentPage.value > 1);
const canGoNextDepartmentPage = computed(
    () => departmentPage.value < totalDepartmentPages.value,
);

const goToPrevDepartmentPage = () => {
    if (!canGoPrevDepartmentPage.value) return;
    departmentPage.value -= 1;
};

const goToNextDepartmentPage = () => {
    if (!canGoNextDepartmentPage.value) return;
    departmentPage.value += 1;
};

const statusClasses: Record<TaskStatus, string> = {
    Open: 'bg-amber-100 text-amber-700',
    'In Progress': 'bg-blue-100 text-blue-700',
    Completed: 'bg-emerald-100 text-emerald-700',
    Overdue: 'bg-red-100 text-red-700',
};

const priorityClasses: Record<TaskPriority, string> = {
    Low: 'bg-slate-100 text-slate-700',
    Medium: 'bg-amber-100 text-amber-700',
    High: 'bg-orange-100 text-orange-700',
    Critical: 'bg-rose-100 text-rose-700',
};

const taskSearch = ref('');
const taskStatusFilter = ref<'all' | TaskStatus>('all');

const filteredRecentTasks = computed(() =>
    recentTasks.filter((task) => {
        const matchesSearch =
            task.title.toLowerCase().includes(taskSearch.value.toLowerCase()) ||
            task.department.toLowerCase().includes(taskSearch.value.toLowerCase()) ||
            task.assignedUser
                .toLowerCase()
                .includes(taskSearch.value.toLowerCase());
        const matchesStatus =
            taskStatusFilter.value === 'all' ||
            task.status === taskStatusFilter.value;

        return matchesSearch && matchesStatus;
    }),
);

const initials = (name: string) =>
    name
        .split(' ')
        .slice(0, 2)
        .map((part) => part[0])
        .join('')
        .toUpperCase();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">
                    Operations Dashboard
                </p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">Task and Ticket Overview</h1>
            </div>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <Card
                    v-for="item in kpis"
                    :key="item.label"
                    class="group relative overflow-hidden rounded-2xl border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-black/20"
                    :class="item.cardTone"
                >
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r" :class="item.accent" />
                    <CardHeader class="flex flex-row items-start justify-between pb-2">
                        <div class="space-y-1">
                            <CardTitle class="text-sm font-medium text-muted-foreground">{{ item.label }}</CardTitle>
                            <CardDescription class="text-xs">{{ item.helper }}</CardDescription>
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
                <Card class="rounded-2xl">
                    <CardHeader>
                        <CardTitle>Task Status</CardTitle>
                        <CardDescription>Open, in progress, completed, and overdue distribution</CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-5">
                        <div class="flex justify-center">
                            <div class="relative flex size-48 items-center justify-center rounded-full" :style="{ background: donutBackground }">
                                <div class="flex size-28 flex-col items-center justify-center rounded-full border bg-background">
                                    <p class="text-2xl font-semibold leading-none">{{ totalTasks }}</p>
                                    <p class="mt-1 text-xs text-muted-foreground">Total Tasks</p>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div
                                v-for="item in taskStatusWithPercent"
                                :key="item.name"
                                class="flex items-center justify-between rounded-lg border p-2"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="inline-block size-2.5 rounded-full" :style="{ backgroundColor: item.color }" />
                                    <span>{{ item.name }}</span>
                                </div>
                                <span class="font-medium">{{ item.value }} ({{ item.percentage }}%)</span>
                            </div>
                        </div>
                        <div class="rounded-xl border bg-muted/30 p-3">
                            <p class="text-xs uppercase tracking-wide text-muted-foreground">Top Status</p>
                            <p class="mt-1 text-base font-semibold">{{ topStatus?.name }}</p>
                            <p class="text-sm text-muted-foreground">{{ topStatus?.value }} tasks</p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="rounded-2xl">
                    <CardHeader>
                        <CardTitle>Tasks per Department</CardTitle>
                        <CardDescription>Task volume by team</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div
                            v-for="(row, index) in paginatedDepartments"
                            :key="row.department"
                            class="group rounded-xl border border-border/60 bg-muted/20 p-3 transition-colors hover:bg-muted/40"
                        >
                            <div class="mb-2 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex size-5 items-center justify-center rounded-full bg-primary/10 text-[10px] font-semibold text-primary"
                                    >
                                        {{ departmentStartIndex + index + 1 }}
                                    </span>
                                    <span class="text-sm font-medium">{{ row.department }}</span>
                                </div>
                                <div class="text-right leading-tight">
                                    <p class="text-sm font-semibold">{{ row.tasks }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ row.percentage }}%
                                    </p>
                                </div>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-muted">
                                <div
                                    class="h-full rounded-full bg-primary transition-all duration-300 group-hover:brightness-110"
                                    :style="{ width: `${(row.tasks / maxDepartmentTasks) * 100}%` }"
                                />
                            </div>
                        </div>

                        <div class="flex items-center justify-between rounded-lg border border-border/50 bg-muted/20 p-2">
                            <p class="text-xs text-muted-foreground">
                                Showing {{ showingDepartmentFrom }}-{{ showingDepartmentTo }} of {{ tasksByDepartmentWithPercent.length }} departments
                            </p>
                            <div class="flex items-center gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    :disabled="!canGoPrevDepartmentPage"
                                    @click="goToPrevDepartmentPage"
                                >
                                    Previous
                                </Button>
                                <span class="text-xs text-muted-foreground">
                                    Page {{ departmentPage }} of {{ totalDepartmentPages }}
                                </span>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    :disabled="!canGoNextDepartmentPage"
                                    @click="goToNextDepartmentPage"
                                >
                                    Next
                                </Button>
                            </div>
                        </div>

                        <div class="mt-4 rounded-xl border bg-muted/30 p-3">
                            <p class="text-xs uppercase tracking-wide text-muted-foreground">
                                Top Department
                            </p>
                            <p class="mt-1 text-base font-semibold">
                                {{ topDepartment?.department }}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                {{ topDepartment?.tasks }} tasks
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </section>

            <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
                <Card class="rounded-2xl xl:col-span-2">
                    <CardHeader class="flex flex-row items-center justify-between">
                        <div>
                            <CardTitle>Recent Tasks</CardTitle>
                            <CardDescription>Latest task and ticket updates</CardDescription>
                        </div>
                        <p class="text-xs text-muted-foreground">Page 1 of 12</p>
                    </CardHeader>
                    <CardContent>
                        <div class="mb-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <Input
                                v-model="taskSearch"
                                placeholder="Filter by title, department, or assignee"
                            />
                            <Select v-model="taskStatusFilter">
                                <SelectTrigger>
                                    <SelectValue placeholder="Filter by status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Statuses</SelectItem>
                                    <SelectItem value="Open">Open</SelectItem>
                                    <SelectItem value="In Progress">In Progress</SelectItem>
                                    <SelectItem value="Completed">Completed</SelectItem>
                                    <SelectItem value="Overdue">Overdue</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <Table class="text-xs">
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="h-8 px-2 py-1">Task Title</TableHead>
                                    <TableHead class="h-8 px-2 py-1">Department</TableHead>
                                    <TableHead class="h-8 px-2 py-1">Assigned User</TableHead>
                                    <TableHead class="h-8 px-2 py-1">Status</TableHead>
                                    <TableHead class="h-8 px-2 py-1">Priority</TableHead>
                                    <TableHead class="h-8 px-2 py-1">Due Date</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="task in filteredRecentTasks"
                                    :key="task.id"
                                >
                                    <TableCell class="px-2 py-1.5 font-medium leading-tight">{{ task.title }}</TableCell>
                                    <TableCell class="px-2 py-1.5 leading-tight">{{ task.department }}</TableCell>
                                    <TableCell class="px-2 py-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <Avatar class="size-6">
                                                <AvatarFallback class="text-[9px]">{{ initials(task.assignedUser) }}</AvatarFallback>
                                            </Avatar>
                                            <span class="leading-tight">{{ task.assignedUser }}</span>
                                        </div>
                                    </TableCell>
                                    <TableCell class="px-2 py-1.5">
                                        <Badge class="border-0 px-1.5 py-0 text-[10px]" :class="statusClasses[task.status]">{{ task.status }}</Badge>
                                    </TableCell>
                                    <TableCell class="px-2 py-1.5">
                                        <Badge class="border-0 px-1.5 py-0 text-[10px]" :class="priorityClasses[task.priority]">{{ task.priority }}</Badge>
                                    </TableCell>
                                    <TableCell class="px-2 py-1.5 leading-tight">{{ task.dueDate }}</TableCell>
                                </TableRow>
                                <TableRow v-if="filteredRecentTasks.length === 0">
                                    <TableCell
                                        class="h-24 text-center text-muted-foreground"
                                        colspan="6"
                                    >
                                        No matching tasks found.
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                        <div class="mt-4 flex justify-end gap-2">
                            <Button variant="outline" size="sm">Previous</Button>
                            <Button variant="outline" size="sm">Next</Button>
                        </div>
                    </CardContent>
                </Card>

                <Card class="rounded-2xl xl:col-span-1">
                    <CardHeader>
                        <CardTitle>Recent Activity</CardTitle>
                        <CardDescription>System events and updates</CardDescription>
                    </CardHeader>
                    <CardContent class="max-h-[420px] overflow-y-auto pr-2">
                        <ul class="space-y-4">
                            <li
                                v-for="(activity, index) in recentActivity"
                                :key="index"
                                class="flex items-start gap-3"
                            >
                                <span class="rounded-full bg-muted p-1.5 text-muted-foreground">
                                    <component :is="activity.icon" class="size-3.5" />
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm">{{ activity.text }}</p>
                                    <p class="text-xs text-muted-foreground">{{ activity.time }}</p>
                                </div>
                            </li>
                        </ul>
                    </CardContent>
                </Card>
            </section>

            <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
                <Card class="rounded-2xl">
                    <CardHeader>
                        <CardTitle class="text-sm">Most Active Department</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-lg font-semibold">Engineering</p>
                        <p class="text-xs text-muted-foreground">62 active tasks this week</p>
                    </CardContent>
                </Card>
                <Card class="rounded-2xl">
                    <CardHeader>
                        <CardTitle class="text-sm">Most Used Preset</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-lg font-semibold">Onboarding Checklist</p>
                        <p class="text-xs text-muted-foreground">Used 34 times this month</p>
                    </CardContent>
                </Card>
                <Card class="rounded-2xl">
                    <CardHeader>
                        <CardTitle class="text-sm">User With Most Tasks</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-lg font-semibold">Juan Dela Cruz</p>
                        <p class="text-xs text-muted-foreground">17 currently assigned tasks</p>
                    </CardContent>
                </Card>
            </section>
        </div>
    </AppLayout>
</template>
