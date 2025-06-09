<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  headers: Array,

  items: Array,

  loading: Boolean,

  isManager: Boolean,
})

const emit = defineEmits(['edit-item', 'approve-item', 'cancel-item', 'update:options'])

const getStatusColor = (statusCode) => {
  switch (statusCode) {
    case 'REQUESTED':
      return 'orange'

    case 'APPROVED':
      return 'green'

    case 'CANCELED':
      return 'red'

    default:
      return 'grey'
  }
}

function canCancel(order) {
  if (!props.isManager) {
    return false
  }

  if (order.status.key === 'CANCELED') {
    return false
  }

  if (order.status.key === 'APPROVED') {
    const approvedAt = new Date(order.approved_at)

    const now = new Date()

    if (approvedAt && approvedAt.getTime() + 24 * 60 * 60 * 1000 <= now.getTime()) {
      return false
    }

    const departureDate = new Date(order.departure_date)

    const diffDays = Math.abs((departureDate - approvedAt) / (1000 * 60 * 60 * 24))

    if (diffDays < 7) {
      return false
    }
  }

  return true
}
</script>

<template>
  <v-data-table-server
    :headers="headers"
    :items="items"
    :items-length="items.length"
    :loading="loading"
    item-value="id"
    class="elevation-1 flex-grow"
    @update:options="emit('update:options', $event)"
    hide-default-footer
    no-data-text="Nenhum pedido encontrado."
  >
    <template v-slot:item.status="{ item }">
      <v-chip :color="getStatusColor(item.status.key)" label small>
        {{ item.status.label }}
      </v-chip>
    </template>

    <template v-slot:item.actions="{ item }">
      <v-icon
        v-if="!isManager && item.status.key === 'REQUESTED'"
        size="small"
        class="me-2"
        @click="emit('edit-item', item)"
      >
        mdi-pencil
      </v-icon>

      <v-icon
        v-if="item.status.key === 'REQUESTED' && isManager"
        size="small"
        class="me-2"
        icon="mdi-check-circle-outline"
        @click="emit('approve-item', item)"
      >
      </v-icon>

      <v-icon v-if="canCancel(item)" size="small" class="me-2" @click="emit('cancel-item', item)">
        mdi-cancel
      </v-icon>
    </template>
  </v-data-table-server>
</template>
