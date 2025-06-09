<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const userInitials = computed(() => {
  const user = authStore.loggedInUser
  if (user && user.name) {
    const parts = user.name.split(' ')
    if (parts.length > 1) {
      return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
    }
    return parts[0][0].toUpperCase()
  }
  return 'NF'
})

const loggedInUser = computed(() => authStore.loggedInUser)

const logoutUser = () => {
  authStore.logout()
}
</script>
<template>
  <v-menu offset-y>
    <template v-slot:activator="{ props }">
      <v-btn icon v-bind="props">
        <v-avatar color="#FACE0C">
          <span class="">{{ userInitials }}</span>
        </v-avatar>
      </v-btn>
    </template>

    <v-list>
      <v-list-item>
        <v-list-item-title>{{ loggedInUser.name }}</v-list-item-title>
      </v-list-item>
      <v-divider></v-divider>
      <v-list-item @click="logoutUser">
        <v-list-item-title>Sair</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<style scoped></style>
