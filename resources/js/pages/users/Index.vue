<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Users', href: '/users' }],
    },
});

defineProps<{
    users: { id: number; username: string; email: string }[];
}>();

const page = usePage();

function remove(id: number) {
    if (!confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')) {
        return;
    }

    router.delete(`/users/${id}`);
}
</script>

<template>
    <Head title="Users" />

    <div class="flex flex-col gap-6 p-4">
        <Heading title="Users" description="Alle geregistreerde gebruikers" />

        <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left text-muted-foreground">
                    <tr>
                        <th class="px-4 py-3 font-medium">ID</th>
                        <th class="px-4 py-3 font-medium">Username</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" :key="user.id" class="border-t border-sidebar-border/70">
                        <td class="px-4 py-3 text-muted-foreground">{{ user.id }}</td>
                        <td class="px-4 py-3">
                            <Link :href="`/users/${user.id}`" class="font-medium hover:underline">
                                {{ user.username }}
                            </Link>
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">{{ user.email }}</td>
                        <td class="px-4 py-3 text-right">
                            <Button
                                v-if="user.id !== page.props.auth.user.id"
                                variant="destructive"
                                size="sm"
                                @click="remove(user.id)"
                            >
                                Verwijderen
                            </Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>