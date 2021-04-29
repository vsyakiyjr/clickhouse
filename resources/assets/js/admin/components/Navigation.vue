<template>
  <v-navigation-drawer :value="drawer" @input="toggle" app width="200px">
    <v-toolbar flat class="transparent">
      <v-list class="pa-0">
        <v-list-tile>
          <v-list-tile-content>
            <v-avatar color="red">
              <span class="white--text headline">{{ user.name ? user.name[0] : user.email[0] }}</span>
            </v-avatar>
          </v-list-tile-content>
          <v-list-tile-content>

            <v-list-tile-title>{{ user.name ? user.name : user.email }}</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-toolbar>
    <img class="admin-logo" width="150px" src="/images/logo.png" alt="">
    <v-list dense class="pt-0">
      <v-list-tile exact to="/admin">
        <v-list-tile-content>
          <v-list-tile-title><img src="/images/calendar.png" alt="">Планирование</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
      <v-list-tile to="/admin/orders">
        <v-list-tile-content>
          <v-list-tile-title><img src="/images/edit_file.png" alt="">Заказы</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
      <v-list-tile to="/admin/catalog">
        <v-list-tile-content>
          <v-list-tile-title><img src="/images/purchase_order.png" alt="">Каталог</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
      <v-list-tile to="/admin/users">
        <v-list-tile-content>
          <v-list-tile-title><img src="/images/user.png" alt="">Клиенты</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
      <v-list-tile to="/admin/information">
        <v-list-tile-content>
          <v-list-tile-title><img src="/images/user.png" alt="">Информация</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
      <v-list-tile to="/admin/promo">
        <v-list-tile-content>
          <v-list-tile-title><img src="/images/discount.png" alt="" class="pr-1">Промокоды</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
      <div role="listitem">
        <a href="/cms"
           target="_blank"
           class="v-list__tile v-list__tile--link theme--light">
          <div class="v-list__tile__content">
            <div class="v-list__tile__title">
              Управление блогом
            </div>
          </div>
        </a>
      </div>
      <v-list-tile to="/admin/mainpage">
        <v-list-tile-content>
          <v-list-tile-title>Главная</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
  import {mapGetters} from "vuex"

  export default {
    name: 'Navigation',
    computed: {
      ...mapGetters(['drawer', 'user'])
    },
    methods: {
      toggle(value) {
        if (this.drawer != value) {
          this.$store.dispatch('toggleDrawer')
        }
      }
    }
  }
</script>
