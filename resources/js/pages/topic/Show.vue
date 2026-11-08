<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { home, login } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Forum', href: home() }],
    },
});

type Author = { id: number; username: string } | null;

const props = defineProps<{
    topic: {
        id: number;
        title: string;
        body: string;
        created_at: string;
        user: Author;
        thread: { id: number; title: string };
    };
    replies: {
        id: number;
        body: string;
        created_at: string;
        user: Author;
    }[];
}>();

const page = usePage();

const form = useForm({
    body: '',
});

function submit() {
    form.post(`/topics/${props.topic.id}/replies`, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function formatDate(value: string) {
    return new Date(value).toLocaleString('nl-NL');
}
</script>

<template>
    <Head :title="topic.title" />

    <div class="flex max-w-3xl flex-col gap-6 p-4">
        <Link
            :href="`/threads/${topic.thread.id}`"
            class="text-sm text-muted-foreground hover:underline"
        >
            ← {{ topic.thread.title }}
        </Link>

        <article class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
            <Heading :title="topic.title" />
            <p class="-mt-6 mb-4 text-xs text-muted-foreground">
                {{ topic.user?.username ?? 'Verwijderde gebruiker' }}
                · {{ formatDate(topic.created_at) }}
            </p>
            <p class="text-sm whitespace-pre-line">{{ topic.body }}</p>
        </article>

        <section class="flex flex-col gap-3">
            <Heading variant="small" :title="`Replies (${replies.length})`" />

            <p v-if="replies.length === 0" class="text-sm text-muted-foreground">
                Nog geen replies. Wees de eerste!
            </p>

            <div
                v-for="reply in replies"
                :key="reply.id"
                class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
            >
                <p class="mb-2 text-xs text-muted-foreground">
                    {{ reply.user?.username ?? 'Verwijderde gebruiker' }}
                    · {{ formatDate(reply.created_at) }}
                </p>
                <p class="text-sm whitespace-pre-line">{{ reply.body }}</p>
            </div>
        </section>

        <form v-if="page.props.auth.user" class="flex flex-col gap-3" @submit.prevent="submit">
            <Label for="body">Jouw reply</Label>
            <textarea
                id="body"
                v-model="form.body"
                rows="4"
                required
                class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30"
            ></textarea>
            <InputError :message="form.errors.body" />
            <div>
                <Button type="submit" :disabled="form.processing">Plaatsen</Button>
            </div>
        </form>

        <p v-else class="text-sm text-muted-foreground">
            <Link :href="login()" class="text-foreground underline underline-offset-4">Log in</Link>
            om te reageren.
        </p>
    </div>
</template>