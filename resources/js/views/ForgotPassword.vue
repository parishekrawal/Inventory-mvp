<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
    <div class="w-full max-w-sm">

      <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Reset your password</h1>
        <p class="text-sm text-slate-500 mt-1">
          Enter your email and we’ll send you a secure reset link
        </p>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-5">

        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">
            Email address
          </label>
          <input
            v-model="email"
            type="email"
            placeholder="you@company.com"
            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <div
          v-if="message"
          class="text-sm rounded-xl px-3 py-2 text-center"
          :class="isSuccess ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-600 border border-red-200'"
        >
          {{ message }}
        </div>

        <button
          @click="sendResetLink"
          class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition shadow-sm cursor-pointer"
        >
          Send reset link
        </button>

        <button
          @click="$router.push('/login')"
          class="w-full text-sm text-blue-600 hover:underline text-center cursor-pointer"
        >
          Back to sign in
        </button>

      </div>

      <p class="text-center text-xs text-slate-400 mt-6">
        © 2026 Your Company. All rights reserved.
      </p>
    </div>
  </div>
</template>

<script>
import api from '../axios.js'

export default {
  name: 'ForgotPassword',
  data() {
    return {
      email: '',
      message: '',
      isSuccess: false
    }
  },
  methods: {
    async sendResetLink() {
      try {
        const res = await api.post('/forgot-password', { email: this.email })
        this.message = res.data.message
        this.isSuccess = true
        this.email = ''
      } catch (e) {
        this.message = e.response?.data?.message || 'Unable to send reset link'
        this.isSuccess = false
      }
    }
  }
}
</script>
