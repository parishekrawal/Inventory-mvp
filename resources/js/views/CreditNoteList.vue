<template>
  <div class="min-h-screen flex bg-slate-100 text-slate-800">
    <Sidebar @logout="logout" />

    <main class="ml-64 flex-1 p-8 overflow-y-auto">
      <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex justify-between items-center">
          <div>
            <h2 class="text-2xl font-bold text-slate-900">Credit Notes</h2>
            <p class="text-sm text-slate-500">Manage sales & purchase returns</p>
          </div>

          <button
            @click="$router.push('/credit-notes-create')"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white font-medium shadow transition cursor-pointer"
          >
            Create Credit Note
          </button>
        </div>

        <div class="bg-white rounded-2xl shadow border overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b">
              <tr class="text-slate-600">
                <th class="px-6 py-4 text-left">No</th>
                <th class="px-6 py-4 text-left">Type</th>
                <th class="px-6 py-4 text-left">Reference</th>
                <th class="px-6 py-4 text-left">Date</th>
                <th class="px-6 py-4 text-right">Amount</th>
                <th class="px-6 py-4 text-center">Action</th>
              </tr>
            </thead>

            <tbody class="divide-y">
              <tr
                v-for="c in creditNotes"
                :key="c.id"
                class="hover:bg-slate-50"
              >
                <td class="px-6 py-4 font-medium">
                  {{ c.note_no }}
                </td>

                <td class="px-6 py-4">
                  <span
                    class="inline-flex px-2 py-1 rounded-full text-xs font-semibold"
                    :class="c.type === 'sales'
                      ? 'bg-red-100 text-red-700'
                      : 'bg-blue-100 text-blue-700'"
                  >
                    {{ c.type === 'sales' ? 'Sales Return' : 'Purchase Return' }}
                  </span>
                </td>

                <td class="px-6 py-4">
                  <span v-if="c.type === 'sales'">
                    INV-{{ c.invoice?.id }}
                  </span>
                  <span v-else>
                    PB-{{ c.purchase_bill?.id }}
                  </span>
                </td>

                <td class="px-6 py-4 text-slate-600">
                  {{ c.return_date }}
                </td>

                <td class="px-6 py-4 text-right font-semibold">
                  ₹{{ c.total_amount }}
                </td>

                <td class="px-6 py-4 text-center">
                  <button
                    @click="$router.push(`/credit-notes/${c.id}`)"
                    class="text-cyan-600 hover:text-cyan-800 font-medium"
                  >
                    View
                  </button>
                </td>
              </tr>

              <tr v-if="creditNotes.length === 0">
                <td colspan="6" class="py-10 text-center text-slate-400">
                  No credit notes found
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </main>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import api from '../axios'

export default {
  components: { Sidebar },
  data() {
    return {
      creditNotes: []
    }
  },
  created() {
    this.loadCreditNotes()
  },
  methods: {
    async loadCreditNotes() {
      const res = await api.get('/credit-notes')
      this.creditNotes = res.data.data
    },
    async logout() {
      await api.post('/logout')
      localStorage.removeItem('token')
      this.$router.push('/login')
    }
  }
}
</script>
