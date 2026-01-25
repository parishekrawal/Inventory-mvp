<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout"/>

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-5xl mx-auto space-y-6">

        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-2xl font-bold text-gray-800 mb-6">Stock Management</h2>

          <form @submit.prevent="saveStock" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <select v-model="form.product_id" class="input-field" required>
              <option value="">Select Product</option>
              <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>

            <input type="number" v-model="form.quantity" placeholder="Quantity" class="input-field" required/>

            <select v-model="form.type" class="input-field" required>
              <option value="IN">Stock IN</option>
              <option value="OUT">Stock OUT</option>
            </select>

            <div class="md:col-span-3 flex justify-start mt-3">
              <button class="btn-primary w-half cursor-pointer">
                {{ form.type === 'IN' ? 'Add Stock' : 'Reduce Stock' }}
              </button>
            </div>
          </form>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 overflow-x-auto">
          <h3 class="text-xl font-semibold text-gray-800 mb-4">Stock Ledger</h3>

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Quantity</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="tx in ledger" :key="tx.id" class="hover:bg-gray-50">
                <td class="px-6 py-3">{{ tx.product_name }}</td>
                <td class="px-6 py-3">{{ tx.type }}</td>
                <td class="px-6 py-3">{{ tx.quantity }}</td>
                <td class="px-6 py-3">{{ new Date(tx.created_at).toLocaleString() }}</td>
              </tr>

              <tr v-if="ledger.length === 0">
                <td colspan="4" class="text-center py-6 text-gray-400">No stock transactions found</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue';
import api from '../axios';

export default {
  name: 'Stock',
  components: { Sidebar },
  data() {
    return {
      products: [],
      form: {
        product_id: '',
        quantity: '',
        type: 'IN',
      },
      ledger: []
    }
  },
  created() {
    this.getProducts();
  },
  methods: {
    async getProducts() {
      try {
        const res = await api.get('/products');
        this.products = res.data.data;
      } catch(e) { console.log(e); }
    },
    async getLedger() {
      if(!this.form.product_id) {
        this.ledger = [];
        return;
      }
      try {
        const res = await api.get(`/stock/ledger/${this.form.product_id}`);
        this.ledger = res.data.ledger.map(tx => ({
          ...tx,
          product_name: res.data.product.name
        }));
      } catch(e) { console.log(e); }
    },
    async saveStock() {
      if(!this.form.product_id || !this.form.quantity) return;
      try {
        await api.post(`/stock/${this.form.type.toLowerCase()}`, this.form);
        this.form.quantity = '';
        this.form.product_id = '';
        this.getLedger();
      } catch(e) { console.log(e); }
    },
    async logout() {
      try {
        await api.post('/logout');
        localStorage.removeItem('token');
        this.$router.push('/login');
      } catch(e) { console.log(e); }
    }
  },
  watch: {
    'form.product_id': function() {
      this.getLedger();
    }
  }
}
</script>

<style>
.input-field {
  border: 1px solid #ccc;
  border-radius: 12px;
  padding: 0.75rem 1rem;
  outline: none;
  transition: all 0.2s;
}
.input-field:focus {
  border-color: #22d3ee;
  box-shadow: 0 0 0 2px rgba(34,211,238,0.3);
}
.btn-primary {
  background-color: #06b6d4;
  color: #fff;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  transition: all 0.2s;
}
.btn-primary:hover { background-color: #0891b2; }
</style>
