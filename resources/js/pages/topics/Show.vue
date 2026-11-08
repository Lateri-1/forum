<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { Pencil, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
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

type Reply = {
    id: number;
    body: string;
    created_at: string;
    user: Author;
};

const props = defineProps<{
    topic: {
        id: number;
        title: string;
        body: string;
        created_at: string;
        user: Author;
        thread: { id: number; title: string };
    };
    replies: Reply[];
}>();

const page = usePage();
const isAdmin = page.props.auth.user?.role === 'admin';

function isAuthor(author: Author) {
    return !!page.props.auth.user && author?.id === page.props.auth.user.id;
}

function formatDate(value: string) {
    return new Date(value).toLocaleString('nl-NL');
}

// nieuwe reply
const form = useForm({ body: '' });

function submit() {
    form.post(`/topics/${props.topic.id}/replies`, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

// reply bewerken
const editingId = ref<number | null>(null);
const editForm = useForm({ body: '' });

function startEdit(reply: Reply) {
    editingId.value = reply.id;
    editForm.body = reply.body;
    editForm.clearErrors();
}

function saveEdit(reply: Reply) {
    editForm.patch(`/replies/${reply.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null;
        },
    });
}

// verwijderen (admin)
function removeTopic() {
    if (confirm('Dit topic en alle replies verwijderen?')) {
        router.delete(`/topics/${props.topic.id}`);
    }
}

function removeReply(reply: Reply) {
    if (confirm('Deze reply verwijderen?')) {
        router.delete(`/replies/${reply.id}`, { preserveScroll: true });
    }
}

const textareaClass =
    'w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30';
</script>

<template>
    <Head :title="topic.title" />

    <div class="flex max-w-3xl flex-col gap-6 p-4">
        <Link :href="`/threads/${topic.thread.id}`" class="text-sm text-muted-foreground hover:underline">
            ← {{ topic.thread.title }}
        </Link>

        <article class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
            <div class="flex items-start justify-between gap-4">
                <Heading :title="topic.title" />
                <div class="flex shrink-0 gap-2">
                    <Button v-if="isAuthor(topic.user)" variant="outline" size="sm" as-child>
                        <Link :href="`/topics/${topic.id}/edit`"><Pencil /> Bewerken</Link>
                    </Button>
                    <Button v-if="isAdmin" variant="destructive" size="sm" @click="removeTopic">
                        <Trash2 /> Verwijderen
                    </Button>
                </div>
            </div>
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
                <div class="mb-2 flex items-center justify-between gap-4">
                    <p class="text-xs text-muted-foreground">
                        {{ reply.user?.username ?? 'Verwijderde gebruiker' }}
                        · {{ formatDate(reply.created_at) }}
                    </p>
                    <div v-if="editingId !== reply.id" class="flex gap-1">
                        <Button v-if="isAuthor(reply.user)" variant="ghost" size="sm" @click="startEdit(reply)">
                            <Pencil />
                        </Button>
                        <Button v-if="isAdmin" variant="ghost" size="sm" @click="removeReply(reply)">
                            <Trash2 class="text-destructive" />
                        </Button>
                    </div>
                </div>

                <form v-if="editingId === reply.id" class="flex flex-col gap-2" @submit.prevent="saveEdit(reply)">
                    <textarea v-model="editForm.body" rows="3" required :class="textareaClass"></textarea>
                    <InputError :message="editForm.errors.body" />
                    <div class="flex gap-2">
                        <Button type="submit" size="sm" :disabled="editForm.processing">Opslaan</Button>
                        <Button type="button" variant="ghost" size="sm" @click="editingId = null">Annuleren</Button>
                    </div>
                </form>
                <p v-else class="text-sm whitespace-pre-line">{{ reply.body }}</p>
            </div>
        </section>

        <form v-if="page.props.auth.user" class="flex flex-col gap-3" @submit.prevent="submit">
            <Label for="body">Jouw reply</Label>
            <textarea id="body" v-model="form.body" rows="4" required :class="textareaClass"></textarea>
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