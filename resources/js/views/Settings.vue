<template>
  <div class="min-h-screen flex bg-slate-50 text-slate-800">
    <Sidebar @logout="logout" />

    <main class="ml-64 flex-1 p-8">
      <div class="max-w-3xl mx-auto">

        <header class="mb-10">
          <h1 class="text-3xl font-bold text-slate-900">Settings</h1>
          <p class="text-sm text-slate-500 mt-1">
            Manage your company profile and preferences
          </p>
        </header>

        <section class="bg-white rounded-2xl shadow-lg border border-slate-200 p-8 space-y-8">

          <form @submit.prevent="saveSettings" class="space-y-6">

            <div>
              <label class="block text-sm font-medium text-slate-600 mb-1">
                Company Name
              </label>
              <input
                type="text"
                v-model="form.company_name"
                placeholder="Your company name"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-600 mb-2">
                Company Logo
              </label>

              <div class="flex items-center gap-4">
                <div
                  class="w-24 h-24 rounded-xl border border-slate-300 bg-slate-50 flex items-center justify-center overflow-hidden"
                >
                  <img
                    v-if="logoSrc"
                    :src="logoSrc"
                    class="w-full h-full object-contain"
                  />
                  <span v-else class="text-xs text-slate-400">No Logo</span>
                </div>

                <input
                  type="file"
                  @change="handleLogo"
                  class="text-sm file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-cyan-100 file:text-cyan-700 hover:file:bg-cyan-200 transition"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-600 mb-1">
                Address
              </label>
              <textarea
                rows="2"
                v-model="form.address"
                placeholder="Company address"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition"
              />
            </div>

            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">
                  GST Number
                </label>
                <input
                  type="text"
                  v-model="form.gst_number"
                  placeholder="GSTIN"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">
                  Invoice Prefix
                </label>
                <input
                  type="text"
                  v-model="form.invoice_prefix"
                  placeholder="INV-"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition"
                />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">
                  Financial Year Start
                </label>
                <input
                  type="date"
                  v-model="form.financial_year_start"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">
                  Financial Year End
                </label>
                <input
                  type="date"
                  v-model="form.financial_year_end"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition"
                />
              </div>
            </div>

            <button
              type="submit"
              class="w-full py-3 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-semibold shadow-md transition"
            >
              Save Settings
            </button>

          </form>

          <div
            v-if="message"
            class="text-center text-sm text-green-700 bg-green-100 rounded-xl py-2"
          >
            {{ message }}
          </div>

        </section>
      </div>
    </main>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import api from '../axios'

export default {
  name: 'Settings',
  components: { Sidebar },
  data() {
    return {
      form: {},
      logoFile: null,
      logoPreview: '',
      message: ''
    }
  },
  created() {
    this.getSettings()
  },
  computed: {
    logoSrc() {
      return (
        this.logoPreview ||
        (this.form.logo
          ? `${import.meta.env.VITE_BASE_URL}/storage/${this.form.logo}`
          : '')
      )
    }
  },
  methods: {
    async getSettings() {
      const res = await api.get('/settings')
      this.form = res.data.data || {}
    },
    handleLogo(e) {
      const file = e.target.files[0]
      if (!file) return
      this.logoFile = file
      this.logoPreview = URL.createObjectURL(file)
    },
    async saveSettings() {
      const data = new FormData()
      Object.keys(this.form).forEach(k => data.append(k, this.form[k]))
      if (this.logoFile) data.append('logo', this.logoFile)
      const res = await api.post('/settings', data)
      this.message = res.data.message
    },
    async logout() {
      await api.post('/logout')
      localStorage.removeItem('token')
      this.$router.push('/login')
    }
  }
}
</script>
