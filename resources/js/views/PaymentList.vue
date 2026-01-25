<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-6xl mx-auto">

        <div class="overflow-x-auto bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Payments</h2>

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">No</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Amount</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Mode</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="p in payments" :key="p.id" class="hover:bg-gray-50">
                <td class="px-6 py-4">{{ p.payment_no }}</td>
                <td class="px-4 py-4">
                  <span
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                      :class="p.type === 'purchase'
                        ? 'bg-blue-100 text-blue-700'
                        : p.type === 'sales'
                          ? 'bg-green-100 text-green-700'
                          : 'bg-gray-100 text-gray-500'"
                    >
                        {{ p.type }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">₹{{ Number(p.amount).toFixed(2) }}</td>
                <td class="px-6 py-4">{{ p.mode }}</td>
                <td class="px-6 py-4">{{ new Date(p.payment_date).toLocaleDateString() }}</td>
              </tr>

              <tr v-if="payments.length === 0">
                <td colspan="6" class="text-center py-6 text-gray-400">No payments found</td>
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
  name: 'PaymentList',
  components: { Sidebar },
  data() {
    return { payments: [] }
  },
  created() {
    this.getPayments();
  },
  methods: {
    async getPayments() {
      try {
        const res = await api.get('/payments');
        this.payments = res.data.data;
      } catch(e) { console.log(e); }
    },
    async logout() {
      try {
        await api.post('/logout');
        localStorage.removeItem('token');
        this.$router.push('/login');
      } catch(e) { console.log(e); }
    },
  }
}
</script>
