<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Users', href: '/users' }],
    },
});

const props = defineProps<{
    user: {
        id: number;
        name: string;
        username: string;
        email: string;
        role: string;
        created_at: string;
        updated_at: string;
    };
}>();

const form = useForm({
    name: props.user.name,
    username: props.user.username,
    email: props.user.email,
});

function submit() {
    form.patch(`/users/${props.user.id}`);
}

function formatDate(value: string) {
    return new Date(value).toLocaleString('nl-NL');
}
</script>

<template>
    <Head :title="user.username" />

    <div class="flex max-w-2xl flex-col gap-8 p-4">
        <div>
            <Heading :title="user.username" description="Gegevens van de gebruiker" />

            <dl class="grid grid-cols-[auto_1fr] gap-x-6 gap-y-2 text-sm">
                <dt class="text-muted-foreground">ID</dt>
                <dd>{{ user.id }}</dd>

                <dt class="text-muted-foreground">Rol</dt>
                <dd>
                    <Badge :variant="user.role === 'admin' ? 'default' : 'secondary'">
                        {{ user.role }}
                    </Badge>
                </dd>

                <dt class="text-muted-foreground">Aangemaakt</dt>
                <dd>{{ formatDate(user.created_at) }}</dd>

                <dt class="text-muted-foreground">Laatst bijgewerkt</dt>
                <dd>{{ formatDate(user.updated_at) }}</dd>
            </dl>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <Heading variant="small" title="Bewerken" description="Wijzig naam, username en email" />

            <div class="grid gap-2">
                <Label for="name">Naam</Label>
                <Input id="name" v-model="form.name" required />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="username">Username</Label>
                <Input id="username" v-model="form.username" required />
                <InputError :message="form.errors.username" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input id="email" v-model="form.email" type="email" required />
                <InputError :message="form.errors.email" />
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing">Opslaan</Button>
                <p v-if="form.recentlySuccessful" class="text-sm text-muted-foreground">Opgeslagen.</p>
            </div>
        </form>
    </div>
</template>