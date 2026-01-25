<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-7xl mx-auto">

        <div class="bg-white shadow-xl rounded-2xl p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Invoices</h2>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Invoice No</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Customer</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total</th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Paid Amount</th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Due Amount</th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Action</th>
                </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="inv in invoices" :key="inv.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4">{{ inv.invoice_no }}</td>
                  <td class="px-6 py-4">{{ inv.customer.name }}</td>
                  <td class="px-6 py-4">{{ new Date(inv.invoice_date).toLocaleDateString() }}</td>
                  <td class="px-6 py-4 text-right font-semibold">₹{{ inv.grand_total }}</td>
                  <td class="px-6 py-4 text-right text-green-600 font-medium">₹{{ inv.paid_amount }}</td>
                  <td class="px-6 py-4 text-right text-red-600 font-medium">₹{{ inv.due_amount }}</td>
                  <td class="px-6 py-4 text-center">
                    <button
                      @click="viewInvoice(inv.id)"
                      class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg shadow-sm transition"
                    >
                      View
                    </button>
                  </td>
                </tr>

                <tr v-if="invoices.length === 0">
                  <td colspan="7" class="text-center py-6 text-gray-400">No invoices found</td>
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
  name: 'InvoicesList',
  components: { Sidebar },
  data() {
    return { invoices: [] }
  },
  created() {
    this.loadInvoices()
  },
  methods: {
    async loadInvoices() {
      try {
        const res = await api.get('/invoices')
        this.invoices = res.data
      } catch(e) {
        console.log(e)
      }
    },
    viewInvoice(id) {
      this.$router.push(`/invoices/${id}`)
    },
    async logout() {
      try {
        await api.post('/logout')
        localStorage.removeItem('token')
        this.$router.push('/login')
      } catch(e) {
        console.log(e.response?.data?.message)
      }
    }
  }
}
</script>

<style scoped>
table th, table td {
  transition: all 0.2s;
}
</style>
