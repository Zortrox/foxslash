<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import SetPasswordController from '@/actions/App/Http/Controllers/Auth/SetPasswordController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';

defineProps<{
    passwordRules: string;
}>();
</script>

<template>
    <div class="space-y-6">
        <Heading
            variant="small"
            title="Set a password"
            description="You signed up with Steam. Set a password to also log in with your email, and to turn on two-factor authentication or passkeys."
        />

        <Form
            v-bind="SetPasswordController.store.form()"
            :options="{ preserveScroll: true }"
            reset-on-success
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    placeholder="Password"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirm password</Label>
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    placeholder="Confirm password"
                    :passwordrules="passwordRules"
                />
            </div>

            <Button :disabled="processing" data-test="set-password-button">
                Set password
            </Button>
        </Form>
    </div>
</template>
