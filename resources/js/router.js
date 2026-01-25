import { createRouter, createWebHistory } from 'vue-router';
import Login from './views/Login.vue';
import ChangePassword from './views/ChangePassword.vue';
import ForgotPassword from './views/ForgotPassword.vue';
import Settings from './views/Settings.vue';
import Category from './views/Category.vue';
import Unit from './views/Unit.vue';
import Tax from './views/Tax.vue';
import Product from './views/Product.vue';
import ProductPage from './views/ProductPage.vue';
import CustomerList from './views/CustomerList.vue';
import CustomerPage from './views/CustomerPage.vue';
import VendorList from './views/VendorList.vue';
import Stock from './views/Stock.vue';
import axios from 'axios';
import Quotations from './views/Quotations.vue';
import QuotationList from './views/QuotationList.vue';
import PurchaseBillCreate from './views/PurchaseBillCreate.vue';
import PurchaseBillList from './views/PurchaseBillList.vue';
import CreditNoteCreate from './views/CreditNoteCreate.vue';
import CreditNoteList from './views/CreditNoteList.vue';
import InvoiceList from './views/InvoiceList.vue';
import PaymentList from './views/PaymentList.vue';
import OutstandingList from './views/OutstandingList.vue';
import ReportsDashboard from './views/ReportsDashboard.vue';

const routes=[
    {path:'/',redirect:'/login'},
    {path:'/login',component:Login},
    {path:'/dashboard',component:ReportsDashboard,meta:{requiresAuth:true}},
    {path:'/change-password',component:ChangePassword,meta:{requiresAuth:true}},
    {path:'/forgot-password',component:ForgotPassword},
    {path:'/reset-password/:token?',component:()=>import('./views/ResetPassword.vue')},
    {path:'/settings',component:Settings,meta:{requiresAuth:true}},
    {path:'/category',component:Category,meta:{requiresAuth:true}},
    {path:'/unit',component:Unit,meta:{requiresAuth:true}},
    {path:'/tax',component:Tax,meta:{requiresAuth:true}},
    {path:'/product-list',component:Product,meta:{requiresAuth:true}},
    {path:'/product-page/:id?',name:'product-page',component:ProductPage,meta:{requiresAuth:true}},
    {path:'/customer-list',name:'customer-list',component:CustomerList,meta:{requiresAuth:true}},
    {path:'/customer-page/:id?',name:'customer-page',component:CustomerPage,meta:{requiresAuth:true}},
    {path:'/vendor-list',component:VendorList,meta:{requiresAuth:true}},
    {path:'/vendor-page/:id?',name:'vendor-page',component:()=>import('./views/VendorForm.vue'),meta:{requiresAuth:true}},
    {path:'/stock',component:Stock,meta:{requiresAuth:true}},
    {path:'/quotations-create',component:Quotations,meta:{requiresAuth:true}},
    {path:'/quotations-list',component:QuotationList,meta:{requiresAuth:true}},
    {path:'/quotations/:id/edit',component:()=>import('./views/Quotations.vue'),meta:{requiresAuth:true}},
    {path:'/quotations/:id',component:()=>import('./views/QuotationView.vue'),meta:{requiresAuth:true}},
    {path:'/invoices/:id',component:()=> import('./views/InvoiceView.vue'),meta:{requiresAuth:true}},
    {path:'/invoices',component:InvoiceList,meta:{requiresAuth:true}},
    {path:'/purchase-bill-create',component:PurchaseBillCreate,meta:{requiresAuth:true}},
    {path:'/purchase-bills',component:PurchaseBillList,meta:{requiresAuth:true}},
    {path:'/purchase-bills/:id',component:()=>import('./views/PurchaseBillView.vue'),meta:{requiresAuth:true}},
    {path:'/credit-notes',component:CreditNoteList,meta:{requiresAuth:true}},
    {path:'/credit-notes-create',component:CreditNoteCreate,meta:{requiresAuth:true}},
    {path:'/credit-notes/:id',component:()=>import('./views/CreditNoteView.vue'),meta:{requiresAuth:true}},
    {path:'/sales-payment/:id',component:()=>import('./views/SalesPayment.vue'),meta:{requiresAuth:true}},
    {path:'/purchase-payment/:id',component:()=>import('./views/PurchasePayment.vue'),meta:{requiresAuth:true}},
    {path:'/payments',component:PaymentList,meta:{requiresAuth:true}},
    {path:'/outstanding',component:OutstandingList,meta:{requiresAuth:true}},
];

const router=createRouter({
    history:createWebHistory(),
    routes
});

router.beforeEach(async(to,from,next)=>{
    const token=localStorage.getItem('token');

    if(to.meta.requiresAuth){
        if(!token){
           return next('/login');
        }
        try{
            const apiUrl=import.meta.env.VITE_API_URL;
            const res=await axios.get(`${apiUrl}/auth-check`,{
                headers:{
                    Authorization:`Bearer ${token}`
                }
            });
            console.log(res);
            next();
        }catch(e){
            console.log(e);
            localStorage.removeItem('token');
            next('/login');
        }
    }
    else if(to.path==='/login' && token){
        next('/dashboard');
    }else{
        next();
    }
});

export default router;