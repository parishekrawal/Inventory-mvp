<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
    <div class="w-full max-w-sm">

      <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Sign in to your account</h1>
        <p class="text-sm text-slate-500 mt-1">
          Welcome back, please enter your credentials
        </p>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 space-y-5">

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

        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">
            Password
          </label>
          <input
            v-model="password"
            type="password"
            placeholder="Enter your password"
            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <div v-if="errors.length" class="rounded-xl bg-red-50 border border-red-200 px-3 py-2 space-y-1">
          <p
            v-for="error in errors"
            :key="error"
            class="text-sm text-red-600"
          >
            {{ error }}
          </p>
        </div>

        <button
          @click="login"
          class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition shadow-sm cursor-pointer"
        >
          Sign In
        </button>

        <button
          @click="$router.push('/forgot-password')"
          class="w-full text-sm text-blue-600 hover:underline text-center"
        >
          Forgot your password?
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
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      errors: []
    }
  },
  methods: {
    async login() {
      try {
        const res = await api.post('/login', {
          email: this.email,
          password: this.password
        })
        localStorage.setItem('token', res.data.token)
        this.$router.push('/dashboard')
      } catch (e) {
        this.errors = Object.values(e?.response?.data?.errors || {}).flat()
      }
    }
  }
}
</script>
