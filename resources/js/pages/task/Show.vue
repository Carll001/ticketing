<script setup lang="ts">
import { computed, reactive } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Checkbox } from '@/components/ui/checkbox'

type ShowTask = {
  id: string
  title: string
  description?: string | null
  creator_id?: string | null
  department_assigned?: { id?: string; name?: string } | null
  steps?: Array<{
    id: string
    title: string
    description?: string | null
    claimed_by_user_id?: string | null
    claimed_by?: { id?: string; name?: string } | null
    allow_proof?: boolean
    allow_comments?: boolean
    has_cost?: boolean
    expected_cost?: number | string | null
    submitted_cost?: number | string | null
    proof_text?: string | null
    proof_type?: 'text' | 'image' | 'file' | null
    proof_file_name?: string | null
    proof_file_mime?: string | null
    proof_file_url?: string | null
    proof_files?: Array<{ name?: string | null; mime?: string | null; size?: number | null; url?: string | null }>
    comments?: Array<{ id: string; message: string; user?: { id?: string; name?: string } | null }>
    fields?: Array<{ id: string; label: string; type: string; placeholder?: string | null; value?: string | boolean | null }>
  }>
}

const props = defineProps<{
  task: { data: ShowTask } | ShowTask
}>()

const task = 'data' in props.task ? props.task.data : props.task
const page = usePage()
const authUserId = computed<string | null>(() => {
  const user = (page.props as { auth?: { user?: { id?: string } } }).auth?.user
  return user?.id ?? null
})

type UiField = {
  id: string
  label: string
  type: string
  placeholder?: string | null
}

type UiStep = {
  id: string
  title: string
  description?: string | null
  fields: UiField[]
  claimedByUserId: string | null
  responses: Record<string, string | boolean>
  allowProof: boolean
  allowComments: boolean
  hasCost: boolean
  expectedCost: string
  submittedCost: string
  proofText: string
  proofType: 'text' | 'image' | 'file'
  proofFiles: File[]
  existingProofFileUrl: string | null
  existingProofFileName: string | null
  existingProofFiles: Array<{ name: string; url: string | null }>
  comments: Array<{ id: string; message: string; userName: string }>
  newComment: string
}

const uiSteps = reactive<UiStep[]>(
  (task.steps ?? []).map((step) => ({
    id: step.id,
    title: step.title,
    description: step.description,
    fields: (step.fields ?? []).map((field) => ({
      id: field.id,
      label: field.label,
      type: field.type,
      placeholder: field.placeholder ?? null,
    })),
    claimedByUserId: step.claimed_by_user_id ?? null,
    responses: Object.fromEntries(
      (step.fields ?? []).map((field) => [field.id, ((field as { value?: string | boolean | null }).value ?? (field.type === 'checkbox' ? false : ''))]),
    ),
    allowProof: Boolean(step.allow_proof),
    allowComments: step.allow_comments !== false,
    hasCost: Boolean(step.has_cost),
    expectedCost: step.expected_cost == null ? '' : String(step.expected_cost),
    submittedCost: step.submitted_cost == null ? '' : String(step.submitted_cost),
    proofText: step.proof_text ?? '',
    proofType: (step.proof_type as 'text' | 'image' | 'file' | null) ?? 'text',
    proofFiles: [],
    existingProofFileUrl: step.proof_file_url ?? null,
    existingProofFileName: step.proof_file_name ?? null,
    existingProofFiles: (step.proof_files ?? [])
      .map((file) => ({
        name: file.name || 'Attachment',
        url: file.url || null,
      })),
    comments: (step.comments ?? []).map((comment) => ({
      id: comment.id,
      message: comment.message,
      userName: comment.user?.name || 'User',
    })),
    newComment: '',
  })),
)

const claimingState = reactive<Record<string, boolean>>({})
const submittingState = reactive<Record<string, boolean>>({})

const isClaimedByCurrentUser = (step: UiStep) =>
  !!authUserId.value && step.claimedByUserId === authUserId.value

const isTaskCreator = computed(() => !!authUserId.value && task.creator_id === authUserId.value)

const isClaimedByOtherUser = (step: UiStep) =>
  !!step.claimedByUserId && step.claimedByUserId !== authUserId.value

const canTakeStep = (step: UiStep) =>
  !!authUserId.value && !step.claimedByUserId

const canCommentStep = (step: UiStep) =>
  step.allowComments && (isClaimedByCurrentUser(step) || isTaskCreator.value)

const takeStep = (step: UiStep) => {
  if (!authUserId.value) return
  if (step.claimedByUserId) return

  claimingState[step.id] = true
  router.post(
    `/task/${task.id}/steps/${step.id}/claim`,
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        step.claimedByUserId = authUserId.value
      },
      onFinish: () => {
        claimingState[step.id] = false
      },
    },
  )
}

const submitStepResponse = (step: UiStep) => {
  if (!isClaimedByCurrentUser(step)) return

  submittingState[step.id] = true
  const payload: Record<string, unknown> = {
    responses: step.fields.map((field) => ({
      field_id: field.id,
      value: step.responses[field.id] ?? null,
    })),
    submitted_cost: step.hasCost ? (step.submittedCost === '' ? null : Number(step.submittedCost)) : null,
    proof_type: step.allowProof ? step.proofType : null,
    proof_text: step.allowProof && step.proofType === 'text' ? (step.proofText || null) : null,
  }

  if (step.allowProof && (step.proofType === 'image' || step.proofType === 'file') && step.proofFiles.length > 0) {
    payload.proof_files = step.proofFiles
  }

  router.post(
    `/task/${task.id}/steps/${step.id}/respond`,
    payload,
    {
      preserveScroll: true,
      forceFormData: true,
      onFinish: () => {
        submittingState[step.id] = false
      },
    },
  )
}

const submitStepComment = (step: UiStep) => {
  if (!canCommentStep(step)) return
  const message = step.newComment.trim()
  if (!message) return

  router.post(
    `/task/${task.id}/steps/${step.id}/comment`,
    { message },
    {
      preserveScroll: true,
      onSuccess: () => {
        const currentUserName = ((page.props as { auth?: { user?: { name?: string } } }).auth?.user?.name) || 'You'
        step.comments.push({
          id: `${Date.now()}`,
          message,
          userName: currentUserName,
        })
        step.newComment = ''
      },
    },
  )
}
</script>

<template>
  <Head :title="`Task: ${task.title}`" />

  <AppLayout>
    <div class="flex flex-col gap-4 p-4">
      <Card>
        <CardHeader>
          <CardTitle>{{ task.title }}</CardTitle>
        </CardHeader>
        <CardContent class="space-y-3">
          <p class="text-sm text-muted-foreground">{{ task.description || 'No description' }}</p>
          <p class="text-sm">
            Department:
            <span class="font-medium">{{ task.department_assigned?.name || 'Open for anyone' }}</span>
          </p>
          <div class="space-y-2">
            <p class="text-sm font-medium">Steps</p>
            <div v-for="(step, idx) in uiSteps" :key="step.id" class="rounded-md border p-3">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-sm font-medium">Step {{ idx + 1 }}: {{ step.title }}</p>
                  <p class="text-xs text-muted-foreground">{{ step.description || 'No description' }}</p>
                  <p v-if="isTaskCreator && step.claimedByUserId" class="text-xs text-muted-foreground">
                    Taken by: {{ (task.steps?.find(s => s.id === step.id)?.claimed_by?.name) || 'User' }}
                  </p>
                </div>
                <Button
                  v-if="canTakeStep(step)"
                  type="button"
                  size="sm"
                  :disabled="Boolean(claimingState[step.id])"
                  @click="takeStep(step)"
                >
                  {{ claimingState[step.id] ? 'Taking...' : 'Take Step' }}
                </Button>
                <Button
                  v-else-if="isClaimedByCurrentUser(step)"
                  type="button"
                  size="sm"
                  variant="secondary"
                  disabled
                >
                  Your Step
                </Button>
                <Button
                  v-else
                  type="button"
                  size="sm"
                  variant="outline"
                  disabled
                >
                  Taken
                </Button>
              </div>

              <div class="mt-3 space-y-3">
                <div v-for="field in step.fields" :key="field.id" class="space-y-1">
                  <p class="text-xs font-medium">
                    {{ field.label }}
                  </p>

                  <Input
                    v-if="field.type === 'text' || field.type === 'input'"
                    v-model="step.responses[field.id] as string"
                    :disabled="!isClaimedByCurrentUser(step)"
                    :placeholder="field.placeholder || 'Answer here...'"
                  />

                  <Textarea
                    v-else-if="field.type === 'textarea'"
                    v-model="step.responses[field.id] as string"
                    :disabled="!isClaimedByCurrentUser(step)"
                    :placeholder="field.placeholder || 'Answer here...'"
                    class="min-h-[84px]"
                  />

                  <label v-else class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Checkbox
                      :disabled="!isClaimedByCurrentUser(step)"
                      :model-value="Boolean(step.responses[field.id])"
                      @update:model-value="(value) => (step.responses[field.id] = Boolean(value))"
                    />
                    Checkbox
                  </label>
                </div>

                <div v-if="step.hasCost" class="space-y-1">
                  <p class="text-xs font-medium">Cost Submission</p>
                  <Input
                    v-model="step.submittedCost"
                    type="number"
                    min="0"
                    step="0.01"
                    :disabled="!isClaimedByCurrentUser(step)"
                    :placeholder="step.expectedCost ? `Expected: ${step.expectedCost}` : 'Enter cost'"
                  />
                </div>

                <div v-if="step.allowProof" class="space-y-1">
                  <p class="text-xs font-medium">Proof Submission</p>
                  <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                      <Button
                        type="button"
                        size="sm"
                        :variant="step.proofType === 'text' ? 'default' : 'outline'"
                        :disabled="!isClaimedByCurrentUser(step)"
                        @click="step.proofType = 'text'"
                      >
                        Text
                      </Button>
                      <Button
                        type="button"
                        size="sm"
                        :variant="step.proofType === 'image' ? 'default' : 'outline'"
                        :disabled="!isClaimedByCurrentUser(step)"
                        @click="step.proofType = 'image'"
                      >
                        Image
                      </Button>
                      <Button
                        type="button"
                        size="sm"
                        :variant="step.proofType === 'file' ? 'default' : 'outline'"
                        :disabled="!isClaimedByCurrentUser(step)"
                        @click="step.proofType = 'file'"
                      >
                        File
                      </Button>
                    </div>

                    <Textarea
                      v-if="step.proofType === 'text'"
                      v-model="step.proofText"
                      :disabled="!isClaimedByCurrentUser(step)"
                      placeholder="Add proof details"
                      class="min-h-[84px]"
                    />

                    <div v-else class="space-y-1">
                      <Input
                        type="file"
                        :accept="step.proofType === 'image' ? 'image/*' : '*/*'"
                        multiple
                        :disabled="!isClaimedByCurrentUser(step)"
                        @change="step.proofFiles = Array.from(($event.target as HTMLInputElement).files ?? [])"
                      />
                      <p v-if="step.proofFiles.length > 0" class="text-xs text-muted-foreground">
                        Selected {{ step.proofFiles.length }} file(s) ready to upload.
                      </p>
                      <p v-else-if="step.existingProofFileName" class="text-xs text-muted-foreground">
                        Current: {{ step.existingProofFileName }}
                      </p>
                      <div class="space-y-1">
                        <a
                          v-if="step.existingProofFileUrl"
                          class="block text-xs underline"
                          :href="step.existingProofFileUrl"
                          target="_blank"
                        >
                          View Current Proof
                        </a>
                        <template v-for="(file, fileIdx) in step.existingProofFiles" :key="`${step.id}-${fileIdx}`">
                          <a
                            v-if="file.url"
                            class="block text-xs underline"
                            :href="file.url"
                            target="_blank"
                          >
                            {{ file.name }}
                          </a>
                        </template>
                      </div>
                    </div>
                  </div>
                </div>

                <Button
                  type="button"
                  @click="submitStepResponse(step)"
                  :disabled="!isClaimedByCurrentUser(step)"
                  class="w-full"
                >
                  {{ submittingState[step.id] ? 'Submitting...' : 'Submit Step Response' }}
                </Button>
              </div>

              <div v-if="step.allowComments" class="mt-4 space-y-2 rounded-md border p-3">
                <p class="text-xs font-medium">Discussion</p>
                <div v-if="step.comments.length" class="space-y-1">
                  <p v-for="comment in step.comments" :key="comment.id" class="text-xs">
                    <span class="font-medium">{{ comment.userName }}:</span> {{ comment.message }}
                  </p>
                </div>
                <p v-else class="text-xs text-muted-foreground">No discussion yet.</p>

                <div class="flex items-center gap-2">
                  <Input
                    v-model="step.newComment"
                    :disabled="!canCommentStep(step)"
                    placeholder="Write a comment..."
                  />
                  <Button
                    type="button"
                    size="sm"
                    :disabled="!canCommentStep(step) || !step.newComment.trim()"
                    @click="submitStepComment(step)"
                  >
                    Send
                  </Button>
                </div>
              </div>

              <p v-if="isClaimedByOtherUser(step)" class="mt-2 text-xs text-muted-foreground">
                This step is already taken by another user.
              </p>
              <p v-else-if="isClaimedByCurrentUser(step)" class="mt-2 text-xs text-muted-foreground">
                You claimed this step and can submit responses.
              </p>
              <p v-else class="mt-2 text-xs text-muted-foreground">
                Open step. Click "Take Step" to answer this section.
              </p>
            </div>
            <p v-if="uiSteps.length === 0" class="text-xs text-muted-foreground">
              No steps available for this task.
            </p>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
