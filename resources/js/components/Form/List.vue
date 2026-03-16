<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  initialItems: {
    type: Array,
    default: () => [],
  },
  createUrl: {
    type: String,
    default: '',
  },
  editBaseUrl: {
    type: String,
    default: '',
  },
  labels: {
    type: Object,
    default: () => ({}),
  },
})

const defaultLabels = {
  create: 'Create New',
  search: 'Search',
  empty: 'No forms found.',
  edit: 'Edit',
  name: 'Name',
  ext: 'Ext.',
  city: 'City',
  startDate: 'Start Date',
  completion: 'Completion',
}

const resolvedLabels = computed(() => ({
  ...defaultLabels,
  ...props.labels,
}))

const search = ref('')

const filteredItems = computed(() => {
  const keyword = search.value.trim().toLowerCase()

  if (!keyword) {
    return props.initialItems
  }

  return props.initialItems.filter((item) =>
    String(item.name ?? '').toLowerCase().includes(keyword)
  )
})

const editUrl = (uuid) => {
  const baseUrl = props.editBaseUrl.replace(/\/$/, '')
  return `${baseUrl}/${encodeURIComponent(uuid)}`
}

const createNew = async () => {
  if (!props.createUrl || !window.axios) {
    return
  }

  if (typeof window.showLoader === 'function') {
    window.showLoader(true)
  }

  try {
    const response = await window.axios.get(props.createUrl)
    const formId = response?.data?.form?.uuid

    if (formId) {
      window.location.href = editUrl(formId)
    }
  } catch (error) {
    if (typeof window.handleErrorMessage === 'function' && error?.response) {
      window.handleErrorMessage(error.response)
    } else {
      console.error(error)
    }
  } finally {
    if (typeof window.showLoader === 'function') {
      window.showLoader(false)
    }
  }
}
</script>

<template>
  <div class="row">
    <div class="col-sm-10 mx-auto">
      <div class="row">
        <div class="col-lg-3 mb-3">
          <button class="btn btn-primary" type="button" @click="createNew">
            <i class="fas fa-plus"></i>
            {{ resolvedLabels.create }}
          </button>
        </div>
        <div class="col-lg-9 float-end">
          <div class="input-group mb-3">
            <input
              v-model="search"
              type="text"
              class="form-control"
              :placeholder="resolvedLabels.search"
              :aria-label="resolvedLabels.search"
            >
            <button class="btn btn-primary" type="button" disabled>
              <i class="fas fa-search"></i> {{ resolvedLabels.search }}
            </button>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table mb-0">
              <thead class="table-light">
                <tr>
                  <th>{{ resolvedLabels.name }}</th>
                  <th>{{ resolvedLabels.ext }}</th>
                  <th>{{ resolvedLabels.city }}</th>
                  <th data-type="date" data-format="YYYY/DD/MM">{{ resolvedLabels.startDate }}</th>
                  <th>{{ resolvedLabels.completion }}</th>
                  <th>{{ resolvedLabels.edit }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in filteredItems" :key="item.uuid">
                  <td>{{ item.name }}</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>
                    <a :href="editUrl(item.uuid)">
                      <span>{{ resolvedLabels.edit }}</span>
                    </a>
                  </td>
                </tr>
                <tr v-if="filteredItems.length === 0">
                  <td colspan="6" class="text-center">{{ resolvedLabels.empty }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
