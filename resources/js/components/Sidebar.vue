<template>
  <aside
    class="fixed inset-y-0 left-0 w-64
           bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900
           text-slate-200 shadow-xl border-r border-white/5
           flex flex-col justify-between"
  >
    <div class="px-4 py-3 text-center border-b border-white/10">
      <h1 class="text-lg font-semibold text-cyan-300 tracking-tight">
        Inventory
      </h1>
      <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest">
        Control Center
      </p>
    </div>

    <nav class="px-2 py-2 flex-1 flex flex-col gap-1 overflow-y-auto">
      <SidebarLink to="/dashboard" label="Dashboard"><DashboardIcon /></SidebarLink>
      <SidebarLink to="/category" label="Categories"><CategoryIcon /></SidebarLink>
      <SidebarLink to="/unit" label="Units"><UnitIcon /></SidebarLink>
      <SidebarLink to="/tax" label="Taxes"><TaxIcon /></SidebarLink>
      <SidebarLink to="/product-list" label="Products"><ProductIcon /></SidebarLink>
      <SidebarLink to="/customer-list" label="Customers"><CustomerIcon /></SidebarLink>
      <SidebarLink to="/vendor-list" label="Vendors"><VendorIcon /></SidebarLink>
      <SidebarLink to="/stock" label="Stock"><StockIcon /></SidebarLink>
      <SidebarLink to="/quotations-list" label="Quotations"><QuotationIcon /></SidebarLink>
      <SidebarLink to="/purchase-bills" label="Purchase Bills"><PurchaseIcon /></SidebarLink>
      <SidebarLink to="/invoices" label="Invoices"><InvoiceIcon /></SidebarLink>
      <SidebarLink to="/credit-notes" label="Credit Notes"><CreditNoteIcon /></SidebarLink>
      <SidebarLink to="/payments" label="Payments"><PaymentIcon /></SidebarLink>
      <SidebarLink to="/outstanding" label="Outstanding"><OutstandingIcon /></SidebarLink>
      <SidebarLink to="/settings" label="Settings"><SettingIcon /></SidebarLink>
    </nav>

    <div class="px-3 py-3 flex flex-col gap-2 border-t border-white/10">
      <div class="flex items-center gap-2">
        <img
          src="https://i.pravatar.cc/40"
          class="w-9 h-9 rounded-full ring-2 ring-cyan-400/70"
        />
        <div class="flex-1 text-left">
          <p class="text-sm font-semibold text-white">Parishek Rawal</p>
          <p class="text-xs text-slate-400">Admin</p>
        </div>
      </div>
      <button
        @click="$router.push('/change-password')"
        class="w-full py-1 text-xs text-cyan-300/80 hover:text-cyan-300 transition text-left pl-2 cursor-pointer"
      >
        Change Password
      </button>
      <button
        @click="$emit('logout')"
        class="w-full py-2 rounded-lg bg-cyan-500/90 hover:bg-cyan-500 text-white text-sm font-medium shadow transition cursor-pointer"
      >
        Sign Out
      </button>
    </div>
  </aside>
</template>

<script>
import SidebarLink from './SidebarLink.vue'
import DashboardIcon from './icons/DashboardIcon.vue'
import CategoryIcon from './icons/CategoryIcon.vue'
import UnitIcon from './icons/UnitIcon.vue'
import TaxIcon from './icons/TaxIcon.vue'
import ProductIcon from './icons/ProductIcon.vue'
import CustomerIcon from './icons/CustomerIcon.vue'
import VendorIcon from './icons/VendorIcon.vue'
import StockIcon from './icons/StockIcon.vue'
import QuotationIcon from './icons/QuotationIcon.vue'
import PurchaseIcon from './icons/PurchaseIcon.vue'
import InvoiceIcon from './icons/InvoiceIcon.vue'
import CreditNoteIcon from './icons/CreditNoteIcon.vue'
import PaymentIcon from './icons/PaymentIcon.vue'
import OutstandingIcon from './icons/OutstandingIcon.vue'
import SettingIcon from './icons/SettingIcon.vue'

export default {
  name: 'Sidebar',
  components: {
    SidebarLink,
    DashboardIcon,
    CategoryIcon,
    UnitIcon,
    TaxIcon,
    ProductIcon,
    CustomerIcon,
    VendorIcon,
    StockIcon,
    QuotationIcon,
    PurchaseIcon,
    InvoiceIcon,
    CreditNoteIcon,
    PaymentIcon,
    OutstandingIcon,
    SettingIcon,
  },
}
</script>

<style scoped>
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background-color: white;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background-color: #f0f0f0;
}

* {
  scrollbar-width: thin;
  scrollbar-color: white transparent;
}
</style>