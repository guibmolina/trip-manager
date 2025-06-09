<script setup>
import { ref, watch, computed } from 'vue'
import VueDatePicker from '@vuepic/vue-datepicker'
import 'vue-datepicker-ui/lib/vuedatepickerui.css'
import { useDestinationsStore } from '@/stores/destinations'

const props = defineProps({
  modelValue: Boolean,
  isManager: Boolean,
  order: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['update:modelValue', 'save', 'cancel'])

const dialog = ref(props.modelValue)
const editedOrder = ref(initializeOrder(props.order))
const error = ref('')

const destinationStore = useDestinationsStore()
const destinationsFromStore = computed(() =>
  destinationStore.getDestinations.map((d) => ({
    title: `${d.city}, ${d.country} (${d.iata_code})`,
    ...d,
  })),
)

const isNewOrder = computed(() => !props.order || !props.order.id)

function initializeOrder(orderData) {
  if (orderData && orderData.id) {
    return {
      ...orderData,
      user: { ...orderData.user },
      status: orderData.status
        ? { ...orderData.status }
        : { label: 'Solicitado', key: 'REQUESTED' },
      departure_date: orderData.departure_date ? new Date(orderData.departure_date) : null,
      return_date: orderData.return_date ? new Date(orderData.return_date) : null,
    }
  } else {
    return {
      id: null,
      user: {},
      destination_id: null,
      status: { label: 'Solicitado', key: 'REQUESTED' },
      departure_date: null,
      return_date: null,
    }
  }
}

watch(
  () => props.modelValue,
  (newValue) => {
    dialog.value = newValue
    if (newValue) {
      editedOrder.value = initializeOrder(props.order)
    }
  },
)

watch(dialog, (newValue) => {
  emit('update:modelValue', newValue)
})

const save = () => {
  emit('save', editedOrder.value)
  dialog.value = false
}

const cancel = () => {
  emit('cancel')
  dialog.value = false
}
</script>

<template>
  <v-dialog v-model="dialog" max-width="600px">
    <div class="rounded-lg shadow-lg bg-white px-4 py-4">
      <div class="text-[#1D2B39] font-semibold">
        <div class="flex justify-between">
          {{ isNewOrder ? 'Novo Pedido' : 'Editar Pedido' }}
          <v-btn variant="text" @click="cancel"
            ><v-icon icon="mdi-close-circle" size="large"></v-icon
          ></v-btn>
        </div>
      </div>

      <div class="py-4 px-6">
        <v-text-field
          v-if="!isNewOrder"
          readonly
          v-model="editedOrder.user.name"
          label="Nome do Funcionário"
          variant="outlined"
        />

        <v-select
          v-model="editedOrder.destination"
          :items="destinationsFromStore"
          label="Destino"
          variant="outlined"
          item-title="title"
          item-value="id"
          return-object
          :readonly="isManager"
          :rules="[(v) => !!v || 'Destino é obrigatório']"
        >
          <template v-slot:item="{ props: itemProps, item }">
            <v-list-item
              v-bind="itemProps"
              :title="`${item.raw.city}, ${item.raw.country}`"
              :subtitle="`${item.raw.iata_code}`"
            ></v-list-item>
          </template>
          <template v-slot:selection="{ item }">
            <span v-if="item.raw">
              {{ item.raw.city }}, {{ item.raw.country }} ({{ item.raw.iata_code }})
            </span>
          </template>
        </v-select>

        <div class="mb-4">
          <VueDatePicker
            v-model="editedOrder.departure_date"
            :enable-time-picker="false"
            locale="pt-BR"
            select-text="Selecionar"
            cancel-text="Cancelar"
            placeholder="Data de Ida"
            format="dd/MM/yyyy"
            :readonly="isManager"
            :rules="[(v) => !!v || 'Data de Ida é obrigatória']"
          ></VueDatePicker>
        </div>

        <div>
          <VueDatePicker
            v-model="editedOrder.return_date"
            :enable-time-picker="false"
            locale="pt-BR"
            select-text="Selecionar"
            cancel-text="Cancelar"
            placeholder="Data de Volta"
            format="dd/MM/yyyy"
            :readonly="isManager"
            :min-date="editedOrder.departure_date"
          ></VueDatePicker>
        </div>
        <p v-if="error" class="text-red-400 mt-1 flex justify-center">
          {{ error }}
        </p>
      </div>

      <div class="flex justify-end py-4">
        <v-btn variant="text" @click="cancel">Cancelar</v-btn>
        <v-btn
          color="primary"
          @click="save"
          :disabled="
            !editedOrder.return_date || !editedOrder.departure_date || !editedOrder.destination
          "
          >Salvar</v-btn
        >
      </div>
    </div>
  </v-dialog>
</template>
