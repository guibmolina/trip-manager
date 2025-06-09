<script setup>
import TopBar from '@/components/TopBar.vue'
import GenericSnackbar from '@/components/GenericSnackbar.vue'
</script>

<template>
  <div class="px-2 py-2">
    <TopBar />
    <div class="mt-4">
      <router-view />
      <GenericSnackbar />
    </div>
  </div>
</template>

<style scoped></style>
