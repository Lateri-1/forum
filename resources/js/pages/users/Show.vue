<script setup lang="ts">

defineProps<{   
    user: { id: number; username: string; email: string, name: string, created_at: Date, role: string, updated_at: Date};
}>();
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
</template>