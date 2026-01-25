<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 ml-65 overflow-y-auto">
      <div class="max-w-6xl mx-auto space-y-6">

        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">
              {{ isEdit ? 'Edit Quotation' : 'Create Quotation' }}
            </h2>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
              <select v-model="form.customer_id" class="input w-full">
                <option value="">Select Customer</option>
                <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
              <textarea
                v-model="form.notes"
                rows="2"
                placeholder="Write notes"
                class="input w-full resize-none"
              ></textarea>
            </div>
          </div>

          <div class="overflow-x-auto mb-4">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-left font-semibold text-gray-700">Product</th>
                  <th class="px-4 py-2 text-left font-semibold text-gray-700">Quantity</th>
                  <th class="px-4 py-2 text-left font-semibold text-gray-700">Price</th>
                  <th class="px-4 py-2 text-left font-semibold text-gray-700">Tax %</th>
                  <th class="px-4 py-2 text-left font-semibold text-gray-700">Total</th>
                  <th class="px-4 py-2"></th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(item,index) in items" :key="index" class="hover:bg-gray-50">
                  <td class="px-4 py-2">
                    <select v-model="item.product_id" @change="setProduct(item)" class="input w-full">
                      <option value="">Select</option>
                      <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                  </td>
                  <td class="px-4 py-2">
                    <input type="number" v-model.number="item.quantity" class="input w-20" />
                  </td>
                  <td class="px-4 py-2">₹{{ item.price }}</td>
                  <td class="px-4 py-2">{{ item.tax_percent }}%</td>
                  <td class="px-4 py-2">₹{{ lineTotal(item) }}</td>
                  <td class="px-4 py-2 text-center">
                    <button @click="removeRow(index)" class="text-red-500 font-bold text-lg">✕</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <button @click="addRow" class="btn-sm mb-6 btn-primary cursor-pointer">+ Add Product</button>

          <div class="max-w-sm space-y-2 mb-6">
            <div class="flex justify-between"><span>Subtotal:</span><span>₹{{ subtotal.toFixed(2) }}</span></div>
            <div class="flex justify-between"><span>Tax:</span><span>₹{{ taxTotal.toFixed(2) }}</span></div>
            <div class="flex justify-between items-center">
              <span>Discount:</span>
              <input type="number" v-model.number="form.discount" class="input w-24 text-right" />
            </div>
            <div class="flex justify-between font-bold text-lg"><span>Grand Total:</span><span>₹{{ grandTotal.toFixed(2) }}</span></div>
          </div>

          <div class="flex justify-end">
            <button @click="saveQuotation" class="btn-primary cursor-pointer">
              {{ isEdit ? 'Update Quotation' : 'Save Quotation' }}
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue';
import api from '../axios';

export default {
  name: 'QuotationPage',
  components: { Sidebar },
  data() {
    return {
      customers: [],
      products: [],
      items: [],
      form: {
        customer_id: '',
        notes: '',
        discount: 0
      },
      isEdit: false
    };
  },
  computed: {
    subtotal() {
      return this.items.reduce((sum, item) => sum + this.lineTotal(item), 0);
    },
    taxTotal() {
      return this.items.reduce((sum, item) => sum + (item.price * item.quantity * item.tax_percent / 100), 0);
    },
    grandTotal() {
      return this.subtotal + this.taxTotal - this.form.discount;
    }
  },
  created() {
    this.loadData();
    this.addRow();
    if(this.$route.params.id) {
      this.isEdit = true;
      this.fetchQuotation();
    }
  },
  methods: {
    async loadData() {
      this.products = (await api.get('/products')).data.data;
      this.customers = (await api.get('/customers')).data.data;
    },
    async fetchQuotation() {
      const res = await api.get(`/quotations/${this.$route.params.id}`);
      const q = res.data.data;
      this.form.customer_id = q.customer_id;
      this.form.notes = q.notes;
      this.form.discount = q.discount;
      this.items = q.items.map(i => ({ ...i }));
    },
    lineTotal(item) {
      return item.price * item.quantity;
    },
    addRow() {
      this.items.push({ product_id: '', quantity: 1, price: 0, tax_percent: 0 });
    },
    setProduct(item) {
      const product = this.products.find(p => p.id == item.product_id);
      if(product) {
        item.price = product.sale_price;
        item.tax_percent = product.tax?.percentage || 0;
      }
    },
    removeRow(index) {
      this.items.splice(index, 1);
    },
    async saveQuotation() {
      try {
        const data = { ...this.form, items: this.items };
        if(this.isEdit) await api.put(`/quotations/${this.$route.params.id}`, data);
        else await api.post('/quotations', data);
        this.isEdit = false;
        this.form.customer_id = '';
        this.form.notes = '';
        this.form.discount = 0;
        this.items = [];
        this.addRow();
      } catch(e) { console.log(e); }
    },
    async logout() {
      try {
        await api.post('/logout');
        localStorage.removeItem('token');
        this.$router.push('/login');
      } catch(e) { console.log(e.response?.data?.message); }
    }
  }
};
</script>

<style>
.input {
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 0.5rem;
  outline: none;
  transition: all 0.2s;
}
.input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
}

.btn-primary {
  background-color: #06b6d4;
  color: white;
  font-weight: 600;
  padding: 0.5rem 1rem;
  border-radius: 10px;
  transition: all 0.2s;
}
.btn-primary:hover { background-color: #0891b2; }

.btn-sm {
  background-color: #f3f4f6;
  color: #374151;
  padding: 0.3rem 0.7rem;
  font-weight: 500;
  border-radius: 8px;
  transition: all 0.2s;
}
.btn-sm:hover { background-color: #e5e7eb; }
</style>
