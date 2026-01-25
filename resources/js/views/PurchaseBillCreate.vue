<template>
  <div class="flex min-h-screen bg-gray-100">
    <Sidebar @logout="logout"/>

    <div class="flex-1 p-6 ml-65 max-w-5xl mx-auto">
      <div class="bg-white shadow-lg rounded-2xl p-6 ml-60">

        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-semibold text-gray-800">Create Purchase Bill</h2>
          <button class="btn btn-secondary cursor-pointer" @click="resetForm">Reset</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <div>
            <label class="block mb-1 font-medium text-gray-700">Vendor</label>
            <select v-model="form.vendor_id" class="input w-full">
              <option value="">Select Vendor</option>
              <option v-for="v in vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
            </select>
          </div>

          <div>
            <label class="block mb-1 font-medium text-gray-700">Notes</label>
            <textarea 
              v-model="form.notes" 
              class="input w-full h-20 resize-none" 
              placeholder="Write notes"
            ></textarea>
          </div>
        </div>

        <div class="overflow-x-auto mb-4">
          <table class="w-full text-sm border border-gray-200 rounded-lg">
            <thead class="bg-gray-50 text-gray-600">
              <tr>
                <th class="p-2 border-b">Product</th>
                <th class="p-2 border-b">Quantity</th>
                <th class="p-2 border-b">Price</th>
                <th class="p-2 border-b">Tax %</th>
                <th class="p-2 border-b">Total</th>
                <th class="p-2 border-b">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, i) in form.items" :key="i" class="hover:bg-gray-50 text-center">
                <td class="p-2 border-b">
                  <select v-model="item.product_id" @change="setProduct(item)" class="input w-full">
                    <option value="">Select Product</option>
                    <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
                  </select>
                </td>
                <td class="p-2 border-b">
                  <input type="number" v-model.number="item.quantity" class="input w-20" />
                </td>
                <td class="p-2 border-b">
                  <input type="number" v-model.number="item.price" class="input w-24" />
                </td>
                <td class="p-2 border-b">
                  <input type="number" v-model.number="item.tax_percent" class="input w-20" />
                </td>
                <td class="p-2 border-b">₹{{ lineTotal(item).toFixed(2) }}</td>
                <td class="p-2 border-b">
                  <button @click="removeItem(i)" class="text-red-500 font-bold">✕</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <button class="btn btn-sm btn-primary mb-4 cursor-pointer" @click="addItem">+ Add Item</button>

        <div class="max-w-xs ml-auto text-right space-y-1 mb-4">
          <div>Subtotal: ₹{{ subtotal.toFixed(2) }}</div>
          <div>Tax: ₹{{ taxTotal.toFixed(2) }}</div>
          <div class="font-bold text-lg">Grand Total: ₹{{ grandTotal.toFixed(2) }}</div>
        </div>

        <button class="btn btn-primary w-full cursor-pointer" @click="saveBill">Save Purchase Bill</button>

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
      vendors: [],
      products: [],
      form: {
        vendor_id: '',
        notes: '',
        items: [
          { product_id: '', quantity: 1, price: 0, tax_percent: 0 }
        ]
      }
    }
  },
  computed: {
    subtotal() {
      return this.form.items.reduce((sum, item) => sum + this.lineTotal(item), 0)
    },
    taxTotal() {
      return this.form.items.reduce((sum, item) => sum + (item.price * item.quantity * item.tax_percent / 100), 0)
    },
    grandTotal() {
      return this.subtotal + this.taxTotal
    }
  },
  created() {
    this.loadData()
  },
  methods: {
    async loadData() {
      try {
        this.vendors = (await api.get('/vendors')).data.data
        this.products = (await api.get('/products')).data.data
      } catch(e) {
        console.log(e)
      }
    },
    addItem() {
      this.form.items.push({ product_id: '', quantity: 1, price: 0, tax_percent: 0 })
    },
    removeItem(i) {
      this.form.items.splice(i, 1)
    },
    setProduct(item) {
      const product = this.products.find(p => p.id === item.product_id)
      if(product) {
        item.price = Number(product.purchase_price)
        item.tax_percent = product.tax?.percentage || 0
      }
    },
    lineTotal(item) {
      return (item.price * item.quantity) + (item.price * item.quantity * item.tax_percent / 100)
    },
    async saveBill() {
      try {
        await api.post('/purchase-bills', this.form)
        this.resetForm()
      } catch(e) {
        console.log(e)
      }
    },
    resetForm() {
      this.form = {
        vendor_id: '',
        notes: '',
        items: [{ product_id: '', quantity: 1, price: 0, tax_percent: 0 }]
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
.input {
  border: 1px solid #ccc;
  border-radius: 0.5rem;
  padding: 0.5rem 0.75rem;
  outline: none;
  transition: all 0.2s;
}
.input:focus {
  border-color: #22d3ee;
  box-shadow: 0 0 0 2px rgba(34,211,238,0.3);
}
</style>
