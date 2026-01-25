<template>
  <div class="flex h-screen bg-gray-100">

    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-3xl mx-auto">

        <div class="bg-white shadow-lg rounded-2xl p-6">
          <h2 class="text-2xl font-bold text-gray-800 mb-6">Units</h2>

          <form @submit.prevent="saveUnit" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <input
              type="text"
              v-model="form.name"
              placeholder="Unit Name"
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
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-xl px-4 py-3 shadow-md transition"
            >
              Cancel
            </button>
          </form>

          <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Unit</th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="unit in units" :key="unit.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 text-gray-800">{{ unit.name }}</td>
                  <td class="px-6 py-4 text-center flex justify-center gap-2">
                    <button
                      class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg transition shadow-sm"
                      @click="editUnit(unit)"
                    >
                      Edit
                    </button>
                    <button
                      class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg transition shadow-sm"
                      @click="deleteUnit(unit.id)"
                    >
                      Delete
                    </button>
                  </td>
                </tr>

                <tr v-if="units.length === 0">
                  <td colspan="2" class="py-6 text-center text-gray-400">No units found</td>
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
  name: 'Unit',
  components: { Sidebar },
  data() {
    return {
      units: [],
      form: { name: '' },
      editId: null,
    };
  },
  mounted() {
    this.getUnits();
  },
  methods: {
    async getUnits() {
      try {
        const res = await api.get('/units');
        this.units = res.data.data;
      } catch (e) {
        console.log(e);
      }
    },
    async saveUnit() {
      try {
        if (this.editId) {
          await api.put(`/unit/${this.editId}`, this.form);
        } else {
          await api.post('/units', this.form);
        }
        this.resetForm();
        this.getUnits();
      } catch (e) {
        console.log(e);
      }
    },
    editUnit(unit) {
      this.editId = unit.id;
      this.form.name = unit.name;
    },
    async deleteUnit(id) {
      if (!confirm('Are you sure you want to delete this unit?')) return;
      try {
        await api.delete(`/unit/${id}`);
        this.getUnits();
      } catch (e) {
        console.log(e);
      }
    },
    async logout() {
      try {
        await api.post('/logout');
        localStorage.removeItem('token');
        this.$router.push('/login');
      } catch (e) {
        console.log(e.response?.data?.message);
      }
    },
    resetForm() {
      this.editId = null;
      this.form.name = '';
    },
  },
};
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
