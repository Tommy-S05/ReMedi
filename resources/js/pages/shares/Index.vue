<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useTranslations } from '@/composables/useTranslations';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { AlertTriangleIcon, ArrowRight, FileText, Pill, Share2, UserCheck, UserMinus } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

interface ShareableResource {
    id: number;
    name?: string;
    title?: string;
}

interface Share {
    id: number;
    owner_user_id: number;
    shared_with_user_id: number | null;
    shared_with_email: string;
    shareable_type: string;
    shareable_id: number;
    status: 'pending' | 'accepted' | 'revoked' | 'expired';
    token: string;
    expires_at: string | null;
    created_at: string;
    updated_at: string;
    status_label: string;
    shareable_type_label: string;
    shareable: ShareableResource;
    owner?: {
        id: number;
        name: string;
        email: string;
    };
    shared_with_user?: {
        id: number;
        name: string;
        email: string;
    };
}

const props = defineProps<{
    sharedByMe: Share[];
    sharedWithMe: Share[];
    // sharedWithMe: Record<string, Share[]>;
}>();

const { t, formatDate } = useTranslations();

const showRevokeDialog = ref(false);
const shareToRevoke = ref<Share | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Dashboard'),
        href: route('dashboard'),
    },
    {
        title: t('Shared Resources'),
        href: route('shares.index'),
    },
];

const getResourceIcon = (shareableTypeLabel: string) => {
    switch (shareableTypeLabel) {
        case 'Medication':
            return Pill;
        case 'Prescription':
            return FileText;
        default:
            return Share2;
    }
};

const getResourceRoute = (share: Share) => {
    const type = share.shareable_type.split('\\').pop()?.toLowerCase();
    return route(`${type}s.show`, share.shareable_id);
};

const getResourceName = (share: Share) => {
    return share.shareable?.name || share.shareable?.title || `#${share.shareable_id}`;
};

const getStatusVariant = (status: Share['status']): 'default' | 'secondary' | 'destructive' | 'outline' => {
    switch (status) {
        case 'accepted':
            return 'default';
        case 'pending':
            return 'secondary';
        case 'expired':
        case 'revoked':
            return 'destructive';
        default:
            return 'outline';
    }
};

const sharedWithMeCount = computed(() => props.sharedWithMe.length);
const sharedByMeCount = computed(() => props.sharedByMe.length);

/**
 * Abre el diálogo de confirmación para revocar un compartido.
 * @param {Share} share - El compartido a revocar.
 */
const confirmRevokeShare = (share: Share) => {
    shareToRevoke.value = share;
    showRevokeDialog.value = true;
};

/**
 * Revoke a share
 */
const revokeShare = () => {
    if (!shareToRevoke.value) return;

    router.delete(route('shares.revoke', shareToRevoke.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(t('Share revoked successfully.'));
        },
        onError: (errors) => {
            if (errors.error) {
                toast.error(t(errors.error));
            } else {
                toast.error(t('Failed to revoke share. Please try again.'));
            }
        },
    });
};
</script>

<template>
    <Head :title="t('Shared Resources')" />
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <div class="animate-in fade-in-0 pb-8 duration-500 md:pb-12">
            <div class="container mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold tracking-tight">{{ t('Shared Resources') }}</h1>
                    <p class="text-muted-foreground mt-2">{{ t('Manage resources you share and resources shared with you') }}.</p>
                </div>

                <Tabs default-value="received" class="w-full">
                    <TabsList class="grid w-full grid-cols-2">
                        <TabsTrigger value="received" class="flex items-center gap-2">
                            <UserCheck class="h-4 w-4" />
                            <span>{{ t('Shared with me') }}</span>
                            <Badge v-if="sharedWithMeCount > 0" variant="secondary" class="ml-1">
                                {{ sharedWithMeCount }}
                            </Badge>
                        </TabsTrigger>
                        <TabsTrigger value="sent" class="flex items-center gap-2">
                            <Share2 class="h-4 w-4" />
                            <span>{{ t('Shared by me') }}</span>
                            <Badge v-if="sharedByMeCount > 0" variant="secondary" class="ml-1">
                                {{ sharedByMeCount }}
                            </Badge>
                        </TabsTrigger>
                    </TabsList>

                    <!-- Shared With Me Tab -->
                    <TabsContent value="received" class="mt-6">
                        <div v-if="sharedWithMeCount === 0" class="text-muted-foreground rounded-lg border-2 border-dashed py-12 text-center">
                            <UserCheck class="mx-auto mb-4 h-12 w-12 opacity-50" />
                            <p class="text-lg font-medium">{{ t('No resources shared with you yet') }}</p>
                            <p class="text-sm">{{ t('When someone shares a resource with you, it will appear here') }}.</p>
                        </div>

                        <div v-else class="grid gap-4 md:grid-cols-2">
                            <Card v-for="share in sharedWithMe" :key="share.id" class="hover:border-primary transition-colors">
                                <CardHeader>
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-primary/10 rounded-lg p-2">
                                                <component :is="getResourceIcon(share.shareable_type_label)" class="text-primary h-5 w-5" />
                                            </div>
                                            <div>
                                                <CardTitle class="text-lg">{{ getResourceName(share) }}</CardTitle>
                                                <CardDescription>{{ t(share.shareable_type_label) }}</CardDescription>
                                            </div>
                                        </div>
                                        <Badge :variant="getStatusVariant(share.status)">
                                            {{ t(share.status_label) || t(share.status) }}
                                        </Badge>
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center justify-between">
                                            <span class="text-muted-foreground">{{ t('Shared by') }}:</span>
                                            <span class="font-medium">{{ share.owner?.name || t('Owner') }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-muted-foreground">{{ t('Shared on') }}:</span>
                                            <span>{{ formatDate(share.created_at) }}</span>
                                        </div>
                                    </div>
                                    <Link
                                        :href="getResourceRoute(share)"
                                        class="bg-primary text-primary-foreground hover:bg-primary/90 mt-4 flex items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition-colors"
                                    >
                                        {{ t('View Resource') }}
                                        <ArrowRight class="h-4 w-4" />
                                    </Link>
                                </CardContent>
                            </Card>
                        </div>
                    </TabsContent>

                    <!-- Shared By Me Tab -->
                    <TabsContent value="sent" class="mt-6">
                        <div v-if="sharedByMeCount === 0" class="text-muted-foreground rounded-lg border-2 border-dashed py-12 text-center">
                            <Share2 class="mx-auto mb-4 h-12 w-12 opacity-50" />
                            <p class="text-lg font-medium">{{ t('No resources shared by you yet') }}</p>
                            <p class="text-sm">{{ t('Share a resource to collaborate with others') }}.</p>
                        </div>

                        <div v-else class="grid gap-4 md:grid-cols-2">
                            <Card v-for="share in props.sharedByMe" :key="share.id" class="hover:border-primary transition-colors">
                                <CardHeader>
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-primary/10 rounded-lg p-2">
                                                <component :is="getResourceIcon(share.shareable_type_label)" class="text-primary h-5 w-5" />
                                            </div>
                                            <div>
                                                <CardTitle class="text-lg">{{ getResourceName(share) }}</CardTitle>
                                                <CardDescription>{{ t(share.shareable_type_label) }}</CardDescription>
                                            </div>
                                        </div>
                                        <Badge :variant="getStatusVariant(share.status)">
                                            {{ t(share.status_label) || t(share.status) }}
                                        </Badge>
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center justify-between">
                                            <span class="text-muted-foreground">{{ t('Shared with') }}:</span>
                                            <span class="font-medium">{{ share.shared_with_user?.name || share.shared_with_email }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-muted-foreground">{{ t('Shared on') }}:</span>
                                            <span>{{ formatDate(share.created_at) }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex gap-2">
                                        <Link
                                            :href="getResourceRoute(share)"
                                            class="bg-primary text-primary-foreground hover:bg-primary/90 flex flex-1 items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition-colors"
                                        >
                                            {{ t('View Resource') }}
                                            <ArrowRight class="h-4 w-4" />
                                        </Link>
                                        <Button
                                            v-if="share.status !== 'revoked'"
                                            variant="destructive"
                                            size="default"
                                            @click="confirmRevokeShare(share)"
                                            class="hover:dark:bg-destructive/90 flex items-center gap-2 transition-colors"
                                        >
                                            <UserMinus class="h-4 w-4" />
                                            {{ t('Revoke') }}
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </TabsContent>
                </Tabs>
            </div>
        </div>

        <AlertDialog :open="showRevokeDialog" @update:open="showRevokeDialog = $event">
            <AlertDialogContent v-if="shareToRevoke">
                <AlertDialogHeader>
                    <AlertDialogTitle class="flex items-center">
                        <AlertTriangleIcon class="text-destructive mr-2 h-5 w-5" />
                        {{ t('Confirm Revocation') }}
                    </AlertDialogTitle>
                    <AlertDialogDescription>
                        {{
                            t('Are you sure you want to revoke the share :name? This action cannot be undone.', {
                                name: getResourceName(shareToRevoke),
                            })
                        }}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        @click="
                            showRevokeDialog = false;
                            shareToRevoke = null;
                        "
                        class="cursor-pointer"
                    >
                        {{ t('Cancel') }}
                    </AlertDialogCancel>
                    <AlertDialogAction
                        @click="revokeShare()"
                        class="bg-destructive text-destructive-foreground hover:bg-destructive/90 cursor-pointer"
                    >
                        {{ t('Revoke Share') }}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AuthenticatedLayout>
</template>
