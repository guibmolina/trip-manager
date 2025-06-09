<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Confirmação',
  },
  message: {
    type: String,
    default: 'Você tem certeza que deseja realizar esta ação?',
  },
  confirmText: {
    type: String,
    default: 'Confirmar',
  },
  confirmColor: {
    type: String,
    default: 'primary',
  },
})

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel'])

const dialog = ref(props.modelValue)
watch(
  () => props.modelValue,
  (newValue) => {
    dialog.value = newValue
  },
)

watch(dialog, (newValue) => {
  emit('update:modelValue', newValue)
})

const confirm = () => {
  emit('confirm')
  dialog.value = false
}

const cancel = () => {
  emit('cancel')
  dialog.value = false
}
</script>
<template>
  <v-dialog v-model="dialog" max-width="500px">
    <v-card class="rounded-lg shadow-lg">
      <v-card-title class="text-h6 text-center py-4">
        {{ title }}
      </v-card-title>
      <v-card-text class="py-6 px-6 text-center text-lg text-gray-800">
        {{ message }}
      </v-card-text>
      <v-card-actions class="justify-center gap-4 py-4">
        <v-btn color="red-darken-1" variant="flat" rounded="lg" min-width="100" @click="cancel">
          Cancelar
        </v-btn>
        <v-btn :color="confirmColor" variant="flat" rounded="lg" min-width="100" @click="confirm">
          {{ confirmText }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<style scoped></style>
