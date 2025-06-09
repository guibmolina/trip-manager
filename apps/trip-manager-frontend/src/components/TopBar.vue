<script setup>
import UserMenu from '@/components/UserMenu.vue'
</script>
<template>
  <div>
    <v-toolbar color="#1D2B39" dark class="px-4 !rounded-2xl">
      <span class="text-2xl font-bold">Trip Manager</span>
      <v-spacer></v-spacer>
      <UserMenu />
    </v-toolbar>
  </div>
</template>
<style scoped></style>
