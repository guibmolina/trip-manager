import { defineStore } from 'pinia'
import api from '@/axios'

export const useDestinationsStore = defineStore('destinations', {
  state: () => ({
    destinations: [],
    loading: false,
    error: null,
  }),
  getters: {
    getDestinations: (state) => state.destinations,
    isLoading: (state) => state.loading,
    hasError: (state) => state.error !== null,
  },
  actions: {
    async fetchDestinations() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/api/destinations')
        this.destinations = response.data
      } catch (error) {
        this.error = error
      } finally {
        this.loading = false
      }
    },
  },
})
