<template>
  <div>
    <div class="text-center pt-2">
      <v-pagination v-model="pagination.page"
                    :length="pagination.totalPages"
      ></v-pagination>
    </div>
    <v-data-table hide-actions
                  sort-icon="arrow_drop_down"
                  :loading="!orders.length"
                  :headers="headersVendors"
                  :items-per-page="pagination.itemsPerPage"
                  :page.sync="pagination.page"
                  :items="orders">
      <template v-slot:items="props">
        <td @click='copyAction'>{{ props.item.product.vendor_code }}</td>
        <td>
          <v-list class='transparent'>
            <v-list-tile class='item_info'>
              <v-list-tile-content>
                <v-list-tile-title>
                  {{ props.item.product.name }}
                </v-list-tile-title>
                <v-list-tile-sub-title>{{ props.item.product.type }}</v-list-tile-sub-title>
                <v-list-tile-sub-title>
                  {{ props.item.product.color ? `Цвет: ${props.item.product.color}` : '' }}
                </v-list-tile-sub-title>
                <v-list-tile-sub-title>
                  {{ props.item.product.length ? `Длина: ${props.item.product.length}` : '' }}
                </v-list-tile-sub-title>
              </v-list-tile-content>
            </v-list-tile>
          </v-list>
        </td>
        <td>{{ props.item.count }}</td>
        <td>{{ props.item.product.price_final }}</td>
      </template>
    </v-data-table>
    <div class="text-center pt-2">
      <v-pagination v-model="pagination.page"
                    :length="pagination.totalPages"
      ></v-pagination>
    </div>
  </div>
</template>

<script>
  import axios from '@/utils/axios';
  import {copyToClipboard} from '../utils/copyToClipboard';

  export default {
    name: 'OrdersByProducts',
    props: ['date'],
    data(){
      return {
        orders: [],
        pagination: {
          page        : 1,
          totalPages  : 0,
          totalItems  : 0,
          itemsPerPage: 50,
        },

        headersVendors: [
          {text: 'Артикул',     value: 'vendor_code'},
          {text: 'Описание',    value: 'name'},
          {text: 'Кол-во',      value: 'count'},
          {text: 'Цена, руб.',  value: 'price'},
        ],
      };
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
          type: 'byProducts',
        }).then(response => {
          this.orders = response.data.orders;
          this.pagination = response.data.pagination;
        }).finally(() => {
          this.loading = false;
        })
      },
      copyAction(e){
        copyToClipboard(e.target);
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
