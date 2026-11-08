<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';

defineProps<{
    users: { id: number; username: string; email: string }[];
}>();

function remove(id: number) {
    if (!confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')) {
        return;
    }

    router.delete(`/users/${id}`);
}
</script>

<template>
    <div>
        <h1>Users</h1>
        <table>
            <thead>
                <tr>
                    <th>usernames</th>
                    <th>emails</th>
                    <th>id</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td>{{ user.username }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.id }}</td>
                    <td><Link :href="`/users/${user.id}`">{{ user.username }}</Link></td>
                    <td>
                        <button type="button" @click="remove(user.id)">Verwijderen</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>