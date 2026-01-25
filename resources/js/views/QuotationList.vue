<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-6xl mx-auto space-y-6">

        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Quotations</h2>
            <button 
              @click="$router.push('/quotations-create')" 
              class="btn-primary w-full md:w-auto cursor-pointer">
              Create New Quotation
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left font-semibold text-gray-700">No</th>
                  <th class="px-6 py-3 text-left font-semibold text-gray-700">Customer</th>
                  <th class="px-6 py-3 text-left font-semibold text-gray-700">Date</th>
                  <th class="px-6 py-3 text-left font-semibold text-gray-700">Total</th>
                  <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                  <th class="px-6 py-3 text-center font-semibold text-gray-700">Actions</th>
                </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="q in quotations" :key="q.id" class="hover:bg-gray-50">
                  <td class="px-6 py-3">{{ q.quotation_no }}</td>
                  <td class="px-6 py-3">{{ q.customer.name }}</td>
                  <td class="px-6 py-3">{{ new Date(q.quotation_date).toLocaleDateString() }}</td>
                  <td class="px-6 py-3">{{ q.grand_total.toFixed(2) }}</td>
                  <td class="px-6 py-3">
                    <span :class="q.status === 'converted' ? 'text-green-600 font-semibold' : 'text-yellow-600 font-semibold'">
                      {{ q.status }}
                    </span>
                  </td>
                  <td class="px-6 py-3 text-center space-x-2 flex justify-center items-center">
                    <button 
                      v-if="q.status === 'draft'" 
                      @click="editQuotation(q.id)" 
                      class="btn-sm btn-warning">
                      Edit
                    </button>

                    <button 
                      v-if="q.status === 'draft'" 
                      @click="convert(q.id)" 
                      class="btn-sm btn-success">
                      Convert
                    </button>

                    <button 
                      @click="downloadPdf(q.id, q.quotation_no)" 
                      class="btn-sm btn-primary">
                      PDF
                    </button>

                    <button 
                      @click="viewQuotation(q.id)" 
                      class="btn-sm text-blue-600 underline">
                      View
                    </button>
                  </td>
                </tr>

                <tr v-if="quotations.length === 0">
                  <td colspan="6" class="text-center py-6 text-gray-400">No quotations found</td>
                </tr>
              </tbody>
            </table>
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
  name: 'QuotationsList',
  components: { Sidebar },
  data() {
    return {
      quotations: []
    }
  },
  created() {
    this.getQuotations();
  },
  methods: {
    async getQuotations() {
      try {
          const res = await api.get('/quotations');
          this.quotations = res.data.data.map(q => ({
          ...q, grand_total: Number(q.grand_total)
        }));
      } catch(e) { console.log(e); }
    },
    editQuotation(id) {
      this.$router.push(`/quotations/${id}/edit`);
    },
    async convert(id) {
      try {
        const res = await api.post(`/quotations/${id}/convert`);
        this.$router.push(`/invoices/${res.data.invoice_id}`);
      } catch(e) { console.log(e); }
    },
    async downloadPdf(id, quotation_no) {
      try {
        const res = await api.get(`/quotations/${id}/pdf`, { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `quotation-${quotation_no}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
      } catch(e) { console.log(e); }
    },
    viewQuotation(id) {
      this.$router.push(`/quotations/${id}`);
    },
    async logout() {
      try {
        await api.post('/logout');
        localStorage.removeItem('token');
        this.$router.push('/login');
      } catch(e) { console.log(e.response?.data?.message); }
    }
  }
}
</script>

<style>
.btn-primary {
  background-color: #06b6d4;
  color: #fff;
  font-weight: 600;
  padding: 0.5rem 1rem;
  border-radius: 10px;
  transition: all 0.2s;
}
.btn-primary:hover { background-color: #0891b2; }

.btn-secondary {
  background-color: #f3f4f6;
  color: #374151;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 10px;
  transition: all 0.2s;
}
.btn-secondary:hover { background-color: #e5e7eb; }

.btn-warning {
  background-color: #fbbf24;
  color: #fff;
  font-weight: 500;
  padding: 0.25rem 0.75rem;
  border-radius: 8px;
}
.btn-warning:hover { background-color: #f59e0b; }

.btn-success {
  background-color: #22c55e;
  color: #fff;
  font-weight: 500;
  padding: 0.25rem 0.75rem;
  border-radius: 8px;
}
.btn-success:hover { background-color: #16a34a; }

.btn-sm {
  font-size: 0.8rem;
  padding: 0.25rem 0.5rem;
}
</style>
