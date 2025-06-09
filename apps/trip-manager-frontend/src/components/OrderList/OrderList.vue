<script setup>
import { ref, onMounted, computed } from 'vue'
import { useDestinationsStore } from '@/stores/destinations'
import { useOrdersStore } from '@/stores/orders'
import { useAuthStore } from '@/stores/auth'

import OrderFilterBar from './OrderFilterBar.vue'
import OrderTable from './OrderTable.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'
import OrderFormModal from '@/components/OrderFormModal.vue'

const destinationStore = useDestinationsStore()
const ordersStore = useOrdersStore()
const authStore = useAuthStore()

const headers = ref([
  { title: 'ID do Pedido', key: 'id', align: 'start', sortable: false },
  { title: 'Nome funcionário', key: 'user.name', sortable: false },
  { title: 'Destino', key: 'destination', sortable: false },
  { title: 'Status', key: 'status', sortable: false },
  { title: 'Data da viajem', key: 'departureDate', sortable: false },
  { title: 'Data do retorno', key: 'returnDate', sortable: false },
  { title: 'Ações', key: 'actions', sortable: false, align: 'center' },
])
const serverItems = computed(() => ordersStore.getFormattedOrders)
const loading = computed(() => ordersStore.getLoading)

const filterStatus = computed({
  get: () => ordersStore.filterStatus,
  set: (value) => ordersStore.setFilterStatus(value),
})
const filterDestination = computed({
  get: () => ordersStore.filterDestination,
  set: (value) => ordersStore.setFilterDestination(value),
})
const selectedDates = computed({
  get: () => ordersStore.selectedDates,
  set: (value) => ordersStore.setSelectedDates(value),
})
const dialog = ref(false)
const showConfirmModal = ref(false)
const modalTitle = ref('')
const modalMessage = ref('')
const modalConfirmText = ref('')
const modalConfirmColor = ref('')
const currentItemForAction = ref(null)
const currentActionType = ref('')

const showOrderFormModal = ref(false)
const orderToEditOrCreate = ref(null)

const createNewOrder = () => {
  orderToEditOrCreate.value = null
  showOrderFormModal.value = true
}

const editItem = async (item) => {
  const fullOrder = await ordersStore.fetchOrderById(item.id)
  orderToEditOrCreate.value = {
    ...fullOrder,
    departure_date: fullOrder.departure_date ? new Date(fullOrder.departure_date) : null,
    return_date: fullOrder.return_date ? new Date(fullOrder.return_date) : null,
    status: { label: ordersStore.getStatusLabel(fullOrder.status), key: fullOrder.status },
    destination: fullOrder.destination,
    user: fullOrder.user,
  }
  showOrderFormModal.value = true
}

const saveOrder = async (orderData) => {
  const payload = {
    ...(orderData.id && { id: orderData.id }),
    employee_name: orderData.user?.name || undefined,
    destination_id: orderData.destination?.id || undefined,
    status: orderData.status?.key || 'REQUESTED',
    departure_date: orderData.departure_date ? orderData.departure_date.toISOString() : null,
    return_date: orderData.return_date ? orderData.return_date.toISOString() : null,
  }

  if (orderData.id) {
    await ordersStore.updateOrder(orderData.id, payload)
  } else {
    await ordersStore.createOrder(payload)
  }
  showOrderFormModal.value = false
  orderToEditOrCreate.value = null
}

const triggerApproveConfirmation = (item) => {
  currentItemForAction.value = item
  currentActionType.value = 'approve'
  modalTitle.value = 'Aprovar Pedido'
  modalMessage.value = `Você tem certeza que deseja aprovar o pedido ${item.id} de ${item.user.name}?`
  modalConfirmText.value = 'Sim, Aprovar'
  modalConfirmColor.value = 'green'
  showConfirmModal.value = true
}

const triggerCancelConfirmation = (item) => {
  currentItemForAction.value = item
  currentActionType.value = 'cancel'
  modalTitle.value = 'Cancelar Pedido'
  modalMessage.value = `Você tem certeza que deseja cancelar o pedido ${item.id} de ${item.user.name}?`
  modalConfirmText.value = 'Sim, Cancelar'
  modalConfirmColor.value = 'green'
  showConfirmModal.value = true
}

const handleModalConfirm = async () => {
  if (!currentItemForAction.value) return

  const item = currentItemForAction.value

  try {
    ordersStore.loading = true
    if (currentActionType.value === 'approve') {
      await ordersStore.approveOrder(item.id)
    } else if (currentActionType.value === 'cancel') {
      await ordersStore.cancelOrder(item.id)
    }
  } catch (error) {
  } finally {
    showConfirmModal.value = false
    currentItemForAction.value = null
    currentActionType.value = ''
  }
}

const handleModalCancel = () => {
  showConfirmModal.value = false
  currentItemForAction.value = null
  currentActionType.value = ''
}

onMounted(() => {
  destinationStore.fetchDestinations()
  ordersStore.fetchOrders()
})
</script>

<template>
  <div class="bg-white shadow-lg rounded-2xl px-4 py-4 flex flex-col h-full">
    <div class="mb-4 flex flex-row justify-between items-center w-full">
      <div class="flex items-center">
        <span class="text-[#1D2B39] text-h5 font-weight-bold mb-4 md:mb-0 text-center">
          Lista de Pedidos
        </span>
        <v-btn icon size="small" variant="text" @click="dialog = true" class="ml-2 mb-4 md:mb-0">
          <v-icon>mdi-information-outline</v-icon>
        </v-btn>
      </div>

      <v-btn
        v-if="!authStore.isManager"
        color="#1D2B39"
        variant="flat"
        rounded="xl"
        @click="createNewOrder"
      >
        <div class="md:hidden flex">
          <v-icon icon="mdi-plus-circle-outline" size="large"></v-icon>
        </div>
        <span class="md:flex hidden">Novo pedido</span>
      </v-btn>
    </div>

    <OrderFilterBar
      v-model:filterStatus="filterStatus"
      v-model:filterDestination="filterDestination"
      v-model:selectedDates="selectedDates"
      @apply-filters="ordersStore.fetchOrders"
    />

    <OrderTable
      :headers="headers"
      :items="serverItems"
      :loading="loading"
      :isManager="authStore.isManager"
      @edit-item="editItem"
      @approve-item="triggerApproveConfirmation"
      @cancel-item="triggerCancelConfirmation"
      @update:options="ordersStore.fetchOrders"
    />

    <ConfirmationModal
      v-model="showConfirmModal"
      :title="modalTitle"
      :message="modalMessage"
      :confirm-text="modalConfirmText"
      :confirm-color="modalConfirmColor"
      @confirm="handleModalConfirm"
      @cancel="handleModalCancel"
    />

    <OrderFormModal
      v-model="showOrderFormModal"
      :order="orderToEditOrCreate"
      :isManager="authStore.isManager"
      @save="saveOrder"
      @cancel="showOrderFormModal = false"
    />

    <v-dialog v-model="dialog" max-width="500">
      <v-card>
        <v-card-title class="headline">Regras dos Botões de Ação</v-card-title>
        <v-card-text>
          <ul>
            <li>
              <v-icon size="small" class="mr-2">mdi-pencil</v-icon>
              O botão de <strong>Editar</strong> é visível para o
              <strong>usuário comum</strong> apenas quando o pedido está com o status
              <strong>"SOLICITADO"</strong>.
            </li>
            <li>
              <v-icon size="small" class="mr-2">mdi-check-circle-outline</v-icon>
              O botão de <strong>Aprovar</strong> é exibido para o
              <strong>gerente</strong> exclusivamente quando o pedido está no status
              <strong>"SOLICITADO"</strong>.
            </li>
            <li>
              <v-icon size="small" class="mr-2">mdi-cancel</v-icon>
              O botão de <strong>Cancelar</strong> pode ser utilizado pelo
              <strong>gerente</strong> se o pedido ainda não foi cancelado e atende a uma das
              seguintes condições:
              <ul>
                <li>O pedido foi <strong>APROVADO há menos de 24 horas</strong>.</li>
                <li>
                  A <strong>data de partida</strong> é igual ou superior a
                  <strong>7 dias</strong> após a data de aprovação.
                </li>
              </ul>
            </li>
          </ul>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="dialog = false">Entendi</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
