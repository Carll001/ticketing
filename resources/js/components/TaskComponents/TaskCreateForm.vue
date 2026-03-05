
<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
// Idinagdag na imports para sa Combobox
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { Textarea } from '@/components/ui/textarea';
import { ChevronDownIcon, Check, ChevronsUpDown } from 'lucide-vue-next';
import InputError from '../InputError.vue';
import Separator from '../ui/separator/Separator.vue';
import { cn } from '@/lib/utils'; // Utility para sa conditional classes


</script>

<template>
    <div class="space-y-4">
        <form>
            <section class="flex justify-between">
                <div class="flex gap-2">
                    <Heading title="Add Task" />
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        size="sm"
                        type="button"
                        variant="destructive"
                        >Discard</Button
                    >
                    <Button size="sm" type="submit">
                        Create
                    </Button>
                </div>
            </section>

            <section class="grid grid-cols-[3fr_2fr] gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Task Details</CardTitle>
                        <CardDescription>Description of tas k</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <section class="flex items-center gap-4">
                                <div class="w-full ">
                                    <Label for="task-title"
                                        >Task title
                                        <span class="text-lg text-red-500"
                                            >*</span
                                        ></Label
                                    >
                                    <Input
                                        id="task-title"
                                        placeholder="task title"
                                    />
                                    <InputError/>
                                </div>
                                <div class="flex flex-col gap-3">
                                    <Label for="date" class="px-1">
                                        Due date <span class="text-muted-foreground text-xs"> (Optional) </span>
                                    </Label>
                                    <Popover v-slot="{ close }">
                                        <PopoverTrigger as-child>
                                            <Button
                                                id="date"
                                                variant="outline"
                                                class="w-48 justify-between font-normal"
                                            >
                                                <ChevronDownIcon />
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent
                                            class="w-auto overflow-hidden p-0"
                                            align="start"
                                        >
                                            <Calendar
                                                layout="month-and-year"
                                            />
                                        </PopoverContent>
                                    </Popover>
                                </div>
                            </section>
                            <section class="space-y-4">
                                <Label for="task-title">Task description <span class="text-muted-foreground text-xs"> (Optional) </span></Label>
                                <Textarea
                                    id="task-title"
                                    placeholder="task title"
                                />
                                <InputError
                                />
                            </section>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle>Additional details</CardTitle>
                        <CardDescription>additional details</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <Label for="tast-type"
                                >Step order
                                <span class="text-lg text-red-500"
                                    >*</span
                                ></Label
                            >
                            <Select id="step-order">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select a order" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Task order</SelectLabel>
                                        <SelectItem value="sequential">
                                            Sequential
                                        </SelectItem>
                                        <SelectItem value="random">
                                            Random
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError />
                        </div>

                        <Separator class="my-4" />

                        <div class="space-y-4">
                            <Label for="assigned_to">Assign department</Label>
                            
                            <Popover>
                                <PopoverTrigger as-child>
                                    <Button
                                        variant="outline"
                                        role="combobox"
                                        class="w-full justify-between"
                                    >
                                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-full p-0">
                                    <Command>
                                        <CommandInput placeholder="Search department..." />
                                        <CommandEmpty>No department found.</CommandEmpty>
                                        <CommandList>
                                            <CommandGroup>
                                                <CommandItem
                                                    value="anyone"
                                                >
                                                    <Check
                                                        :class="cn(
                                                            'mr-2 h-4 w-4',
                                                        )"
                                                    />
                                                    Anyone
                                                </CommandItem>

                                                <!-- <CommandItem
                                                   
                                                >
                                                    <Check

                                                    />
                                                  
                                                </CommandItem> -->
                                            </CommandGroup>
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>
                            
                            <InputError/>
                        </div>
                    </CardContent>
                </Card>
            </section>
        </form>

        <!-- <pre>{{ form }}</pre> -->
    </div>

    <div class="">
        <form  class="space-y-2">
            <section class="flex justify-between items-start">
                <Heading title="Add Task Step" />
            </section>

            <section class="grid grid-cols-[2fr_1fr] gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Step Details</CardTitle>
                        <CardDescription>Description of step</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <section class="flex items-start gap-4">
                                <div class="w-full">
                                    <Label for="step-title">Step title
                                        <span class="text-lg text-red-500">*</span></Label>
                                    <Input id="step-title" placeholder="step title" />
                                    <InputError />
                                </div>
                            </section>
                            <section class="space-y-4">
                                <Label for="step-title">Step description</Label>
                                <Textarea id="step-title" placeholder="step title" />
                                <InputError />
                            </section>
                        </div>
                        <div class="space-y-6">
                            <div class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <Label class="text-base font-semibold">User Input Fields</Label>
                                    <p class="text-xs text-zinc-500">Define what information the User must provide.</p>
                                </div>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button size="sm" variant="outline" class="gap-2">
                                            <Plus class="w-4 h-4" /> Add Field
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem >Textarea (Description)
                                        </DropdownMenuItem>
                                        <DropdownMenuItem >Checkbox (Confirmation)
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>Small Input (Text/Number)
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>

                            <div 
                                class="border-2 border-dashed border-zinc-800 rounded-lg p-6 text-center">
                                <p class="text-sm text-zinc-500">No input fields added. The User will just mark this
                                    step as complete.</p>
                            </div>

                            <div class="space-y-4">
                                <Label
                                    class="text-[10px] uppercase font-black text-zinc-500 tracking-[0.2em] border-b border-zinc-800/50 pb-1 block">
                                     type  type === 'Checkbox' ? 'es' : 's' 
                                </Label>

                                <div class="space-y-4">
                                    <section
                                        class="relative p-4 rounded-xl border border-zinc-300  space-y-3 group">

                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 mr-4">
                                                <Label
                                                    class="text-xs dark:text-white text-black uppercase font-bold mb-1 block">Field
                                                    Label / Question</Label>
                                                <Input 
                                                    placeholder="e.g.Checkbox Check if confirmed Enter detail name "/>
                                            </div>
                                            <Button variant="ghost" size="icon"
                                                class="h-8 w-8 text-zinc-500 hover:text-red-500 shrink-0"
                                              >
                                                <Trash2Icon class="w-4 h-4" />
                                            </Button>
                                        </div>

                                        <div
                                            class="mt-4 pt-4 border-t border-zinc-800/50 opacity-80 grayscale pointer-events-none">
                                            <p class="text-xs uppercase font-bold text-black dark:text-white mb-2">User Response
                                                Preview</p>

                                            <div
                                                class="['flex gap-3', type === 'Checkbox' ? 'flex-row items-center' : 'flex-col items-start']">

                                                <div class="[type=== 'Checkbox' ? 'w-auto' : 'w-full order-2']">
                                                    <Checkbox  class="rounded-sm" />

                                                    <Input  disabled
                                                        placeholder="`User will enter ${field.label || 'data'}...`"
                                                        class="h-8 text-xs " />

                                                    <Textarea  disabled
                                                        placeholder="user will provide"
                                                        class="min-h-[60px] text-xs  resize-none" />
                                                </div>

                                                <span
                                                  >
                                                    checkbox & etc
                                                </span>
                                            </div>
                                        </div>

                                        <InputError
                                            />
                                    </section>
                                </div>

                            </div>
                            <div  class="space-y-4 my-4">
                                <Label class="text-[10px] text-zinc-500 uppercase font-bold mb-1 block">Cost Field
                                    Preview</Label>
                                <Input disabled placeholder="User will enter cost amount..."
                                    class="h-8 text-xs" />
                            </div>
                        </div>
                    </CardContent>
                </Card>
                <Card class="h-fit">
                    <CardHeader>
                        <CardTitle>Additional details</CardTitle>
                        <CardDescription>additional details</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <Label for="task-type">Task type</Label>
                            <Select id="task-type">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select a Type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="preset">Preset</SelectItem>
                                    <SelectItem value="custom">Custom</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div
                            class="space-y-4 mt-4 animate-in fade-in slide-in-from-top-1">
                            <Label for="preset-selection">Choose Preset</Label>
                            <Select class="w-full"
                               >
                                <SelectTrigger class="w-full">
                                    <SelectValue class="w-full" placeholder="Select a template..." />
                                </SelectTrigger>
                                <SelectContent>
                                 
                                       >
                                         preset.name 
                                    
                                </SelectContent>
                            </Select>
                            <p class="text-[10px] text-zinc-500 italic">Selecting a preset will populate the input
                                fields automatically.</p>
                        </div>

                        <Separator class="my-4" />
                        <section class="space-y-4">
                            <Label for="step-type">Assign to </Label>
                            <Popover >
                                <PopoverTrigger as-child>
                                    <Button variant="outline" role="combobox" :class="cn(
                                        'w-full justify-between',
                                       
                                    )
                                        ">
                                        assignedUser 
                                        <ChevronsUpDownIcon class="opacity-50" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-full p-0">
                                    <Command>
                                        <CommandInput class="h-9 w-xs" placeholder="Search user..." />
                                        <CommandList>
                                            <CommandEmpty>No user found.</CommandEmpty>
                                            <!-- <CommandGroup>
                                                <CommandItem >
                                                    Anyone
                                                    <CheckIcon 
                                                         class="ml-auto" />
                                                </CommandItem>
                                                <CommandItem >
                                                  

                                                    <CheckIcon 
                                                        String(user.id)
                                                         class="ml-auto" />
                                                </CommandItem>
                                            </CommandGroup> -->
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>
                            <InputError />
                        </section>


                        <Separator class="my-4" />
                        <section class="space-y-4">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <Checkbox id="has-cost"  />
                                    <Label for="has-cost">has cost</Label>
                                </div>
                                <p class="text-sm text-zinc-500">
                                    Check this if the step has a cost; you can enter an amount below.
                                </p>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <Checkbox />
                                    <Label for="add-new">Add another</Label>
                                </div>
                                <p class="text-sm text-zinc-500">
                                    Keep checked to stay on this form and quickly add another step.
                                </p>
                            </div>
                        </section>
                    </CardContent>
                </Card>
            </section>
        </form>
        <!-- <pre>{{ props }}</pre> -->
        <!-- <pre>{{ form }}</pre> -->
    </div>
</template>
