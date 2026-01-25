<template>
  <div class="flex">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 flex justify-center ml-60">
      <div class="w-full max-w-5xl bg-white shadow rounded p-4">

        <h5 class="mb-4 font-semibold">
          {{ editId ? 'Edit Vendor' : 'Add Vendor' }}
        </h5>

        <form @submit.prevent="saveVendor" class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <input v-model="form.name" class="input" placeholder="Vendor Name" required />
          <input v-model="form.phone" class="input" placeholder="Phone" />
          <input v-model="form.gst_number" class="input" placeholder="GST Number" />

          <textarea
            v-model="form.address"
            class="input md:col-span-2"
            placeholder="Address"
          ></textarea>

          <input
            v-model="form.opening_balance"
            type="number"
            class="input"
            placeholder="Opening Balance"
          />

          <div class="md:col-span-3 flex gap-2">
            <button class="btn btn-primary">
              {{ editId ? 'Update' : 'Save' }}
            </button>
            <router-link to="/vendor-list" class="btn btn-secondary">
              Cancel
            </router-link>
          </div>
        </form>

      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import api from '../axios'

export default {
  name: 'VendorForm',
  components: { Sidebar },
  data() {
    return {
      editId: null,
      form: {
        name: '',
        phone: '',
        gst_number: '',
        address: '',
        opening_balance: ''
      }
    }
  },
  created() {
    if (this.$route.params.id) {
      this.editId = this.$route.params.id
      this.getVendor()
    }
  },
  methods: {
    async getVendor() {
      const res = await api.get(`/vendors/${this.editId}`)
      this.form = res.data.data
    },
    async saveVendor() {
      if (this.editId) {
        await api.put(`/vendors/${this.editId}`, this.form)
      } else {
        await api.post('/vendors', this.form)
      }
      this.$router.push('/vendor-list')
    },
    async logout() {
      await api.post('/logout')
      localStorage.removeItem('token')
      this.$router.push('/login')
    }
  }
}
</script>
