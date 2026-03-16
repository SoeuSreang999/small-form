<script setup>
import { ref, computed } from 'vue'
import Edit from './Edit.vue'

const search = ref('')
const isEditing = ref(false)
const editedItem = ref(null)

const items = ref([
  { id: 1, name: '9958', ext: '9958', city: 'Curicó', startDate: '2005/02/11', completion: 37 },
  { id: 2, name: 'John Doe', ext: '1234', city: 'Santiago', startDate: '2023/05/15', completion: 75 },
  { id: 3, name: 'Jane Smith', ext: '5678', city: 'Valparaíso', startDate: '2024/01/20', completion: 20 },
  // Add more items as needed; in a real app, fetch from API
])

const filteredItems = computed(() => {
  if (!search.value) return items.value
  return items.value.filter(item =>
    item.name.toLowerCase().includes(search.value.toLowerCase()) ||
    item.city.toLowerCase().includes(search.value.toLowerCase())
  )
})

const createNew = () => {
  editedItem.value = { id: Date.now(), name: '', ext: '', city: '', startDate: '', completion: 0 }
  isEditing.value = true
}

const editItem = (item) => {
  editedItem.value = { ...item }
  isEditing.value = true
}

const handleSave = (updatedItem) => {
  if (updatedItem.id && !items.value.some(i => i.id === updatedItem.id)) {
    // New item
    items.value.push(updatedItem)
  } else {
    // Update existing
    const index = items.value.findIndex(i => i.id === updatedItem.id)
    if (index > -1) {
      items.value[index] = updatedItem
    }
  }
  isEditing.value = false
  editedItem.value = null
}

const handleCancel = () => {
  isEditing.value = false
  editedItem.value = null
}
</script>

<template>
  <div class="row">
    <div class="col-sm-10 mx-auto">
      <!-- Buttons and Search Row -->
      <div class="row">
        <div class="col-lg-3 mb-3">
          <button class="btn btn-primary" type="button" @click="createNew">
            <i class="fas fa-plus"></i>
            Create New
          </button>
        </div>
        <div class="col-lg-9 float-end">
          <div class="input-group mb-3">
            <input
              type="text"
              class="form-control"
              v-model="search"
              placeholder="Search"
              aria-label="Search"
            >
            <button class="btn btn-primary" type="button">
              <i class="fas fa-search"></i> Search
            </button>
          </div>
        </div>
      </div>

      <!-- List or Edit View -->
      <div class="row" v-if="!isEditing">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table mb-0">
              <thead class="table-light">
                <tr>
                  <th>Name</th>
                  <th>Ext.</th>
                  <th>City</th>
                  <th data-type="date" data-format="YYYY/DD/MM">Start Date</th>
                  <th>Completion</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in filteredItems" :key="item.id">
                  <td>{{ item.name }}</td>
                  <td>{{ item.ext }}</td>
                  <td>{{ item.city }}</td>
                  <td>{{ item.startDate }}</td>
                  <td>{{ item.completion }}%</td>
                  <td>
                    <button class="btn btn-sm btn-warning" @click="editItem(item)">
                      Edit
                    </button>
                  </td>
                </tr>
                <tr v-if="filteredItems.length === 0">
                  <td colspan="6" class="text-center">No items found.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="row" v-else>
        <div class="col-lg-12">
          <Edit
            :item="editedItem"
            @save="handleSave"
            @cancel="handleCancel"
          />
        </div>
      </div>
    </div>
  </div>
</template>