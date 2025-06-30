import { usePage } from "@inertiajs/vue3";

export function usePermission() {
    const hasPermission = (permission) => usePage().props.auth.permissions.includes(permission);
    const hasRole = (role) => usePage().props.auth.roles.includes(role);

    return {hasPermission, hasRole};
}