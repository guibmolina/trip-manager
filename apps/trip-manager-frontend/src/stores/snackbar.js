import { defineStore } from 'pinia'

export const useSnackbarStore = defineStore('snackbar', {
  state: () => ({
    isVisible: false,
    message: '',
    color: '',
    timeout: 5000,
  }),
  actions: {
    showSnackbar(message, color = '', timeout = 5000) {
      this.message = message
      this.color = color
      this.timeout = timeout
      this.isVisible = true
    },
    hideSnackbar() {
      this.isVisible = false
      this.message = ''
    },
    clearSnackbar() {
      this.isVisible = false
      this.message = ''
      this.color = ''
      this.timeout = 5000
    },
  },
})
