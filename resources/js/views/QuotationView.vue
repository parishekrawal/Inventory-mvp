<template>
  <div class="flex min-h-screen bg-gray-100">
    <Sidebar @logout="logout"/>

    <div class="flex-1 p-6 ml-65">
      <div class="max-w-5xl mx-auto bg-white shadow-lg rounded-2xl p-6">

        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-semibold text-gray-800">Quotation #{{ quotation.quotation_no }}</h2>
          <span 
            :class="quotation.status === 'converted' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'" 
            class="px-3 py-1 rounded-full text-sm font-medium"
          >
            {{ quotation.status }}
          </span>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
          <div class="space-y-1">
            <p class="text-gray-600"><span class="font-semibold">Customer:</span> {{ quotation.customer?.name || '-' }}</p>
            <p class="text-gray-600"><span class="font-semibold">Phone:</span> {{ quotation.customer?.phone || '-' }}</p>
            <p class="text-gray-600"><span class="font-semibold">GST:</span> {{ quotation.customer?.gst_number || '-' }}</p>
          </div>

          <div class="space-y-1 text-right">
            <p class="text-gray-600"><span class="font-semibold">Date:</span> {{ quotation.quotation_date }}</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border border-gray-200 rounded-lg">
            <thead class="bg-gray-50 text-gray-700">
              <tr>
                <th class="p-3 border-b">Product</th>
                <th class="p-3 border-b">Quantity</th>
                <th class="p-3 border-b">Price</th>
                <th class="p-3 border-b">Tax</th>
                <th class="p-3 border-b">Total</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="item in quotation.items" :key="item.id" class="text-gray-700 hover:bg-gray-50">
                <td class="p-3 border-b">{{ item.product.name }}</td>
                <td class="p-3 border-b">{{ item.quantity }}</td>
                <td class="p-3 border-b">₹{{ Number(item.price).toFixed(2) }}</td>
                <td class="p-3 border-b">₹{{ Number(item.tax_amount).toFixed(2) }}</td>
                <td class="p-3 border-b font-medium">₹{{ Number(item.total).toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-6 flex justify-end space-y-1 flex-col text-gray-700 text-right">
          <p><span class="font-medium">Subtotal:</span> ₹{{ Number(quotation.subtotal).toFixed(2) }}</p>
          <p><span class="font-medium">Tax:</span> ₹{{ Number(quotation.tax_total).toFixed(2) }}</p>
          <p><span class="font-medium">Discount:</span> ₹{{ Number(quotation.discount).toFixed(2) }}</p>
          <p class="text-xl font-bold text-gray-900">Grand Total: ₹{{ Number(quotation.grand_total).toFixed(2) }}</p>
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
      quotation: {
        customer: {},
        items: []
      }
    }
  },
  created() {
    this.loadQuotation()
  },
  methods: {
    async loadQuotation() {
      try {
        const res = await api.get(`/quotations/${this.$route.params.id}`)
        const q = res.data.data
        q.grand_total = Number(q.grand_total)
        q.subtotal = Number(q.subtotal)
        q.tax_total = Number(q.tax_total)
        q.discount = Number(q.discount)
        q.items = q.items.map(i => ({
          ...i,
          price: Number(i.price),
          tax_amount: Number(i.tax_amount),
          total: Number(i.total)
        }))
        this.quotation = q
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
