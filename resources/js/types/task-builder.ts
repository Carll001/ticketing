export type StepOrder = 'sequential' | 'free'

export type FieldType = 'input' | 'textarea' | 'checkbox'

export type StepField = {
  id: string
  type: FieldType
  label: string
  required: boolean
  placeholder?: string
}

export type TaskStep = {
  id: string
  title: string
  description: string
  fields: StepField[]
  allowComments: boolean

  // NEW
  allow_proof: boolean

  // NEW
  has_cost: boolean
  cost?: number
}