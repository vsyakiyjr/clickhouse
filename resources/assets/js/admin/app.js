import Vue from 'vue'
import VueAxios from 'vue-axios'
import Vuetify from 'vuetify'

import App from './App.vue'
window.$ = window.jQuery = require('jquery');
import axios from './utils/axios'
import router from './router'
import store from './store'

window.axios = axios

import 'vuetify/dist/vuetify.min.css' // Ensure you are using css-loader

Vue.use(VueAxios, axios)
Vue.use(Vuetify)

window.Vue = new Vue({
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App/>'
})
