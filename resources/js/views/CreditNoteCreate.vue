<template>
  <div class="min-h-screen flex bg-gray-50">
    <Sidebar @logout="logout" />

    <main class="flex-1 ml-64 p-8">
      <div class="max-w-4xl mx-auto">

        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-800">Create Credit Note</h1>
          <p class="text-sm text-gray-500">
            Issue a return against a sales invoice or purchase bill
          </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6 space-y-6">

          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
              Credit Note Type
            </label>
            <select v-model="form.type" class="input w-full">
              <option value="">Select Type</option>
              <option value="sales">Sales Return</option>
              <option value="purchase">Purchase Return</option>
            </select>
          </div>

          <div v-if="form.type === 'sales'">
            <label class="block text-sm font-medium text-gray-600 mb-1">
              Invoice
            </label>
            <select v-model="form.invoice_id" class="input w-full">
              <option value="">Select Invoice</option>
              <option
                v-for="inv in invoices"
                :key="inv.id"
                :value="inv.id"
              >
                {{ inv.invoice_no }}
              </option>
            </select>
          </div>

          <div v-if="form.type === 'purchase'">
            <label class="block text-sm font-medium text-gray-600 mb-1">
              Purchase Bill
            </label>
            <select v-model="form.purchase_bill_id" class="input w-full">
              <option value="">Select Purchase Bill</option>
              <option
                v-for="pb in purchaseBills"
                :key="pb.id"
                :value="pb.id"
              >
                {{ pb.bill_no }}
              </option>
            </select>
          </div>

          <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-3">
              Return Items
            </h3>

            <div
              v-for="(item, i) in form.items"
              :key="i"
              class="grid grid-cols-3 gap-3 mb-3"
            >
              <select v-model="item.product_id" class="input col-span-2">
                <option value="">Select Product</option>
                <option
                  v-for="p in form.products"
                  :key="p.id"
                  :value="p.id"
                >
                  {{ p.name }}
                </option>
              </select>
              <span v-if="item.product_id">{{ item.maxQuantity }}</span>
              <input
                type="number"
                v-model="item.quantity"
                placeholder="Qty"
                class="input"
              />
              <span v-if="this.error" class="text-xs text-red-500 mt-1">
                {{ this.error }}
              </span>
            </div>

            <button
              type="button"
              @click="addItem"
              class="text-sm text-blue-600 btn btn-primary hover:underline cursor-pointer"
            >
              + Add another item
            </button>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t">
            <button
              class="px-5 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition cursor-pointer"
              @click="$router.back()"
            >
              Cancel
            </button>

            <button
              class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition cursor-pointer"
              @click="saveCreditNote"
            >
              Save Credit Note
            </button>
          </div>

        </div>
      </div>
    </main>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import api from '../axios'

export default {
  name: 'CreditNoteCreate',
  components: { Sidebar },
  data() {
    return {
      form: {
        type: '',
        invoice_id: '',
        purchase_bill_id: '',
        items: [],
        products: [],
      },
      invoices: [],
      purchaseBills: [],
      error:'',
    }
  },
  created() {
    this.form.items.push({ product_id: '', quantity: '',maxQuantity:0 })
  },
  watch: {
    'form.type'(val) {
      if (val === 'sales') this.getInvoices()
      if (val === 'purchase') this.getPurchaseBill()
    },
    'form.invoice_id'(val) {
      if (val) this.getinvoiceProducts(val)
    },
    'form.purchase_bill_id'(val) {
      if (val) this.getPurchaseBillProducts(val)
    },
    'form.items': {
      handler(items) {
        (async () => {
          for (const item of items) {
            if (item.product_id) {
              console.log(this.form.products);
              const billId = this.form.type === 'sales'
                ? this.form.invoice_id
                : this.form.purchase_bill_id;

              const quantity = await this.getProductQuantity(item.product_id, this.form.type, billId);

              item.maxQuantity=quantity;
            }
          }
        })();
      },
      deep: true
    },    
  },
  methods: {
    async getInvoices() {
      const res = await api.get('/invoices')
      this.invoices = res.data
    },
    async getPurchaseBill() {
      const res = await api.get('/purchase-bills')
      this.purchaseBills = res.data.data
    },
    async getinvoiceProducts(id) {
      const res = await api.get(`/invoices/${id}`)
      this.form.products = res.data.items.map(i => i.product)
    },
    async getPurchaseBillProducts(id) {
      const res = await api.get(`/purchase-bills/${id}`)
      this.form.products = res.data.data.items.map(i => i.product)
    },
    async getProductQuantity(id,type,billId){
      const res=await api.get(`/products/${id}/${type}/${billId}`);
      return res.data.quantity;
    },
    addItem() {
      this.form.items.push({ product_id: '', quantity: '' })
      console.log(this.form.items);
    },
    async saveCreditNote() {
      if (this.form.type === 'sales' && !this.form.invoice_id) {
        return alert('Please select an invoice')
      }
      if (this.form.type === 'purchase' && !this.form.purchase_bill_id) {
        return alert('Please select a purchase bill')
      }
      try{
        await api.post('/credit-notes', this.form);
        this.$router.push('/credit-notes')
      }catch(e){
        this.error=e.response.data.error;
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
