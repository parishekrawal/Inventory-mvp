<template>
  <div class="flex min-h-screen bg-gray-100">
    <Sidebar @logout="logout"/>

    <div class="flex-1 p-6 ml-65">
      <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-2xl p-6">

        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-semibold text-gray-800">Purchase Bills</h2>
          <button class="btn btn-primary  cursor-pointer" @click="$router.push('/purchase-bill-create')">Create New Bill</button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm text-gray-700 border border-gray-200 rounded-lg">
            <thead class="bg-gray-50 text-gray-600">
              <tr>
                <th class="p-3 border-b">Bill No</th>
                <th class="p-3 border-b">Vendor</th>
                <th class="p-3 border-b">Date</th>
                <th class="p-3 border-b text-right">Total</th>
                <th class="p-3 border-b text-right">Paid Amount</th>
                <th class="p-3 border-b text-right">Due Amount</th>
                <th class="p-3 border-b text-center">Action</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="b in bills" :key="b.id" class="hover:bg-gray-50">
                <td class="p-3 border-b">{{ b.bill_no }}</td>
                <td class="p-3 border-b">{{ b.vendor.name }}</td>
                <td class="p-3 border-b">{{ b.bill_date }}</td>
                <td class="p-3 border-b text-right">₹{{ Number(b.grand_total).toFixed(2) }}</td>
                <td class="p-3 border-b text-right">₹{{ Number(b.paid_amount).toFixed(2) }}</td>
                <td class="p-3 border-b text-right">₹{{ Number(b.due_amount).toFixed(2) }}</td>
                <td class="p-3 border-b text-center">
                  <button 
                    class="btn btn-sm btn-primary text-blue-600 underline"
                    @click="$router.push(`/purchase-bills/${b.id}`)"
                  >
                    View
                  </button>
                </td>
              </tr>

              <tr v-if="bills.length === 0">
                <td colspan="7" class="text-center p-4 text-gray-400">No purchase bills found</td>
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
  components: { Sidebar },
  data() {
    return {
      bills: []
    }
  },
  created() {
    this.loadData()
  },
  methods: {
    async loadData() {
      try {
        const res = await api.get('/purchase-bills')
        this.bills = res.data.data.map(b => ({
          ...b,
          grand_total: Number(b.grand_total),
          paid_amount: Number(b.paid_amount),
          due_amount: Number(b.due_amount)
        }))
      } catch (e) {
        console.log(e)
      }
    },
    async logout() {
      try {
        await api.post('/logout')
        localStorage.removeItem('token')
        this.$router.push('/login')
      } catch (e) {
        console.log(e.response?.data?.message)
      }
    }
  }
}
</script>

<style scoped>
.table th, .table td {
  vertical-align: middle;
}
</style>
