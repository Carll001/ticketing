import { User } from "./auth"
import { Department } from "./department"

export type Task = {
    id: string
    title: string
    description: string
    status: 'open' | 'in_progress' | 'closed'
    created_at: string
    updated_at: string

    department_assigned?: Department
    creator?: Pick<User, 'id' | 'name' | 'email' | 'role'>
    steps_count?: number
    last_step?: {
        id: string
        title: string
        status: string
        claimed_by?: {
            id?: string
            name?: string
        } | null
    } | null
}
