<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-7xl mx-auto">

        <div class="bg-white rounded-2xl shadow-xl p-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800">Outstanding Report</h2>

            <select
              v-model="type"
              @change="loadData"
              class="w-60 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer"
            >
              <option value="customers">Customer Outstanding</option>
              <option value="vendors">Vendor Outstanding</option>
            </select>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total</th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Paid</th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Returned</th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Outstanding</th>
                </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="row in data"
                  :key="row.id"
                  class="hover:bg-gray-50 transition"
                >
                  <td class="px-6 py-4 font-medium text-gray-800">
                    {{ row.name }}
                  </td>
                  <td class="px-6 py-4 text-right font-semibold">
                    ₹{{ row.total }}
                  </td>
                  <td class="px-6 py-4 text-right text-green-600 font-medium">
                    ₹{{ row.paid }}
                  </td>
                  <td class="px-6 py-4 text-right text-orange-500 font-medium">
                    ₹{{ row.returned }}
                  </td>
                  <td
                    class="px-6 py-4 text-right font-bold"
                    :class="row.outstanding > 0 ? 'text-red-600' : 'text-green-600'"
                  >
                    ₹{{ row.outstanding }}
                  </td>
                </tr>

                <tr v-if="data.length === 0">
                  <td colspan="5" class="text-center py-8 text-gray-400">
                    No data found
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import api from '../axios'

export default {
  components: { Sidebar },
  data() {
    return {
      type: 'customers',
      data: []
    }
  },
  created() {
    this.loadData()
  },
  methods: {
    async loadData() {
      const res = await api.get(`/outstanding/${this.type}`)
      this.data = res.data.data.map(row => ({
        id: row.id,
        name: row.name,
        total: this.type === 'customers'
          ? row.total_invoice
          : row.total_purchase,
        paid: row.total_paid,
        returned: row.total_return,
        outstanding: row.outstanding
      }))
    },
    async logout() {
      await api.post('/logout')
      localStorage.removeItem('token')
      this.$router.push('/login')
    }
  }
}
</script>
