<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar @logout="logout" />

    <div class="flex-1 p-6 overflow-y-auto ml-65">
      <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ editId ? 'Edit Product' : 'Add Product' }}</h2>

          <form @submit.prevent="saveProduct" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input v-model="form.name" type="text" placeholder="Product Name" class="input-field" required />

            <select v-model="form.category_id" class="input-field">
              <option value="">Select Category</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>

            <select v-model="form.unit_id" class="input-field">
              <option value="">Select Unit</option>
              <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
            </select>

            <select v-model="form.tax_id" class="input-field">
              <option value="">Select Tax</option>
              <option v-for="tax in taxes" :key="tax.id" :value="tax.id">{{ tax.name }} ({{ tax.percentage }}%)</option>
            </select>

            <input v-model="form.purchase_price" type="number" placeholder="Purchase Price" class="input-field"/>
            <input v-model="form.sale_price" type="number" placeholder="Sale Price" class="input-field"/>
            <input v-model="form.min_stock" type="number" placeholder="Min Stock" class="input-field"/>
            <input v-model="form.sku" type="text" placeholder="SKU" class="input-field"/>
            <input v-model="form.barcode" type="text" placeholder="Barcode" class="input-field"/>

            <div v-if="logoSrc" class="flex items-center gap-4 mb-3">
              <img :src="logoSrc" class="w-28 h-28 rounded-xl border border-cyan-300 object-contain transition-transform duration-300 hover:scale-105"/>
            </div>

            <input type="file" @change="handleImage" class="input-field"/>

            <div class="md:col-span-2 flex justify-end gap-3 mt-3">
              <button class="btn-primary">{{ editId ? 'Update' : 'Save' }}</button>
              <button v-if="editId" type="button" class="btn-secondary" @click="resetForm">Cancel</button>
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
  name: 'ProductPage',
  components: { Sidebar },
  data() {
    return {
      editId: null,
      categories: [],
      units: [],
      taxes: [],
      form: {
        name: '',
        category_id: '',
        unit_id: '',
        tax_id: '',
        purchase_price: '',
        sale_price: '',
        min_stock: '',
        sku: '',
        barcode: '',
        image: null
      },
      imagePreview: ''
    }
  },
  computed: {
    logoSrc() {
      return this.imagePreview || (this.form.image ? `${import.meta.env.VITE_BASE_URL}/storage/${this.form.image}` : '');
    }
  },
  created() {
    this.getCategories();
    this.getUnits();
    this.getTaxes();

    const id = this.$route?.params?.id;
    if (id) this.loadEdit(id);
  },
  methods: {
    handleImage(e) {
      const file = e.target.files[0];
      if(!file) return;
      this.form.image = file;
      this.imagePreview = URL.createObjectURL(file);
    },
    async getCategories() {
      try {
        const res = await api.get('/categories');
        this.categories = res.data;
      } catch(e){ console.log(e) }
    },
    async getUnits() {
      try {
        const res = await api.get('/units');
        this.units = res.data.data;
      } catch(e){ console.log(e) }
    },
    async getTaxes() {
      try {
        const res = await api.get('/taxes');
        this.taxes = res.data.data;
      } catch(e){ console.log(e) }
    },
    async saveProduct() {
      try {
        const formData = new FormData();
        for (let f in this.form) formData.append(f, this.form[f]);
        if(this.editId){
          formData.append('_method','PUT');
          await api.post(`/products/${this.editId}`, formData);
        } else {
          await api.post('/products', formData);
        }
        this.resetForm();
        this.$router.push('/product-list');
      } catch(e){ console.log(e) }
    },
    async loadEdit(id) {
      try {
        const res = await api.get(`/products/${id}`);
        const prod = res.data.data;
        this.editId = prod.id;
        for(let f in this.form) this.form[f] = prod[f];
        this.imagePreview = prod.image ? `${import.meta.env.VITE_BASE_URL}/storage/${prod.image}` : '';
      } catch(e) { console.log(e) }
    },
    resetForm() {
      this.editId = null;
      this.form = { name:'', category_id:'', unit_id:'', tax_id:'', purchase_price:'', sale_price:'', min_stock:'', sku:'', barcode:'', image:null };
      this.imagePreview = '';
      this.$router.push('/productList');
    },
    async logout() {
      try { await api.post('/logout'); localStorage.removeItem('token'); this.$router.push('/login'); } catch(e){ console.log(e) }
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
</style>
