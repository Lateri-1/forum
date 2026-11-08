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
    topic: { id: number; title: string; body: string };
}>();

const form = useForm({
    title: props.topic.title,
    body: props.topic.body,
});

function submit() {
    form.patch(`/topics/${props.topic.id}`);
}
</script>

<template>
    <Head title="Topic bewerken" />

    <form class="flex max-w-3xl flex-col gap-6 p-4" @submit.prevent="submit">
        <Link :href="`/topics/${topic.id}`" class="text-sm text-muted-foreground hover:underline">
            ← Terug naar topic
        </Link>

        <Heading title="Topic bewerken" />

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
            <Button type="submit" :disabled="form.processing">Opslaan</Button>
        </div>
    </form>
</template>