<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { home, login } from '@/routes';


defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Forum', href: home() }],
    },
});

defineProps<{
    thread: {
        id: number;
        title: string;
        description: string;
    };
    topics: {
        id: number;
        title: string;
        created_at: string;
        replies_count: number;
        user: { id: number; username: string } | null;
    }[];
}>();
const page = usePage();

function formatDate(value: string) {
    return new Date(value).toLocaleDateString('nl-NL');
}
</script>

<template>
    <Head :title="thread.title" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <Heading :title="thread.title" />
                <p class="-mt-6 text-sm whitespace-pre-line text-muted-foreground">
                    {{ thread.description }}
                </p>
            </div>

            <Button v-if="page.props.auth.user" as-child class="shrink-0">
                <Link :href="`/threads/${thread.id}/topics/create`">
                    <Plus />
                    Nieuw topic
                </Link>
            </Button>
            <Link
                v-else
                :href="login()"
                class="shrink-0 text-sm text-muted-foreground underline underline-offset-4"
            >
                Log in om een topic te starten
            </Link>
        </div>
        <p v-if="topics.length === 0" class="text-sm text-muted-foreground">
            In deze thread zijn nog geen topics.
        </p>

        <div v-else class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <Link
                v-for="topic in topics"
                :key="topic.id"
                :href="`/topics/${topic.id}`"
                class="flex items-center justify-between gap-4 border-t border-sidebar-border/70 p-4 transition-colors first:border-t-0 hover:bg-muted/50"
            >
                <div class="min-w-0">
                    <h3 class="truncate font-medium">{{ topic.title }}</h3>
                    <p class="text-xs text-muted-foreground">
                        {{ topic.user?.username ?? 'Verwijderde gebruiker' }}
                        · {{ formatDate(topic.created_at) }}
                    </p>
                </div>
                <span class="shrink-0 text-xs text-muted-foreground">
                    {{ topic.replies_count }} replies
                </span>
            </Link>
        </div>
    </div>
</template>