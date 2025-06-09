<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
const authStore = useAuthStore()

const email = ref('')
const password = ref('')

const emailRule = [
  (value) => {
    if (!value) {
      return 'O campo e-mail é obrigatório'
    }

    if (/^[a-z.-]+@[a-z.-]+\.[a-z]+$/i.test(value)) return true

    return 'O e-mail deve ser válido'
  },
]

const passwordRule = [
  (value) => {
    if (value) return true
    return 'O campo senha é obrigatório'
  },
]

const submitLogin = async () => {
  await authStore.login({ email: email.value, password: password.value })
}
</script>

<template>
  <div class="w-full px-4 py-4">
    <v-card class="w-full px-6 py-6 rounded-lg" elevation="10">
      <span class="md:text-2xl text-lg font-bold mb-8 text-[#2b2b2b]">Entrar na conta</span>

      <v-form @submit.prevent="submitLogin" ref="form">
        <v-text-field
          variant="outlined"
          :rules="emailRule"
          v-model="email"
          label="E-mail"
          type="email"
          required
          class="mb-4"
          rounded=""
        />
        <v-text-field
          variant="outlined"
          :rules="passwordRule"
          v-model="password"
          label="Senha"
          type="password"
          required
          class="mb-6"
          rounded=""
        />
        <v-btn
          color="#707156"
          class="text-white w-full"
          large
          type="submit"
          :loading="authStore.loading"
        >
          Entrar
        </v-btn>
        <p v-if="authStore.authError" class="text-red-400 mt-1 flex justify-center">
          {{ authStore.authError }}
        </p>
      </v-form>
    </v-card>
  </div>
</template>
