export type TransactionRow = {
    id: string
    action: string
    summary: string
    created_at: string
    meta?: Record<string, unknown> | null
    task?: {
        id: string
        title: string
    } | null
    step?: {
        id: string
        title: string
    } | null
    actor?: {
        id: string
        name: string
        email: string
    } | null
}
