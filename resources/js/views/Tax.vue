<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Taxes</h2>

        <form @submit.prevent="saveTax" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
          <input
            v-model="form.name"
            type="text"
            placeholder="Tax Name"
            class="border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400 transition"
            required
          />

          <input
            v-model="form.percentage"
            type="number"
            placeholder="Percentage"
            class="border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400 transition"
            required
          />

          <button
            type="submit"
            class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold rounded-xl px-4 py-3 shadow-md transition cursor-pointer"
          >
            {{ editId ? 'Update' : 'Save' }}
          </button>

          <button
            v-if="editId"
            type="button"
            @click="resetForm"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-xl px-4 py-3 shadow-md transition col-span-3 md:col-span-1"
          >
            Cancel
          </button>
        </form>

        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">%</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="tax in taxes" :key="tax.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-800">{{ tax.name }}</td>
                <td class="px-6 py-4 text-center text-gray-800">{{ tax.percentage }}</td>
                <td class="px-6 py-4 text-center flex justify-center gap-2">
                  <button
                    @click="editTax(tax)"
                    class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg transition shadow-sm"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteTax(tax.id)"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg transition shadow-sm"
                  >
                    Delete
                  </button>
                </td>
              </tr>
              <tr v-if="taxes.length === 0">
                <td colspan="3" class="text-center py-6 text-gray-400">No taxes found</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import api from '../axios.js'

export default {
  name: 'Tax',
  components: { Sidebar },
  data() {
    return {
      taxes: [],
      editId: null,
      form: { name: '', percentage: '' }
    }
  },
  created() {
    this.getTaxes()
  },
  methods: {
    async getTaxes() {
      try {
        const res = await api.get('/taxes')
        this.taxes = res.data.data
      } catch (e) {
        console.log(e)
      }
    },
    async saveTax() {
      try {
        if (this.editId) {
          await api.put(`/tax/${this.editId}`, this.form)
        } else {
          await api.post('/taxes', this.form)
        }
        this.resetForm()
        this.getTaxes()
      } catch (e) {
        console.log(e)
      }
    },
    editTax(tax) {
      this.editId = tax.id
      this.form.name = tax.name
      this.form.percentage = tax.percentage
    },
    async deleteTax(id) {
      if (!confirm('Are you sure you want to delete this tax?')) return
      try {
        await api.delete(`/tax/${id}`)
        this.getTaxes()
      } catch (e) {
        console.log(e)
      }
    },
    async logout() {
      try {
        await api.post('/logout')
        localStorage.removeItem('token')
        this.$router.push('/login')
      } catch (e) {
        console.log(e.response?.data?.message)
      }
    },
    resetForm() {
      this.editId = null
      this.form.name = ''
      this.form.percentage = ''
    }
  }
}
</script>
