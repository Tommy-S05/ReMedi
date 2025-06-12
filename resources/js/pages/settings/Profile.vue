<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { useTranslations } from '@/composables/useTranslations';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { cn } from '@/lib/utils';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';

// Interfaz para un grupo de zonas horarias (por continente/región)
interface TimezoneGroup {
    [timezone: string]: string;
}

// Interfaz para la colección completa de timezones agrupados
interface Timezones {
    [continent: string]: TimezoneGroup;
}

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    timezones: Timezones;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const { t } = useTranslations();

const form = useForm({
    name: user.name,
    email: user.email,
    timezone: user.timezone ?? 'UTC',
});

const open = ref(false);

const selectedTimezone = computed(() => {
    return form.timezone;
});

/**
 * Maneja la selección de un ítem en el combobox.
 * @param {string} timezone - El valor de la zona horaria seleccionada.
 */
function handleSelectTimezone(timezone: string) {
    form.timezone = timezone;
    open.value = false;
}

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Full name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="timezone">{{ t('Timezone') }}</Label>
                        <Popover v-model:open="open">
                            <PopoverTrigger as-child>
                                <Button
                                    variant="outline"
                                    role="combobox"
                                    :aria-expanded="open"
                                    class="hover:dark:text-remedi-white w-full justify-between"
                                >
                                    {{ selectedTimezone || t('Select timezone') + '...' }}
                                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent class="w-[--radix-popover-trigger-width] p-0">
                                <Command v-model="form.timezone">
                                    <CommandInput :placeholder="t('Search timezone') + '...'" />
                                    <CommandEmpty>{{ t('No timezone found.') }}</CommandEmpty>
                                    <CommandList>
                                        <CommandGroup
                                            v-for="(timezones, continent) in props.timezones"
                                            :key="continent"
                                            :heading="continent.toString().toUpperCase()"
                                        >
                                            <CommandItem
                                                v-for="(timezone, key) in timezones"
                                                :key="key"
                                                :value="timezone"
                                                @select="handleSelectTimezone(timezone)"
                                            >
                                                <Check
                                                    :class="
                                                        cn(
                                                            'hover:text-remedi-dark-blue mr-2 h-4 w-4',
                                                            form.timezone === timezone ? 'opacity-100' : 'opacity-0',
                                                        )
                                                    "
                                                />
                                                {{ timezone }}
                                            </CommandItem>
                                        </CommandGroup>
                                    </CommandList>
                                </Command>
                            </PopoverContent>
                        </Popover>
                        <InputError class="mt-2" :message="form.errors.timezone" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="text-muted-foreground -mt-4 text-sm">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
