<template>
  <div class="min-h-screen flex bg-gray-50">
    <Sidebar @logout="logout" />

    <main class="flex-1 ml-64 p-8">
      <div class="max-w-5xl mx-auto">

        <div class="flex justify-between items-center mb-6">
          <div>
            <h2 class="text-2xl font-bold text-gray-800">
              Invoice #{{ invoice?.invoice_no }}
            </h2>
            <p class="text-sm text-gray-500">
              Issued on {{ invoice?.invoice_date }}
            </p>
          </div>

          <div class="flex gap-3">
            <button
              @click="downloadPdf"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-800 text-white text-sm hover:bg-gray-800 transition"
            >
              ⬇ Download PDF
            </button>

            <button
              @click="receivePayment(invoice.id)"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700 transition"
            >
              💳 Receive Payment
            </button>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6 space-y-6">

          <div class="grid grid-cols-2 gap-6">
            <div>
              <p class="text-sm text-gray-500">Customer</p>
              <p class="font-medium text-gray-800">
                {{ invoice.customer?.name }}
              </p>
            </div>

            <div class="text-right">
              <p class="text-sm text-gray-500">Invoice Date</p>
              <p class="font-medium text-gray-800">
                {{ invoice.invoice_date }}
              </p>
            </div>
          </div>

          <div class="overflow-hidden rounded-xl border">
            <table class="w-full text-sm">
              <thead class="bg-gray-100 text-gray-600">
                <tr>
                  <th class="px-4 py-3 text-left">Product</th>
                  <th class="px-4 py-3 text-center">Qty</th>
                  <th class="px-4 py-3 text-right">Price</th>
                  <th class="px-4 py-3 text-right">Tax</th>
                  <th class="px-4 py-3 text-right">Total</th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="item in invoice.items"
                  :key="item.id"
                  class="border-t hover:bg-gray-50"
                >
                  <td class="px-4 py-3">
                    {{ item.product?.name }}
                  </td>
                  <td class="px-4 py-3 text-center">
                    {{ item.quantity }}
                  </td>
                  <td class="px-4 py-3 text-right">
                    ₹{{ item.price }}
                  </td>
                  <td class="px-4 py-3 text-right">
                    ₹{{ item.tax_amount }}
                  </td>
                  <td class="px-4 py-3 text-right font-medium">
                    ₹{{ item.total }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex justify-end">
            <div class="w-72 space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-500">Subtotal</span>
                <span>₹{{ invoice.subtotal }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Tax</span>
                <span>₹{{ invoice.tax_total }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Discount</span>
                <span>₹{{ invoice.discount }}</span>
              </div>

              <div class="flex justify-between pt-2 border-t font-semibold text-base">
                <span>Grand Total</span>
                <span class="text-green-600">
                  ₹{{ invoice.grand_total }}
                </span>
              </div>
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
  name: 'InvoiceView',
  components: { Sidebar },
  data() {
    return {
      invoice: { items: [], customer: {} }
    }
  },
  created() {
    this.getInvoice()
  },
  methods: {
    async getInvoice() {
      try {
        const res = await api.get(`/invoices/${this.$route.params.id}`)
        this.invoice = res.data
      } catch (e) {
        console.log(e)
      }
    },
    async downloadPdf() {
      try {
        const res = await api.get(
          `/invoices/${this.$route.params.id}/pdf`,
          { responseType: 'blob' }
        )
        const url = window.URL.createObjectURL(
          new Blob([res.data], { type: 'application/pdf' })
        )
        const link = document.createElement('a')
        link.href = url
        link.download = `Invoice-${this.invoice.invoice_no}.pdf`
        link.click()
      } catch (e) {
        console.log(e)
      }
    },
    receivePayment(id) {
      this.$router.push(`/sales-payment/${id}`)
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
