<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { home } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Forum', href: home() }],
    },
});

defineProps<{
    threads: {
        id: number;
        title: string;
        description: string;
        topics_count: number;
    }[];
}>();
</script>

<template>
    <Head title="Forum" />

    <div class="flex flex-col gap-6 p-4">
        <Heading title="Forum" description="Kies een categorie" />

        <p v-if="threads.length === 0" class="text-sm text-muted-foreground">
            Er zijn nog geen threads.
        </p>

        <div class="grid gap-3">
            <Link
                v-for="thread in threads"
                :key="thread.id"
                :href="`/threads/${thread.id}`"
                class="block rounded-xl border border-sidebar-border/70 p-4 transition-colors hover:bg-muted/50 dark:border-sidebar-border"
            >
                <div class="flex items-baseline justify-between gap-4">
                    <h2 class="font-semibold">{{ thread.title }}</h2>
                    <span class="shrink-0 text-xs text-muted-foreground">
                        {{ thread.topics_count }} topics
                    </span>
                </div>
                <p class="mt-1 truncate text-sm text-muted-foreground">
                    {{ thread.description }}
                </p>
            </Link>
        </div>
    </div>
</template>