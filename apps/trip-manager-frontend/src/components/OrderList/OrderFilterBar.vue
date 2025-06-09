<script setup>
import { computed } from 'vue'
import { useDestinationsStore } from '@/stores/destinations'
import VueDatePicker from '@vuepic/vue-datepicker'

const props = defineProps({
  filterStatus: String,
  filterDestination: Object,
  selectedDates: Array,
})

const emit = defineEmits([
  'update:filterStatus',
  'update:filterDestination',
  'update:selectedDates',
  'apply-filters',
])

const destinationStore = useDestinationsStore()
const destinations = computed(() => destinationStore.getDestinations)

const statusOptions = [
  { label: 'Solicitado', value: 'REQUESTED' },
  { label: 'Aprovado', value: 'APPROVED' },
  { label: 'Cancelado', value: 'CANCELED' },
]

const updateStatus = (value) => {
  emit('update:filterStatus', value)
  emit('apply-filters')
}

const updateDestination = (value) => {
  emit('update:filterDestination', value)
  emit('apply-filters')
}

const updateDates = (dates) => {
  emit('update:selectedDates', dates)
  emit('apply-filters')
}
</script>

<template>
  <div
    class="flex flex-col md:flex-row md:items-center md:flex-wrap gap-4 w-full md:pr-5 mb-4 justify-center items-center"
  >
    <div class="w-full text-left md:w-auto md:text-center">
      <span class="text-base font-semibold text-gray-700">Filtrar por:</span>
    </div>
    <v-select
      :model-value="filterStatus"
      @update:model-value="updateStatus"
      :items="statusOptions"
      label="Status"
      clearable
      hide-details
      density="compact"
      class="w-full md:w-1/4"
      item-title="label"
      item-value="value"
      variant="outlined"
    ></v-select>

    <v-select
      :model-value="filterDestination"
      @update:model-value="updateDestination"
      :items="destinations"
      label="Destino"
      variant="outlined"
      clearable
      hide-details
      density="compact"
      class="w-full md:w-1/4"
      item-value="id"
      return-object
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

    <div class="w-full md:w-1/4">
      <VueDatePicker
        :model-value="selectedDates"
        @update:model-value="updateDates"
        range
        :enable-time-picker="false"
        locale="pt-BR"
        select-text="Selecionar"
        cancel-text="Cancelar"
        placeholder="Selecione um período"
        format="dd/MM/yyyy"
      ></VueDatePicker>
    </div>
  </div>
</template>
