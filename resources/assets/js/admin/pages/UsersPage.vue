<template>
  <v-card>
    <v-card-title>
      <span class="headline">Пользователи</span>
      <v-spacer></v-spacer>
      <v-text-field :hide-details="true"  dense  v-model="search" name='search' append-icon="search" label="Поиск" single-line
                    ></v-text-field>
    </v-card-title>
    <v-data-table class="box-shadow" :loading="loading" sort-icon="arrow_drop_down" :headers="headers"
                  :pagination.sync="pagination" :total-items="total" :items="users" :search="search"
                  :rows-per-page-items="[10, 15, 20, 25, 100]">
      <template slot="items" slot-scope="props">
        <td class="text-xs-center">{{ props.item.id }}</td>
        <td class="text-xs-center">{{ props.item.email }}</td>
        <td class="text-xs-center">{{ props.item.phone }}</td>
        <!--        <td class="text-xs-center">{{ props.item.name }}</td>-->
        <td class="text-xs-center">{{ props.item.delivery_address }}</td>
        <td class="text-xs-center">{{ props.item.date }}</td>
      </template>
      <v-alert slot="no-results" :value="true" color="error" icon="warning">
        По текущему запросу ничего не найдено!
      </v-alert>
    </v-data-table>
  </v-card>
</template>

<script>
    import axios from '@/utils/axios'
    import * as mixins from '@/mixins'

    export default {
        name: 'UsersPage',
        mixins: [mixins.table],
        data() {
            return {
                loading: true,
                total: 0,
                users: [],
                headers: [
                    {text: '№', align: "center", sortable: true, value: 'id'},
                    {text: 'Email', align: "center", sortable: true, value: 'email'},
                    {text: 'Телефон', align: "center", sortable: true, value: 'phone'},
                    // { text: 'Имя', align: "center", sortable: true, value: 'name' },
                    {text: 'Адрес', align: "center", sortable: true, value: 'delivery_address'},
                    {text: 'Последний рейс', align: "center", sortable: true, value: 'date'},
                ]
            }
        },
        methods: {

            refresh() {
                this.loading = true
                axios.get('/api/users', {params: {...this.pagination, filter: this.search}}).then((response) => {
                    this.users = response.data.data
                    this.total = response.data.total
                    this.loading = false
                })
            },

        }
    }
</script>
