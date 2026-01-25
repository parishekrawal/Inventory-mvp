<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-30">
      <div class="max-w-md mx-auto bg-white shadow-xl rounded-2xl p-6">

        <h2 class="text-2xl font-bold text-gray-800 mb-6">Vendor Payment</h2>

        <div class="bg-gray-50 p-4 rounded-lg shadow-inner mb-6 space-y-2">
          <div class="flex justify-between text-gray-700">
            <span class="font-semibold">Bill No:</span>
            <span>{{ bills.bill_no }}</span>
          </div>
          <div class="flex justify-between text-gray-700">
            <span class="font-semibold">Grand Total:</span>
            <span class="font-semibold text-gray-800">₹{{ bills.grand_total }}</span>
          </div>
          <div class="flex justify-between text-gray-700">
            <span class="font-semibold">Paid Amount:</span>
            <span class="text-gray-600">₹{{ bills.paid_amount }}</span>
          </div>
          <div class="flex justify-between text-gray-700">
            <span class="font-semibold">Due Amount:</span>
            <span class="text-red-600 font-semibold">₹{{ bills.due_amount }}</span>
          </div>
        </div>

        <div class="space-y-4 mb-6">
          <input 
            type="number" 
            v-model.number="form.amount" 
            placeholder="Enter Amount" 
            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition"
          />

          <select 
            v-model="form.mode" 
            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition"
          >
            <option value="">Select Payment Mode</option>
            <option value="cash">Cash</option>
            <option value="upi">UPI</option>
            <option value="bank">Bank</option>
          </select>
        </div>

        <button 
          @click="savePayment"
          class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-3 rounded-xl shadow-lg transition"
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
  name: 'VendorPayment',
  components: { Sidebar },
  data() {
    return {
      bills: {},
      form: {
        type: 'purchase',
        purchase_bill_id: '',
        amount: '',
        mode: ''
      }
    }
  },
  created() {
    this.form.purchase_bill_id = this.$route.params.id
    this.loadBills()
  },
  methods: {
    async loadBills() {
      try {
        const res = await api.get(`/purchase-bills/${this.$route.params.id}`)
        this.bills = res.data.data
      } catch(e) {
        console.log(e)
      }
    },
    async savePayment() {
      if(!this.form.amount || !this.form.mode){
        alert('Please enter amount and select payment mode')
        return
      }
      try {
        await api.post('/payments', { form: this.form, purchase: this.bills })
        alert('Payment saved successfully')
        this.form.amount = ''
        this.form.mode = ''
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
        console.log(e.response?.data?.message)
      }
    }
  }
}
</script>

<style scoped>
input, select {
  transition: all 0.2s ease-in-out;
}
</style>
