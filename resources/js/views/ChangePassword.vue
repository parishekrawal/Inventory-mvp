<template>
  <div class="min-h-screen flex bg-slate-50">
    <Sidebar @logout="logout" />

    <main class="flex-1 ml-64 flex items-center justify-center p-6">
      <div class="w-full max-w-md mr-30 mb-25">

        <div class="mb-6 text-center">
          <h1 class="text-2xl font-bold text-slate-800">Change Password</h1>
          <p class="text-sm text-slate-500">
            Update your account security
          </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-8 space-y-6">

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-600 mb-1">
                Current Password
              </label>
              <input
                v-model="current"
                type="password"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter current password"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-600 mb-1">
                New Password
              </label>
              <input
                v-model="newPass"
                type="password"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter new password"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-600 mb-1">
                Confirm New Password
              </label>
              <input
                v-model="confirm"
                type="password"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Re-enter new password"
              />
            </div>
          </div>

          <div v-if="errors.length" class="rounded-xl bg-red-50 border border-red-200 p-3 space-y-1">
            <p
              v-for="error in errors"
              :key="error"
              class="text-sm text-red-600 text-center"
            >
              {{ error }}
            </p>
          </div>

          <div class="space-y-3">
            <button
              @click="changePassword"
              class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-sm transition"
            >
              Update Password
            </button>

            <button
              @click="$router.push('/dashboard')"
              class="w-full py-3 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 transition"
            >
              Cancel
            </button>
          </div>

        </div>
      </div>
    </main>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import api from '../axios.js'

export default {
  name: 'ChangePassword',
  components: { Sidebar },
  data() {
    return {
      current: '',
      newPass: '',
      confirm: '',
      errors: []
    }
  },
  methods: {
    async changePassword() {
      try {
        await api.post('/change-password', {
          current_password: this.current,
          new_password: this.newPass,
          new_password_confirmation: this.confirm
        })
        this.current = this.newPass = this.confirm = ''
        this.$router.push('/dashboard')
      } catch (e) {
        this.errors = Object.values(e?.response?.data?.errors || {}).flat()
      }
    },
    async logout() {
      await api.post('/logout')
      localStorage.removeItem('token')
      this.$router.push('/login')
    }
  }
}
</script>
