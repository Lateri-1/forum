<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
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

const form = useForm({
    title: '',
    description: '',
});

function submit() {
    form.post('/threads');
}
</script>

<template>
    <Head title="Nieuwe thread" />

    <form class="flex max-w-2xl flex-col gap-6 p-4" @submit.prevent="submit">
        <Heading title="Nieuwe thread" description="Een nieuwe categorie voor het forum" />

        <div class="grid gap-2">
            <Label for="title">Titel</Label>
            <Input id="title" v-model="form.title" required />
            <InputError :message="form.errors.title" />
        </div>

        <div class="grid gap-2">
            <Label for="description">Beschrijving</Label>
            <textarea
                id="description"
                v-model="form.description"
                rows="3"
                required
                maxlength="500"
                class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30"
            ></textarea>
            <InputError :message="form.errors.description" />
        </div>

        <div>
            <Button type="submit" :disabled="form.processing">Opslaan</Button>
        </div>
    </form>
</template>