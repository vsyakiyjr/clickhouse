import Vue from 'vue'
import Router from 'vue-router'

import PlanningPage from '@/pages/PlanningPage'
import OrdersPage from '@/pages/OrdersPage'
import UsersPage from '@/pages/UsersPage'
import ErrorPage from '@/pages/ErrorPage'
import InfoPage from '@/pages/InfoPage'
import CatalogPage from '@/pages/CatalogPage'
import PromoCodesEditor from "../pages/PromoCodesEditorPage";
import MainPage from "../pages/MainPage";

Vue.use(Router);

const routes = [{
    path: '/admin/error',
    name: 'error',
    component: ErrorPage,
  }, {
    path: '/admin',
    name: 'admin',
    component: PlanningPage,
  }, {
    path: '/admin/orders',
    name: 'orders',
    component: OrdersPage,
  }, {
    path: '/admin/users',
    name: 'users',
    component: UsersPage,
  }, {
    path: '/admin/information',
    name: 'information',
    component: InfoPage,
  }, {
    path: '/admin/catalog',
    name: 'catalog',
    component: CatalogPage,
  }, {
    path: '/admin/promo',
    name: 'promo',
    component: PromoCodesEditor,
  }, {
    path: '/admin/mainpage',
    name: 'mainpage',
    component: MainPage,
  }
];

export default new Router({ routes, mode: 'history' })
