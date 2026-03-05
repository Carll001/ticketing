export type Task = {
    id: string
    title: string
    description: string
    status: 'open' | 'in_progress' | 'closed'
    created_at: string
    updated_at: string
}