<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-6xl mx-auto">

        <div class="bg-white rounded-2xl shadow-lg p-6 overflow-x-auto">
          <h2 class="text-2xl font-bold text-gray-800 mb-6">Vendor List</h2>
  
          <router-link
            to="/vendor-page"
            class="btn btn-primary text-white px-4 py-2 rounded-lg mb-4 inline-block"
          >
            + Add Vendor
          </router-link>

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Balance</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="v in vendors" :key="v.id" class="hover:bg-gray-50">
                <td class="px-6 py-4">{{ v.name }}</td>
                <td class="px-6 py-4">{{ v.phone || '-' }}</td>
                <td class="px-6 py-4">{{ v.opening_balance }}</td>
                <td class="px-6 py-4 text-center flex justify-center gap-2">
                  <router-link
                    :to="{ name: 'vendor-page', params: { id: v.id } }"
                    class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg shadow-sm transition"
                  >
                    Edit
                  </router-link>
                  <button
                    @click="remove(v.id)"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg shadow-sm transition"
                  >
                    Delete
                  </button>
                </td>
              </tr>

              <tr v-if="vendors.length === 0">
                <td colspan="4" class="text-center py-6 text-gray-400">No vendors found</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import api from '../axios'

export default {
  name: 'VendorList',
  components: { Sidebar },
  data() {
    return {
      vendors: []
    }
  },
  created() {
    this.getVendors()
  },
  methods: {
    async getVendors() {
      try {
        const res = await api.get('/vendors')
        this.vendors = res.data.data
      } catch(e) {
        console.log(e)
      }
    },
    async remove(id) {
      if(!confirm('Delete Vendor?')) return
      try {
        await api.delete(`/vendors/${id}`)
        this.getVendors()
      } catch(e) {
        console.log(e)
      }
    },
    async logout() {
      try {
        await api.post('/logout')
        localStorage.removeItem('token')
        this.$router.push('/login')
      } catch(e) {
        console.log(e)
      }
    }
  }
}
</script>
