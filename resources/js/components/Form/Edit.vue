<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  item: {
    type: Object,
    default: () => ({ id: null, name: '', ext: '', city: '', startDate: '', completion: 0 })
  }
})

const emit = defineEmits(['save', 'cancel'])

const name = ref(props.item.name)
const ext = ref(props.item.ext)
const city = ref(props.item.city)
const startDate = ref(props.item.startDate)
const completion = ref(props.item.completion)

// Sync props changes (e.g., if item updates externally)
watch(() => props.item, (newItem) => {
  name.value = newItem.name
  ext.value = newItem.ext
  city.value = newItem.city
  startDate.value = newItem.startDate
  completion.value = newItem.completion
}, { immediate: true })

const save = () => {
  if (!name.value.trim()) {
    alert('Name is required!') // Simple validation; use a library like VeeValidate for production
    return
  }
  emit('save', {
    id: props.item.id,
    name: name.value,
    ext: ext.value,
    city: city.value,
    startDate: startDate.value,
    completion: parseInt(completion.value) || 0
  })
}

const cancel = () => {
  emit('cancel')
}
</script>

<template>
  <div class="card">
    <div class="card-header">
      <h5>{{ item.id ? 'Edit Item' : 'Create New Item' }}</h5>
    </div>
    <div class="card-body">
      <form>
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input
            id="name"
            type="text"
            class="form-control"
            v-model="name"
            placeholder="Enter name"
            required
          >
        </div>
        <div class="mb-3">
          <label for="ext" class="form-label">Ext.</label>
          <input
            id="ext"
            type="text"
            class="form-control"
            v-model="ext"
            placeholder="Enter extension"
          >
        </div>
        <div class="mb-3">
          <label for="city" class="form-label">City</label>
          <input
            id="city"
            type="text"
            class="form-control"
            v-model="city"
            placeholder="Enter city"
          >
        </div>
        <div class="mb-3">
          <label for="startDate" class="form-label">Start Date</label>
          <input
            id="startDate"
            type="date"
            class="form-control"
            v-model="startDate"
          >
        </div>
        <div class="mb-3">
          <label for="completion" class="form-label">Completion (%)</label>
          <input
            id="completion"
            type="number"
            min="0"
            max="100"
            class="form-control"
            v-model.number="completion"
          >
        </div>
        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-secondary" @click="cancel">Cancel</button>
          <button type="button" class="btn btn-primary" @click="save">Save</button>
        </div>
      </form>
    </div>
  </div>
</template>