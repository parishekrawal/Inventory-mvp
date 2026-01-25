<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-100 via-slate-200 to-slate-300 px-4">
    <div class="w-full max-w-md bg-white/80 backdrop-blur-xl rounded-2xl shadow-2xl p-8 space-y-6">
      <h2 class="text-2xl font-semibold text-center text-slate-900">Reset Password</h2>

      <div class="space-y-4">
        <input v-model="newPass" type="password" placeholder="Enter New Password" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
        <input v-model="confirm" type="password" placeholder="Confirm New Password" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
      </div>

      <div v-if="message" class="text-center text-red-600 text-sm">
        {{ message }}
      </div>

      <div class="space-y-3">
        <button @click="reset" class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold shadow-lg shadow-blue-600/30 transition-all duration-200 cursor-pointer">
          Reset Password
        </button>
        <button @click="$router.push('/dashboard')" class="w-full py-3 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 transition-all duration-200 cursor-pointer">
          Go Back
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../axios.js';

export default {
  name: 'ResetPassword',
  data() {
    return {
      token: '',
      email: '',
      newPass: '',
      confirm: '',
      message: ''
    };
  },
  mounted() {
    this.email = this.$route.query.email;
    this.token = this.$route.params.token;
  },
  methods: {
    async reset() {
      try {
        const response = await api.post('/reset-password', {
          email: this.email,
          token: this.token,
          password: this.newPass,
          password_confirmation: this.confirm
        });
        this.message = response.data.message;
        this.newPass = this.confirm = '';
        this.$router.push('/dashboard');
      } catch (e) {
        this.message = e.response?.data?.message || 'Unable to reset password';
      }
    }
  }
};
</script>
