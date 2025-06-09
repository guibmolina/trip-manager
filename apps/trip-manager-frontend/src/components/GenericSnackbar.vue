<template>
  <v-snackbar
    v-model="snackbarStore.isVisible"
    :color="snackbarStore.color"
    :timeout="snackbarStore.timeout"
    location="bottom right"
    variant="flat"
    @update:model-value="handleSnackbarClose"
  >
    {{ snackbarStore.message }}

    <template v-slot:actions>
      <v-btn color="white" variant="text" @click="snackbarStore.hideSnackbar"> Fechar </v-btn>
    </template>
  </v-snackbar>
</template>

<script setup>
import { useSnackbarStore } from '@/stores/snackbar'

const snackbarStore = useSnackbarStore()
const handleSnackbarClose = (value) => {
  if (!value) {
    snackbarStore.clearSnackbar()
  }
}
</script>

<style scoped></style>
