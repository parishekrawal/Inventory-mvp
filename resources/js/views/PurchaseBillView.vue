<template>
  <div class="min-h-screen flex bg-slate-100 text-slate-800">
    <Sidebar @logout="logout" />

    <main class="ml-64 flex-1 p-8 overflow-y-auto">
      <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex justify-between items-center bg-white rounded-2xl shadow border p-6">
          <div>
            <h2 class="text-2xl font-bold text-slate-900">
              Purchase Bill
            </h2>
            <p class="text-sm text-slate-500">
              Bill No: {{ bill.bill_no }}
            </p>
          </div>

          <div class="flex gap-3">
            <button
              @click="downloadPdf"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium shadow transition"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-4-4m4 4l4-4M4 19h16"/>
              </svg>
              Download PDF
            </button>

            <button
              @click="receivePayment(bill.id)"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white font-medium shadow transition"
            >
              Send Payment
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-white rounded-2xl shadow border p-6 space-y-2">
            <h3 class="text-sm font-semibold text-slate-500 uppercase">
              Vendor Details
            </h3>
            <p><span class="font-medium">Name:</span> {{ bill.vendor?.name }}</p>
            <p><span class="font-medium">Phone:</span> {{ bill.vendor?.phone }}</p>
            <p><span class="font-medium">GST:</span> {{ bill.vendor?.gst_number }}</p>
          </div>

          <div class="bg-white rounded-2xl shadow border p-6 space-y-2">
            <h3 class="text-sm font-semibold text-slate-500 uppercase">
              Bill Information
            </h3>
            <p><span class="font-medium">Bill Date:</span> {{ bill.bill_date }}</p>
            <p><span class="font-medium">Created At:</span> {{ created_at }}</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow border overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b">
              <tr class="text-slate-600">
                <th class="px-6 py-4 text-left">Product</th>
                <th class="px-6 py-4 text-center">Qty</th>
                <th class="px-6 py-4 text-right">Price</th>
                <th class="px-6 py-4 text-right">Tax</th>
                <th class="px-6 py-4 text-right">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr
                v-for="item in bill.items"
                :key="item.id"
                class="hover:bg-slate-50"
              >
                <td class="px-6 py-4">{{ item.product.name }}</td>
                <td class="px-6 py-4 text-center">{{ item.quantity }}</td>
                <td class="px-6 py-4 text-right">₹{{ item.price }}</td>
                <td class="px-6 py-4 text-right">₹{{ item.tax_amount }}</td>
                <td class="px-6 py-4 text-right font-semibold">
                  ₹{{ item.total }}
                </td>
              </tr>

              <tr v-if="bill.items.length === 0">
                <td colspan="5" class="py-6 text-center text-slate-400">
                  No items found
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="flex justify-end">
          <div class="bg-white rounded-2xl shadow border p-6 w-full max-w-md space-y-3">
            <div class="flex justify-between text-slate-600">
              <span>Subtotal</span>
              <span class="font-medium">₹{{ bill.subtotal }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
              <span>Tax</span>
              <span class="font-medium">₹{{ bill.tax_total }}</span>
            </div>
            <div class="flex justify-between border-t pt-3">
              <span class="font-semibold text-lg">Grand Total</span>
              <span class="font-bold text-xl text-emerald-600">
                ₹{{ bill.grand_total }}
              </span>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import api from '../axios'

export default {
  components: { Sidebar },
  data() {
    return {
      bill: { items: [], vendor: {} },
      created_at: ''
    }
  },
  created() {
    this.loadPurchaseBill()
  },
  methods: {
    async loadPurchaseBill() {
      const res = await api.get(`/purchase-bills/${this.$route.params.id}`)
      this.bill = res.data.data
      this.created_at = new Date(this.bill.created_at).toLocaleString()
    },
    async downloadPdf() {
      const res = await api.get(
        `/purchase-bills/${this.$route.params.id}/pdf`,
        { responseType: 'blob' }
      )
      const url = URL.createObjectURL(res.data)
      const a = document.createElement('a')
      a.href = url
      a.download = `Purchase-${this.bill.bill_no}.pdf`
      a.click()
    },
    receivePayment(id) {
      this.$router.push(`/purchase-payment/${id}`)
    },
    async logout() {
      await api.post('/logout')
      localStorage.removeItem('token')
      this.$router.push('/login')
    }
  }
}
</script>
