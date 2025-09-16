<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { useTranslations } from '@/composables/useTranslations';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Label } from '@/components/ui/label';

// Define the properties the component accepts
const props = defineProps<{
    resource: {
        id: number;
        type: 'medication' | 'prescription';
        name?: string | undefined;
    };
    modelValue: boolean;
}>();

// Define the events the component emits
const emit = defineEmits(['update:modelValue']);

const { t } = useTranslations();

// Setup the form with Inertia's useForm hook
const form = useForm({
    email: '',
    shareable_id: props.resource.id,
    shareable_type: props.resource.type,
});

const submit = () => {
    form.post(route('shares.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            toast.success(t('Invitation sent successfully.'));
        },
        onError: () => {
            toast.error(t('Please check the form for errors.'));
        },
    });
};

const closeModal = () => {
    emit('update:modelValue', false);
    // We wait a bit for the closing animation to finish before resetting the form
    setTimeout(() => {
        form.reset();
        form.clearErrors();
    }, 300);
};
</script>

<template>
    <Dialog :open="modelValue" @update:open="closeModal">
        <!-- <DialogContent class="sm:max-w-[425px]"> -->
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ t('Share') }} "{{ resource.name }}"</DialogTitle>
                <DialogDescription>
                    {{ t('Enter the email of the person you want to share this resource with. They will receive an email with an invitation link.') }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <Label for="email" class="sr-only">
                        {{ t('Email') }}
                    </Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        :placeholder="t('email@example.com')"
                        required
                        autofocus
                        :class="{ 'border-red-500': form.errors.email }"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <DialogFooter>
                    <Button type="submit" :disabled="form.processing">
                        {{ t('Send Invitation') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
