<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-30">
      <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl p-6">

        <h2 class="text-2xl font-bold text-gray-800 mb-6">
          Receive Customer Payment
        </h2>

        <div class="space-y-4 mb-6">
          <div class="flex justify-between text-gray-700">
            <span class="font-semibold">Invoice No</span>
            <span>{{ invoice.invoice_no }}</span>
          </div>

          <div class="flex justify-between text-gray-700">
            <span class="font-semibold">Grand Total</span>
            <span>₹{{ invoice.grand_total }}</span>
          </div>

          <div class="flex justify-between text-gray-700">
            <span class="font-semibold">Paid Amount</span>
            <span class="text-green-600 font-medium">
              ₹{{ invoice.paid_amount }}
            </span>
          </div>

          <div class="flex justify-between text-gray-700">
            <span class="font-semibold">Due Amount</span>
            <span class="text-red-600 font-semibold">
              ₹{{ invoice.due_amount }}
            </span>
          </div>
        </div>

        <div class="space-y-4 mb-6">
          <input
            type="number"
            v-model.number="form.amount"
            placeholder="Enter Amount"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />

          <select
            v-model="form.mode"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Select Payment Mode</option>
            <option value="cash">Cash</option>
            <option value="upi">UPI</option>
            <option value="bank">Bank</option>
          </select>
        </div>

        <button
          @click="savePayment"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition"
        >
          Save Payment
        </button>

      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import api from '../axios'

export default {
  name: 'SalesPayment',
  components: { Sidebar },
  data() {
    return {
      form: {
        invoice_id: '',
        type: 'sales',
        amount: '',
        mode: ''
      },
      invoice: {}
    }
  },
  created() {
    this.form.invoice_id = this.$route.params.id
    this.loadInvoice()
  },
  methods: {
    async loadInvoice() {
      const res = await api.get(`invoices/${this.form.invoice_id}`)
      this.invoice = res.data
    },
    async savePayment() {
      if (!this.form.amount || !this.form.mode) {
        alert('Please enter amount and select payment mode')
        return
      }

      await api.post('/payments', {
        form: this.form,
        invoice: this.invoice
      })

      alert('Payment received successfully')
      this.form.amount = ''
      this.form.mode = ''
    },
    async logout() {
      await api.post('/logout')
      localStorage.removeItem('token')
      this.$router.push('/login')
    }
  }
}
</script>
