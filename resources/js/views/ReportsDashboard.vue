<template>
  <div class="min-h-screen bg-slate-50 flex">
    <Sidebar @logout="logout" />

    <div class="flex-1 px-8 py-6 ml-65">
      <div class="max-w-7xl mx-auto space-y-6">

        <div>
          <h1 class="text-2xl font-semibold text-slate-800">
            Reports Dashboard
          </h1>
          <p class="text-sm text-slate-500">
            Real-time business analytics overview
          </p>
        </div>

        <div
          v-if="lowStock.length"
          class="rounded-xl border border-red-200 bg-red-50 px-5 py-4"
        >
          <h3 class="text-sm font-semibold text-red-600 mb-2">
            Low Stock Alerts
          </h3>
          <ul class="text-sm text-red-700 space-y-1">
            <li v-for="item in lowStock" :key="item.id">
              {{ item.name }} — {{ item.current_stock }} / {{ item.min_stock }}
            </li>
          </ul>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
          <div class="card">
            <h3 class="card-title">Stock Summary</h3>
            <div class="chart-box">
              <canvas id="stockChart"></canvas>
            </div>
          </div>

          <div class="card">
            <h3 class="card-title">Sales Report</h3>
            <div class="chart-box">
              <canvas id="salesChart"></canvas>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
          <div class="card">
            <h3 class="card-title">Purchase Report</h3>
            <div class="chart-box">
              <canvas id="purchaseChart"></canvas>
            </div>
          </div>

          <div class="card">
            <h3 class="card-title">Outstanding Balances</h3>
            <div class="chart-box">
              <canvas id="outstandingChart"></canvas>
            </div>
          </div>
        </div>

        <div class="card max-w-md">
          <h3 class="card-title">Profit & Loss</h3>

          <div class="chart-box h-56">
            <canvas id="profitChart"></canvas>
          </div>

          <div class="flex justify-between text-sm text-slate-600 mt-4">
            <span>Revenue: {{ profitLoss.revenue }}</span>
            <span>Cost: {{ profitLoss.cost }}</span>
            <span class="font-semibold text-slate-800">
              Profit: {{ profitLoss.profit }}
            </span>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import Chart from 'chart.js/auto'
import api from '../axios'

export default {
  name: 'ReportsDashboard',
  components: { Sidebar },

  data() {
    return {
      stockData: [],
      salesData: [],
      purchaseData: [],
      outstandingData: { customers: [], vendors: [] },
      profitLoss: { revenue: 0, cost: 0, profit: 0 },
      charts: {},
      lowStock: []
    }
  },

  mounted() {
    this.loadReports()
    this.getLowStock()
  },

  methods: {
    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: { ticks: { font: { size: 10 } } },
          y: { ticks: { font: { size: 10 } } }
        }
      }
    },

    async loadReports() {
      const stockRes = await api.get('/reports/stock-summary')
      this.stockData = stockRes.data.data

      this.charts.stockChart = new Chart(
        document.getElementById('stockChart'),
        {
          type: 'bar',
          data: {
            labels: this.stockData.map(p => p.name),
            datasets: [{
              data: this.stockData.map(p => p.current_stock),
              backgroundColor: 'rgba(59,130,246,0.6)'
            }]
          },
          options: this.chartOptions()
        }
      )

      const salesRes = await api.get('/reports/sales')
      this.salesData = salesRes.data.data

      this.charts.salesChart = new Chart(
        document.getElementById('salesChart'),
        {
          type: 'bar',
          data: {
            labels: this.salesData.map(c => c.name),
            datasets: [{
              data: this.salesData.map(c => c.total_sales),
              backgroundColor: 'rgba(16,185,129,0.6)'
            }]
          },
          options: this.chartOptions()
        }
      )

      const purchaseRes = await api.get('/reports/purchases')
      this.purchaseData = purchaseRes.data.data

      this.charts.purchaseChart = new Chart(
        document.getElementById('purchaseChart'),
        {
          type: 'bar',
          data: {
            labels: this.purchaseData.map(v => v.name),
            datasets: [{
              data: this.purchaseData.map(v => v.total_purchase),
              backgroundColor: 'rgba(251,146,60,0.6)'
            }]
          },
          options: this.chartOptions()
        }
      )

      const customers = await api.get('/outstanding/customers')
      const vendors = await api.get('/outstanding/vendors')

      this.outstandingData.customers = customers.data.data
      this.outstandingData.vendors = vendors.data.data

      this.charts.outstandingChart = new Chart(
        document.getElementById('outstandingChart'),
        {
          type: 'bar',
          data: {
            labels: [
              ...this.outstandingData.customers.map(c => `C-${c.name}`),
              ...this.outstandingData.vendors.map(v => `V-${v.name}`)
            ],
            datasets: [{
              data: [
                ...this.outstandingData.customers.map(c => c.outstanding),
                ...this.outstandingData.vendors.map(v => v.outstanding)
              ],
              backgroundColor: 'rgba(139,92,246,0.6)'
            }]
          },
          options: this.chartOptions()
        }
      )

      const plRes = await api.get('/reports/profit-loss')
      this.profitLoss = plRes.data.data

      this.charts.profitChart = new Chart(
        document.getElementById('profitChart'),
        {
          type: 'pie',
          data: {
            labels: ['Revenue', 'Cost', 'Profit'],
            datasets: [{
              data: [
                this.profitLoss.revenue,
                this.profitLoss.cost,
                this.profitLoss.profit
              ],
              backgroundColor: [
                'rgba(16,185,129,0.7)',
                'rgba(239,68,68,0.7)',
                'rgba(59,130,246,0.7)'
              ]
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false
          }
        }
      )
    },

    async getLowStock() {
      const res = await api.get('/low-stock')
      this.lowStock = res.data.low_stock
    },

    async logout() {
      await api.post('/logout')
      localStorage.removeItem('token')
      this.$router.push('/login')
    }
  }
}
</script>

<style scoped>
.card {
  background: #ffffff;
  border-radius: 1rem;
  border: 1px solid #e5e7eb;
  padding: 1.5rem;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

.card-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #334155;
  margin-bottom: 0.75rem;
}

.chart-box {
  position: relative;
  width: 100%;
  height: 12rem;
}
</style>
