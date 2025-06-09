import { defineStore } from 'pinia'
import api from '@/axios'
import router from '../router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: null,
    loading: false,
    error: null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    isLoading: (state) => state.loading,
    authError: (state) => state.error,
    loggedInUser: (state) => state.user,
    isManager: (state) => state.user.type === 'MANAGER',
  },
  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('api/login', credentials)
        this.token = response.data.token
        localStorage.setItem('token', this.token)
        await this.fetchUser()
        router.push('/')
        return true
      } catch (err) {
        if (err.response?.data?.message === 'invalid_credentials') {
          this.error = 'E-mail ou senha inválidos'
        } else {
          this.error = 'Erro ao fazer login. Tente novamente.'
        }
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        return false
      } finally {
        this.loading = false
      }
    },

    async fetchUser() {
      try {
        const response = await api.get('api/auth/user')
        this.user = response.data
      } catch (err) {
        this.logout()
      }
    },

    async logout() {
      this.token = null
      localStorage.removeItem('token')
      await router.push('/login')
      this.user = null
    },
  },
})
