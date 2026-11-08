<script setup lang="ts">


import { Link } from '@inertiajs/vue3';



defineProps<{   
    users: { id: number; username: string; email: string }[];
}>();
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
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td>{{ user.username }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.id }}</td>
                    <td><Link :href="`/users/${user.id}`">{{ user.username }}</Link></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>