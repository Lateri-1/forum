<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { home } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Forum', href: home() }],
    },
});

const props = defineProps<{
    thread: { id: number; title: string };
}>();

const form = useForm({
    title: '',
    body: '',
});

function submit() {
    form.post(`/threads/${props.thread.id}/topics`);
}
</script>

<template>
    <Head title="Nieuw topic" />

    <form class="flex max-w-3xl flex-col gap-6 p-4" @submit.prevent="submit">
        <Link :href="`/threads/${thread.id}`" class="text-sm text-muted-foreground hover:underline">
            ← {{ thread.title }}
        </Link>

        <Heading title="Nieuw topic" :description="`In ${thread.title}`" />

        <div class="grid gap-2">
            <Label for="title">Titel</Label>
            <Input id="title" v-model="form.title" required />
            <InputError :message="form.errors.title" />
        </div>

        <div class="grid gap-2">
            <Label for="body">Bericht</Label>
            <textarea
                id="body"
                v-model="form.body"
                rows="8"
                required
                class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30"
            ></textarea>
            <InputError :message="form.errors.body" />
        </div>

        <div>
            <Button type="submit" :disabled="form.processing">Plaatsen</Button>
        </div>
    </form>
</template>