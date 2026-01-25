<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-6xl mx-auto">

        <div class="bg-white rounded-2xl shadow-lg p-6 overflow-x-auto">
          <h2 class="text-2xl font-bold text-gray-800 mb-6">Customer List</h2>
  
          <router-link to="/customer-page" class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg mb-4 inline-block">
            Add Customer
          </router-link>

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">GST Number</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Balance</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="c in customers" :key="c.id" class="hover:bg-gray-50">
                <td class="px-6 py-4">{{ c.name }}</td>
                <td class="px-6 py-4">{{ c.phone }}</td>
                <td class="px-6 py-4">{{ c.gst_number }}</td>
                <td class="px-6 py-4">{{ c.opening_balance }}</td>
                <td class="px-6 py-4 text-center flex justify-center gap-2">
                  <router-link :to="{ name: 'customer-page', params: { id: c.id } }" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg shadow-sm transition">Edit</router-link>
                  <button @click="remove(c.id)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg shadow-sm transition">Delete</button>
                </td>
              </tr>

              <tr v-if="customers.length === 0">
                <td colspan="5" class="text-center py-6 text-gray-400">No customers found</td>
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
  name: 'CustomerList',
  components: { Sidebar },
  data() {
    return {
      customers: []
    }
  },
  created() {
    this.getCustomers();
  },
  methods: {
    async getCustomers() {
      try {
        const res = await api.get('/customers');
        this.customers = res.data.data;
      } catch(e) { console.log(e); }
    },
    async remove(id) {
      if(!confirm('Delete Customer?')) return;
      try {
        await api.delete(`/customers/${id}`);
        this.getCustomers();
      } catch(e) { console.log(e); }
    },
    async logout() {
      try {
        await api.post('/logout');
        localStorage.removeItem('token');
        this.$router.push('/login');
      } catch(e) { console.log(e); }
    }
  }
}
</script>
