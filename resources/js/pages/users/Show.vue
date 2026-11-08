<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{   
    user: { id: number; username: string; email: string, name: string, created_at: string, role: string, updated_at: string};
}>();

const form = useForm({                                        
    name: props.user.name,
    username: props.user.username,
    email: props.user.email,
});

function submit() {                                           
    form.patch(`/users/${props.user.id}`);
}

</script>

<template>
    <div>
        <h1>{{ user.username }}</h1>
        <p>Name: {{ user.name }}</p>
        <p>Aangemaakt: {{ user.created_at }}</p>
        <p>Email: {{ user.email }}</p>
        <p>ID: {{ user.id}}</p>
        <p>Role: {{ user.role }}</p>
        <p>Last update: {{ user.updated_at }}</p>
    </div>
    <form @submit.prevent="submit">                           
        <input v-model="form.name" />                       
        <p v-if="form.errors.name">{{ form.errors.name }}</p>  

        <input v-model="form.username" />
        <p v-if="form.errors.username">{{ form.errors.username }}</p>

        <input v-model="form.email" />
        <p v-if="form.errors.email">{{ form.errors.email }}</p>
        <pre>{{ form.errors }}</pre>
        <button type="submit" :disabled="form.processing">Opslaan</button>  
    </form>
</template>