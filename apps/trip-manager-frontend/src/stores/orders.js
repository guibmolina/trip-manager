import { defineStore } from 'pinia'
import api from '@/axios'
import { useSnackbarStore } from './snackbar'

export const useOrdersStore = defineStore('orders', {
  state: () => ({
    orders: [],
    loading: false,
    filterStatus: null,
    filterDestination: null,
    selectedDates: [],
    statusMapping: [
      { label: 'Solicitado', value: 'REQUESTED' },
      { label: 'Aprovado', value: 'APPROVED' },
      { label: 'Cancelado', value: 'CANCELED' },
    ],
  }),

  getters: {
    getFormattedOrders: (state) => {
      return state.orders.map((order) => ({
        ...order,
        status: { label: state.getStatusLabel(order.status), key: order.status },
        departureDate: order.departure_date
          ? new Date(order.departure_date).toLocaleDateString('pt-BR')
          : 'N/A',
        returnDate: order.return_date
          ? new Date(order.return_date).toLocaleDateString('pt-BR')
          : 'N/A',
        destination: order.destination
          ? `${order.destination.city}, ${order.destination.country}`
          : 'N/A',
      }))
    },
    getStatusLabel: (state) => (statusCode) => {
      const status = state.statusMapping.find((s) => s.value === statusCode)
      return status ? status.label : statusCode
    },
    getFilterStatus: (state) => state.filterStatus,
    getFilterDestination: (state) => state.filterDestination,
    getSelectedDates: (state) => state.selectedDates,
    getLoading: (state) => state.loading,
    getRawOrders: (state) => state.orders,
  },

  actions: {
    async fetchOrders() {
      const snackbarStore = useSnackbarStore()
      this.loading = true
      try {
        const filters = {
          status: this.filterStatus || undefined,
          destination_id: this.filterDestination ? this.filterDestination.id : undefined,
          start_date:
            this.selectedDates !== null && this.selectedDates[0]
              ? this.selectedDates[0].toISOString()
              : undefined,
          end_date:
            this.selectedDates !== null && this.selectedDates[1]
              ? this.selectedDates[1].toISOString()
              : undefined,
        }
        const response = await api.get('/api/orders', { params: filters })
        this.orders = response.data
      } catch (error) {
        snackbarStore.showSnackbar('Erro ao buscar pedidos', 'error', 3000)
      } finally {
        this.loading = false
      }
    },

    setFilterStatus(status) {
      this.filterStatus = status
      this.fetchOrders()
    },
    setFilterDestination(destination) {
      this.filterDestination = destination
      this.fetchOrders()
    },
    setSelectedDates(dates) {
      this.selectedDates = dates
      this.fetchOrders()
    },

    async approveOrder(orderId) {
      const snackbarStore = useSnackbarStore()
      try {
        this.loading = true
        await api.patch(`/api/orders/${orderId}/`, { status: 'APPROVED' })
        await this.fetchOrders()
      } catch (error) {
        snackbarStore.showSnackbar('Erro ao aprovar o pedido', 'error', 3000)
        throw error
      }
    },

    async cancelOrder(orderId) {
      const snackbarStore = useSnackbarStore()
      try {
        this.loading = true
        await api.patch(`/api/orders/${orderId}/`, { status: 'CANCELED' })
        await this.fetchOrders()
      } catch (error) {
        snackbarStore.showSnackbar('Erro ao cancelar o pedido', 'error', 3000)
        throw error
      }
    },

    async updateOrder(orderId, payload) {
      const snackbarStore = useSnackbarStore()
      try {
        this.loading = true
        await api.put(`/api/orders/${orderId}`, payload)
        await this.fetchOrders()
      } catch (error) {
        snackbarStore.showSnackbar('Erro ao atualizar o pedido', 'error', 3000)
        throw error
      }
    },

    async fetchOrderById(orderId) {
      const snackbarStore = useSnackbarStore()
      try {
        const response = await api.get(`/api/orders/${orderId}`)
        return response.data
      } catch (error) {
        snackbarStore.showSnackbar('Erro ao buscar o pedido', 'error', 3000)
        throw error
      }
    },

    async createOrder(orderData) {
      const snackbarStore = useSnackbarStore()

      try {
        const response = await api.post('/api/orders', orderData)
        await this.fetchOrders()
      } catch (error) {
        snackbarStore.showSnackbar('Erro ao criar o pedido', 'error', 3000)
        throw error
      }
    },
  },
})
