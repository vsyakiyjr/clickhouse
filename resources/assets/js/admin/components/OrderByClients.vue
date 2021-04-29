<template>
  <div class="admin-order order">
    <h2 class="order-title">Заказ № {{order.order_num }} (ID: {{order.id }})</h2>
    <h5>Создан {{ order.created_at }}</h5>
    <div class="client-info">
      <v-card class="pa-3 mt-3 d-flex order-client">
        <div class="mr-4">
          <v-text-field class="mb-2" :hide-details="true"  dense  label="Телефон"
                        v-model="order.phone"></v-text-field>
        </div>
        <div class="mr-4">
          <v-text-field class="mb-2" :hide-details="true"  dense  label="Имя"
                        v-model="order.name"></v-text-field>
        </div>
        <div class="mr-4">
          <v-text-field class="mb-2" :hide-details="true"  dense  label="Email"
                        v-model="order.email"></v-text-field>
        </div>
        <div class="mr-4">
          <v-icon @click="saveOrder()">
            save
          </v-icon>
          <v-icon @click.stop="deleteOrder()">
            delete_forever
          </v-icon>
        </div>
      </v-card>
    </div>
    <div class="oreder__by-client">

      <v-data-table hide-actions
                    sort-icon="arrow_drop_down"
                    :headers="headersClients"
                    :items="order.details">

        <template slot="items"
                  slot-scope="props">
          <td @click="copyAction">{{ props.item.vendor_code }}</td>
          <td>
            <v-list class='transparent'>
              <v-list-tile class='item_info'>
                <v-list-tile-content>
                  <v-list-tile-title>{{ props.item.model.name }}</v-list-tile-title>
                  <v-list-tile-sub-title>{{ props.item.model.type }}
                  </v-list-tile-sub-title>
                  <v-list-tile-sub-title>{{ props.item.model.color ? 'Цвет: ' +
                    props.item.model.color : '' }}
                  </v-list-tile-sub-title>
                  <v-list-tile-sub-title>{{ props.item.model.length ? 'Длина: ' +
                    props.item.model.length : '' }}
                  </v-list-tile-sub-title>
                </v-list-tile-content>
              </v-list-tile>
            </v-list>
          </td>
          <td>
            <v-text-field class="mb-2" :hide-details="true"  dense  v-model="props.item.qty"></v-text-field>
          </td>
          <td>{{ props.item.price }}</td>
          <td>
            <v-text-field class="mb-2" :hide-details="true"  dense  v-model="props.item.options.commission"></v-text-field>
          </td>
          <td>
            <v-text-field class="mb-2" :hide-details="true"  dense  v-model="props.item.options.fix_price"></v-text-field>
          </td>
          <td>{{ props.item.options.delivery_cost }}</td>
          <td>
            <v-text-field class="mb-2" :hide-details="true"  dense  v-model="order.exchange"></v-text-field>
          </td>
          <td>
            {{ props.item.options.price_byn }}
          </td>
          <td>
            <v-icon @click="deleteProduct(props.item.model.id,  props.item.vendor_code)">
              delete_forever
            </v-icon>
          </td>
        </template>
        <template slot="footer">
          <tr>
            <td colspan="10">
              <div class="box-artic">
                <div class="tr-text">Добавить товар к заказу:</div>
                <div class="box-artic-id">
                  <v-text-field class="mb-2" :hide-details="true" label="Код" dense  v-model="addVendorCodeField"></v-text-field>
                </div>
                <div class="box-artic-num">
                  <v-text-field class="mb-2" :hide-details="true" label="Количество" dense  v-model="countProductField"></v-text-field>
                </div>
                <div class="box-artic-btn"
                     @click="addProduct()">
                  Добавить
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="10">
              <div class="oreder__by-client-info">
                <div class="textarea-new">
                  <v-textarea v-model="order.comment"
                              placeholder="Комментарий к заказу"></v-textarea>
                </div>

                <div class="tr-text">сума заказа без комиссии:
                  {{ order.without_commission }} RUB
                </div>
                <div class="tr-text">
                  <div class="box-artic-id">
                    <v-text-field class="mb-2" label="фиксированая комиссия:" :hide-details="true"  dense  v-model="order.fix_commission"></v-text-field>
                  </div>
                  RUB
                </div>
                <div class="tr-text">Сумма с комиссией: {{ order.total }} RUB</div>
                <div class="tr-rise">
                  <v-text-field class="mb-2" label="подъем на этаж" :hide-details="true"  dense  v-model="order.floor"></v-text-field>
                </div>
                <div class="tr-sity">
                  <div class="sity">
                    <v-text-field class="mb-2" :hide-details="true"  dense  label="Город"
                                  v-model="order.delivery_city"></v-text-field>
                  </div>
                  <div class="address">
                    <v-text-field class="mb-2" :hide-details="true"  dense  label="Адрес доставки"
                                  v-model="order.delivery_address"></v-text-field>
                  </div>
                  <div class="address">
                    <v-text-field class="mb-2" :hide-details="true"  dense  label="Адрес доставки"
                                  v-model="order.delivery_house"></v-text-field>
                  </div>
                  <div class="cost">
                    <v-text-field class="mb-2" :hide-details="true" label="Стоимость доставки" dense  v-model="order.delivery_cost"></v-text-field>
                  </div>
                  <div class="tr-text">BYN</div>
                </div>
                <div class="tr-sum">
                  <strong>ВСЕГО: {{ orderPriceByn }} BYN</strong>
                </div>
                <div class="tr-sum" v-if="order.promo_discount">
                  <strong>ПРОМОКОД {{ order.promo_code }}: скидка {{ order.promo_discount }}</strong>
                  <br>
                  <strong>ВСЕГО с промокодом: {{ order.total_with_promo }} BYN</strong>
                </div>
              </div>
            </td>
          </tr>
        </template>
      </v-data-table>
    </div>
  </div>
</template>

<script>
  import {copyToClipboard} from "../utils/copyToClipboard";

  export default {
    name: "OrderByClients",
    props: ['order'],
    data(){
      return {
        addVendorCodeField: '',
        countProductField: 0,

        headersClients: [
          {text: "Артикул", value: "vendor_code"},
          {text: "Описание", value: "name"},
          {text: "Кол-во", value: "quantity"},
          {text: "Цена, RUB", value: "price"},
          {text: "Комиссия %", value: "commission"},
          {text: "Фикс. цена, BYN", value: "fix"},
          {text: "Ст-ть доставки, RUB", value: "product_delivery_price"},
          {text: "Курс", value: "exchange"},
          {text: "Ст-ть, BYN", value: "price_BYN"},
          {text: "Действие над товаром", value: "", sortable: false},
        ],
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
      orderPriceByn(){
        let priceByn = this.order.price_byn.total;

        if (priceByn) {
          return priceByn;
        }

        this.order.details.forEach(function (p) {
          priceByn += p.options.price_BYN;
        });

        return Math.ceil(priceByn);
      },
    },
    methods: {
      saveOrder(){
        this.loading = true;
        axios.post('/api/orders/update', this.order).then(response => {
          if (response.data.success) {
            this.loadOrders();
          }
        }).finally(() => {
          this.loading = false;
        })
      },

      deleteOrder(){
        this.$emit('remove');
      },

      addProduct() {
        this.loading = true;
        axios.post('/api/orders/add-product', {
          order_id    : this.order.id,
          quantity    : this.countProductField,
          product_code: this.addVendorCodeField
        }).then(response => {
          this.loading = false;
          if (response.data.success) {
            this.$emit('saved')
          }
        }).finally(() => {
          this.loading = false;
        });
      },
      deleteProduct(product_id, product_code) {
        this.loading = true;
        axios.post('/api/orders/delete-product', {
          order_id: this.order.id,
          product_id,
          product_code
        }).then((response) => {
          if (response.data.success) {
            this.$emit('saved')
          }
        }).finally(() => {
          this.loading = false;
        })
      },
      copyAction(e) {
        copyToClipboard(e.target)
      },
    },
  }
</script>

<style scoped>

</style>
