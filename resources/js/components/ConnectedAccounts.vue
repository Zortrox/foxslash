<script setup lang="ts">
import { Form, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ConnectedAccountController from '@/actions/App/Http/Controllers/ConnectedAccountController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import SteamIcon from '@/components/SteamIcon.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { redirect } from '@/routes/oauth';

type SharedAccount = {
    id: number;
    provider: string;
    avatar_path: string | null;
    created_at: string | null;
};

type OAuthProps = {
    hasPassword: boolean;
    connectedAccounts: SharedAccount[];
};

const page = usePage();
const oauth = computed(() => page.props.oauth as OAuthProps);
const steamAccount = computed(() =>
    oauth.value.connectedAccounts.find(
        (account) => account.provider === 'steam',
    ),
);
const error = computed(() => page.props.errors?.oauth as string | undefined);
</script>

<template>
    <div class="space-y-6">
        <Heading
            variant="small"
            title="Connected accounts"
            description="Log in with Steam and let steamchecker read your library and wishlist"
        />

        <InputError :message="error" />

        <div
            class="flex items-center justify-between gap-4 rounded-lg border border-border p-4"
        >
            <div class="flex items-center gap-3">
                <img
                    v-if="steamAccount?.avatar_path"
                    :src="steamAccount.avatar_path"
                    alt=""
                    class="size-10 rounded-full"
                />
                <SteamIcon v-else class="size-10 p-2 text-muted-foreground" />
                <div>
                    <p class="font-medium">Steam</p>
                    <p class="text-sm text-muted-foreground">
                        {{
                            steamAccount
                                ? `Connected ${steamAccount.created_at}`
                                : 'Not connected'
                        }}
                    </p>
                </div>
            </div>

            <Button v-if="!steamAccount" variant="secondary" as-child>
                <a :href="redirect.url({ provider: 'steam' })">Connect</a>
            </Button>

            <Dialog v-else-if="oauth.hasPassword">
                <DialogTrigger as-child>
                    <Button variant="secondary">Disconnect</Button>
                </DialogTrigger>
                <DialogContent>
                    <Form
                        v-bind="
                            ConnectedAccountController.destroy.form({
                                connectedAccount: steamAccount.id,
                            })
                        "
                        :options="{ preserveScroll: true }"
                        reset-on-success
                        class="space-y-6"
                        v-slot="{ errors, processing }"
                    >
                        <DialogHeader class="space-y-3">
                            <DialogTitle>Disconnect Steam?</DialogTitle>
                            <DialogDescription>
                                You will no longer be able to log in with Steam
                                until you connect it again. Enter your password
                                to confirm.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="disconnect-password" class="sr-only"
                                >Password</Label
                            >
                            <PasswordInput
                                id="disconnect-password"
                                name="password"
                                placeholder="Password"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button variant="secondary">Cancel</Button>
                            </DialogClose>
                            <Button
                                type="submit"
                                variant="destructive"
                                :disabled="processing"
                            >
                                Disconnect
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>

            <p v-else class="max-w-48 text-right text-xs text-muted-foreground">
                Set a password in Security before disconnecting, so you can
                still log in.
            </p>
        </div>
    </div>
</template>
