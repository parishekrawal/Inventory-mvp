<template>
  <div class="min-h-screen flex bg-gray-50">
    <Sidebar @logout="logout" />

    <main class="flex-1 ml-64 p-8">
      <div class="max-w-4xl mx-auto">

        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-800">
            Credit Note {{ creditNote.note_no }}
          </h1>
          <p class="text-sm text-gray-500">
            View credit note details
          </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6 space-y-6">

          <div class="flex justify-between items-start">
            <div class="space-y-1 text-sm text-gray-700">
              <p>
                <span class="font-medium">Date:</span>
                {{ creditNote.return_date }}
              </p>

              <p v-if="creditNote.type === 'sales'">
                <span class="font-medium">Invoice:</span>
                INV-{{ creditNote.invoice?.id }}
              </p>

              <p v-if="creditNote.type === 'purchase'">
                <span class="font-medium">Purchase Bill:</span>
                PB-{{ creditNote.purchase_bill?.id }}
              </p>
            </div>

            <span
              class="inline-flex px-3 py-1 rounded-full text-xs font-semibold"
              :class="creditNote.type === 'sales'
                ? 'bg-red-100 text-red-700'
                : 'bg-blue-100 text-blue-700'"
            >
              {{ creditNote.type === 'sales' ? 'Sales Return' : 'Purchase Return' }}
            </span>
          </div>

          <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-3">
              Returned Items
            </h3>

            <div class="overflow-hidden rounded-lg border">
              <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-600">
                  <tr>
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-center">Quantity</th>
                  </tr>
                </thead>

                <tbody class="divide-y">
                  <tr
                    v-for="item in creditNote.items"
                    :key="item.id"
                    class="hover:bg-gray-50"
                  >
                    <td class="px-4 py-2">
                      {{ item.product.name }}
                    </td>
                    <td class="px-4 py-2 text-center font-medium">
                      {{ item.quantity }}
                    </td>
                  </tr>

                  <tr v-if="!creditNote.items || creditNote.items.length === 0">
                    <td colspan="2" class="px-4 py-6 text-center text-gray-400">
                      No items found
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="flex justify-end pt-4 border-t">
            <div class="text-right">
              <p class="text-sm text-gray-500">Total Amount</p>
              <p class="text-2xl font-bold text-gray-800">
                ₹{{ creditNote.total_amount }}
              </p>
            </div>
          </div>

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
      creditNote: {}
    }
  },
  created() {
    this.loadCreditNote()
  },
  methods: {
    async loadCreditNote() {
      const res = await api.get(`/credit-notes/${this.$route.params.id}`)
      this.creditNote = res.data.data
    },
    async logout() {
      await api.post('/logout')
      localStorage.removeItem('token')
      this.$router.push('/login')
    }
  }
}
</script>
