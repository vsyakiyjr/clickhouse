<template>
  <div>
    <v-card class="pa-3 mt-3 ">
      <v-text-field
        v-model="search"
        :append-outer-icon="'search'"
        filled
        clear-icon="close"
        clearable
        label="Поиск"
        type="text"
        @keyup.enter="loadOrders"
        @click:append-outer="loadOrders"
      ></v-text-field>
    </v-card>

    <div class="text-center pt-2">
      <v-pagination v-model="pagination.page"
                    :length="pagination.totalPages"
      ></v-pagination>
    </div>
    <order-by-clients v-for="order in orders"
                      :key="order.id"
                      :order="order"
                      @saved="loadOrders"
                      @remove="showDeleteDialog(order.id)"
    ></order-by-clients>
    <div class="text-center pt-2">
      <v-pagination v-model="pagination.page"
                    :length="pagination.totalPages"
      ></v-pagination>
    </div>

    <v-dialog v-model="dialog" max-width="290">
      <v-card>
        <v-card-title class="headline">Уверены, что хотите удалить заказ?</v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn olor="green darken-1"
                 flat="flat"
                 @click="dialog = false">
            Отменить
          </v-btn>
          <v-btn color="green darken-1"
                 flat="flat"
                 @click="deleteOrder(dialogOrderId)">
            Да
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
  import axios from '@/utils/axios'
  import OrderByClients from './OrderByClients';

  export default {
    name: "OrdersByClients",
    props: ['date'],
    components: {OrderByClients},
    data(){
      return {
        dialog: false,
        dialogOrderId: 0,
        search: '',

        orders: [],
        pagination: {
          page        : 1,
          totalPages  : 0,
          totalItems  : 0,
          itemsPerPage: 5,
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
    },
    methods: {
      loadOrders(){
        this.loading = true;
        this.orders = [];
        axios.post('/api/orders/' + this.date, {
          pagination: this.pagination,
          search: this.search,
          type: 'byClients',
        }).then(response => {
          this.orders = response.data.orders;
          this.pagination = response.data.pagination;
        }).finally(() => {
          this.loading = false;
        })
      },

      showDeleteDialog(order_id){
        this.dialogOrderId = order_id;
        this.dialog = true;
      },
      deleteOrder(order_id) {
        this.loading = true;
        axios.post('/api/orders/delete', {order_id}).then(response => {
          if (response.data.success) {
            this.loadOrders();
          }
        }).finally(() => {
          this.dialogOrderId = 0;
          this.loading = false;
          this.dialog = false;
        })
      },
    },
    watch: {
      date(){
        this.pagination.page = 1;
        this.loadOrders();
      },
      'pagination.page'(){
        this.loadOrders();
      },
    },
    mounted(){
      this.loadOrders();
    },
  }
</script>

<style scoped>

</style>
