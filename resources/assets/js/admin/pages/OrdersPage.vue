<template>
  <div class="orders-page">

    <div>

      <button @click="prev"
              :disabled="!prevAllowed"
              class='carousel-control carousel-control__prev'>
        <i class="material-icons">navigate_before</i>
      </button>
      <button @click="next"
              :disabled="!nextAllowed"
              class='carousel-control carousel-control__next'>
        <i class="material-icons">navigate_next</i>
      </button>
      <div>
        <v-card>
          <div class="flex pa-3 align-center order__heading flex-wrap">
            <span class="headline font-weight-medium">
              Рейс {{ selectedDeliveryDay.date }}
            </span>
            <span v-if=' selectedDeliveryDay.status === 1'
                  class="ml-3 grey--text subheading">
              (идет прием заказов)
            </span>
            <span v-if=' selectedDeliveryDay.status === 0'
                  class="ml-3 grey--text subheading">
              (прием заказов приостановлен)
            </span>
            <span v-if=' selectedDeliveryDay.status === 2'
                  class="ml-3 grey--text subheading">
              (выполнен)
            </span>
          </div>
        </v-card>
        <div v-if="totalInfo.cities">
          <v-card>
            <div class="pa-2 d-flex justify-space-between align-center order__total-info">
              <div class="order-total-rows">
                <div class="order-total-row">
                  <div class="label">Товаров на сумму:</div>
                  <div class="value">{{ totalInfo.product_total }} руб.</div>
                </div>
                <div class="order-total-row">
                  <div class="label">Клиентов:</div>
                  <div class="value">{{ totalInfo.clients_count }}</div>
                </div>
                <div class="order-total-row">
                  <div class="label">Доставка в города:</div>
                  <div class="value">{{ totalInfo.cities.join(', ') }}</div>
                </div>
              </div>

              <div class="buttons">
                <v-btn v-if='selectedDeliveryDay.status === 1'
                       @click='changeStatus(2)'
                       color="success">
                  Выполнен
                </v-btn>
                <v-btn v-if='selectedDeliveryDay.status !== 1'
                       @click='changeStatus(1)'
                       color="success">
                  Вернуть в работу
                </v-btn>
                <v-btn v-if='selectedDeliveryDay.status === 1'
                       @click='changeStatus(0)'
                       color="error">
                  Остановить прием заказов
                </v-btn>
              </div>
            </div>
          </v-card>
          <v-card class="order__products-sort d-flex">
            <v-select class="ma-0 pl-2 tab-selector"
                      v-model="tab"
                      :items="tabs">
            </v-select>
            <v-btn @click='sendMail'
                   class="px-4"
                   color="success">
              <span>
                <v-icon>email</v-icon>
                На почту
              </span>
            </v-btn>
          </v-card>

          <div v-if="tab === 'byProducts'">
            <orders-by-products :date="selectedDate"></orders-by-products>
          </div>
          <div v-else-if="tab === 'byClients'">
            <orders-by-clients :date="selectedDate"></orders-by-clients>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>


<script>
  import axios from '@/utils/axios'
  import OrdersByClients from '../components/OrdersByClients';
  import OrdersByProducts from '../components/OrdersByProducts';

  export default {
    name: 'OrdersPage',
    components: {
      OrdersByProducts,
      OrdersByClients,
    },
    data(){
      return {
        deliveryDays: [],
        deliveryDayIndex: 0,

        tab: 'byProducts',
        tabs: [
          {value: 'byProducts', text: 'По товарам'},
          {value: 'byClients',  text: 'По Клиентам'},
        ],

        totalInfo: {
          product_total : null,
          clients_count : null,
          cities        : null,
        },
      }
    },
    computed: {
      loading: {
        get(){
          return this.$store.getters.loading;
        },
        set(newValue){
          this.$store.commit('loading', newValue);
        },
      },
      selectedDeliveryDay(){
        return this.deliveryDays.length > 0 ? this.deliveryDays[this.deliveryDayIndex] : {};
      },
      selectedDate(){
        return this.selectedDeliveryDay.date;
      },
      nextAllowed(){
        return this.deliveryDayIndex + 1 < this.deliveryDays.length;
      },
      prevAllowed(){
        return this.deliveryDayIndex > 0;
      },
    },
    methods: {
      next(){
        if (this.nextAllowed) {
          this.deliveryDayIndex++;
        }
      },
      prev(){
        if (this.prevAllowed) {
          this.deliveryDayIndex--;
        }
      },

      loadDeliveryDays(){
        this.loading = true;
        axios.get('/api/planning/delivery').then(response => {
          this.deliveryDays = response.data.delivery;
          this.loadTotalInfo();
        }).finally(() => {
          this.loading = false;
        });
      },

      loadTotalInfo(){
        this.loading = true;
        axios.get('/api/orders/get-total-info/' + this.selectedDate).then(response => {
          this.totalInfo = response.data;
        }).finally(() => {
          this.loading = false;
        })
      },

      changeStatus(newStatus) {
        this.deliveryDays[this.deliveryDayIndex].status = newStatus;
        axios.get(`/api/planning/status/${this.selectedDeliveryDay.id}/${newStatus}`);
      },

      sendMail() {
        this.loading = true;
        axios.get('/api/orders/' + this.selectedDate + '/email').then(response => {
          alert('Email отправлен!');
        }).finally(() => {
          this.loading = false;
        });
      }

    },
    watch: {
      deliveryDayIndex(){
        this.search = '';
        this.loadTotalInfo();
      },
    },
    mounted() {
      this.loadDeliveryDays();
    }
  }
</script>

<style>
  .item_info > .v-list__tile {
    height: auto !important;
    padding-left: 0;
  }

  button.carousel-control {
    position: absolute;
    right: 25px;
    z-index: 1;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    top: 34px;
    outline: none !important;
  }

  button.carousel-control .material-icons {
    font-size: 44px;
    color: #c5c5c5;
  }

  button.carousel-control:not([disabled]):hover .material-icons {
    color: #000;
  }

  .carousel-control.carousel-control__prev {
    right: 60px;
  }
</style>
