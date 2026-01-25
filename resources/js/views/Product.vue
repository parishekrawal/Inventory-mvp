<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg p-6 overflow-x-auto">
          <h2 class="text-2xl font-bold text-gray-800 mb-6">Product List</h2>

          <router-link to="/product-page" class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg mb-4 inline-block">
            Add Product
          </router-link>

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Unit</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tax</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Purchase</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Sale</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="prod in products" :key="prod.id" class="hover:bg-gray-50">
                <td class="px-6 py-4">{{ prod.name }}</td>
                <td class="px-6 py-4">{{ prod.category?.name || '-' }}</td>
                <td class="px-6 py-4">{{ prod.unit?.name || '-' }}</td>
                <td class="px-6 py-4">{{ prod.tax?.name }} ({{ prod.tax?.percentage }}%)</td>
                <td class="px-6 py-4">{{ prod.purchase_price }}</td>
                <td class="px-6 py-4">{{ prod.sale_price }}</td>
                <td class="px-6 py-4 text-center flex justify-center gap-2">
                  <router-link 
                    :to="{ name: 'product-page', params: { id: prod.id } }"
                    class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg shadow-sm transition">
                    Edit
                  </router-link>
                  <button @click="deleteProduct(prod.id)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg shadow-sm transition">
                    Delete
                  </button>
                </td>
              </tr>

              <tr v-if="products.length === 0">
                <td colspan="7" class="text-center py-6 text-gray-400">No products found</td>
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
  name: 'ProductList',
  components: { Sidebar },
  data() {
    return { products: [] };
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
    async deleteProduct(id) {
      if (!confirm('Delete Product?')) return;
      try { 
        await api.delete(`/products/${id}`);
        this.getProducts();
      } catch(e){ console.log(e); }
    },
    async logout() {
      try { 
        await api.post('/logout'); 
        localStorage.removeItem('token'); 
        this.$router.push('/login'); 
      } catch(e){ console.log(e); }
    }
  }
}
</script>
