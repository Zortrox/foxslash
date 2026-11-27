import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

/**
 * Spatie roles and permissions shared in HandleInertiaRequests.
 * "Super Admin" passes every check, matching the Gate::before rule on the server.
 */
export function usePermission() {
    const page = usePage();
    const user = computed(() => page.props.auth.user);

    const isSuperAdmin = computed(
        () => user.value?.roles?.includes('Super Admin') ?? false,
    );

    const hasPermission = (name: string): boolean =>
        isSuperAdmin.value ||
        (user.value?.permissions?.includes(name) ?? false);

    return { isSuperAdmin, hasPermission };
}
