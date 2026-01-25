<template>
  <div class="flex h-screen bg-gray-100">
 
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-5xl mx-auto">

        <div class="bg-white shadow-lg rounded-2xl p-6">
          <h2 class="text-2xl font-bold text-gray-800 mb-6">Categories</h2>

          <form @submit.prevent="saveCategory" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <input
              type="text"
              v-model="form.name"
              placeholder="Category Name"
              class="border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400 transition"
              required
            />

            <select
              v-model="form.parent_id"
              class="border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400 transition"
            >
              <option value="">No Parent</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>

            <button
              type="submit"
              class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold rounded-xl px-4 py-3 transition shadow-md cursor-pointer"
            >
              {{ editId ? 'Update' : 'Save' }}
            </button>

            <button
              v-if="editId"
              type="button"
              @click="resetForm"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-xl px-4 py-3 transition shadow-md"
            >
              Cancel
            </button>
          </form>

          <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Parent</th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="cat in categories" :key="cat.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 text-gray-800">{{ cat.name }}</td>
                  <td class="px-6 py-4 text-gray-500">{{ cat.parent?.name || '-' }}</td>
                  <td class="px-6 py-4 text-center flex justify-center gap-2">
                    <button
                      @click="editCategory(cat)"
                      class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg transition"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteCategory(cat.id)"
                      class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg transition"
                    >
                      Delete
                    </button>
                  </td>
                </tr>

                <tr v-if="categories.length === 0">
                  <td colspan="3" class="py-6 text-center text-gray-400">No categories found</td>
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
import Sidebar from '../components/Sidebar.vue'
import api from '../axios'

export default {
  name: 'Category',
  components: { Sidebar },
  data() {
    return {
      categories: [],
      form: {
        name: '',
        parent_id: ''
      },
      editId: null
    }
  },
  created() {
    this.getCategories()
  },
  methods: {
    async getCategories() {
      try {
        const res = await api.get('/categories')
        this.categories = res.data
      } catch (e) {
        console.log(e)
      }
    },
    async saveCategory() {
      try {
        if (this.editId) {
          await api.put(`/categories/${this.editId}`, this.form)
        } else {
          await api.post('/categories', this.form)
        }
        this.resetForm()
        this.getCategories()
      } catch (e) {
        console.log(e)
      }
    },
    editCategory(cat) {
      this.editId = cat.id
      this.form.name = cat.name
      this.form.parent_id = cat.parent?.id || ''
    },
    async deleteCategory(id) {
      if (!confirm('Are you sure you want to delete this category?')) return
      try {
        await api.delete(`/categories/${id}`)
        this.getCategories()
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
      this.form.name = ''
      this.form.parent_id = ''
      this.editId = null
    }
  }
}
</script>

<style>
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.2);
  border-radius: 3px;
}
</style>
