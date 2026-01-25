<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg p-6">

          <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ editId ? 'Edit Customer' : 'Add Customer' }}</h2>

          <form @submit.prevent="saveCustomer" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input v-model="form.name" placeholder="Name" class="input-field" required />
            <input v-model="form.phone" placeholder="Phone" class="input-field" />
            <input v-model="form.gst_number" placeholder="GST Number" class="input-field" />
            <input v-model="form.opening_balance" placeholder="Opening Balance" class="input-field" />
            <textarea v-model="form.address" placeholder="Address" class="input-field md:col-span-2"></textarea>

            <div class="md:col-span-2 flex justify-end gap-3 mt-4">
              <button class="btn-primary">{{ editId ? 'Update' : 'Save' }}</button>
              <router-link v-if="editId" to="/customer-list" class="btn-secondary">Cancel</router-link>
              <router-link v-else to="/customer-list" class="btn-secondary">Back</router-link>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue';
import api from '../axios';

export default {
  name: 'CustomerPage',
  components: { Sidebar },
  data() {
    return {
      editId: null,
      form: {
        name: '',
        phone: '',
        gst_number: '',
        opening_balance: '',
        address: ''
      }
    }
  },
  created() {
    const id = this.$route?.params?.id;
    if(id) this.loadEdit(id);
  },
  methods: {
    async saveCustomer() {
      try {
        if(this.editId){
          await api.put(`/customers/${this.editId}`, this.form);
        } else {
          await api.post('/customers', this.form);
        }
        this.$router.push('/customer-list');
      } catch(e) { console.log(e); }
    },
    async loadEdit(id) {
      try {
        const res = await api.get(`/customers/${id}`);
        this.editId = res.data.id;
        this.form = {
          name: res.data.name || '',
          phone: res.data.phone || '',
          gst_number: res.data.gst_number || '',
          opening_balance: res.data.opening_balance || '',
          address: res.data.address || ''
        };
      } catch(e) { console.log(e); }
    },
    async logout() {
      try { 
        await api.post('/logout');
        localStorage.removeItem('token');
        this.$router.push('/login');
      } catch(e) { console.log(e); }
    }
  }
}
</script>

<style>
.input-field {
  border: 1px solid #ccc;
  border-radius: 12px;
  padding: 0.75rem 1rem;
  outline: none;
  transition: all 0.2s;
}
.input-field:focus {
  border-color: #22d3ee;
  box-shadow: 0 0 0 2px rgba(34,211,238,0.3);
}
.btn-primary {
  background-color: #06b6d4;
  color: #fff;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  transition: all 0.2s;
}
.btn-primary:hover { background-color: #0891b2; }
.btn-secondary {
  background-color: #e5e7eb;
  color: #374151;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  transition: all 0.2s;
}
.btn-secondary:hover { background-color: #d1d5db; }
</style>
