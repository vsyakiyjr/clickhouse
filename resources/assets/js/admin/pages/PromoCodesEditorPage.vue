<template>
  <div class="promo-page">
    <v-data-table
      hide-actions
      sort-icon="arrow_drop_down"
      :headers="headers"
      :items="promoCodes"
    >
      <template v-slot:items="props">
        <td>{{ props.item.id }}</td>
        <td>
          <h3>{{ props.item.code }}</h3>
        </td>
        <td>{{ props.item.discount }}</td>
        <td>{{ props.item.valid_from }}</td>
        <td>{{ props.item.valid_to }}</td>
        <td>{{ statusDescriptions[props.item.status] }}</td>
        <td class="centered">{{ props.item.usage_count }}</td>
        <td class="centered">{{ props.item.usage_limit || 'Нет' }}</td>
        <td>{{ props.item.comment }}</td>
        <td class="justify-center layout px-0">
          <v-btn icon class="mx-0" @click.stop="openDeleteDialog(props.item)">
            <v-icon color="red">clear</v-icon>
          </v-btn>
        </td>
      </template>
      <template slot="no-data">
        <div class="text-lg-center">Промокодов нет</div>
      </template>
    </v-data-table>

    <div>
      <v-btn class="mx-2" fab dark small color="primary" @click.stop="openAddDialog">
        <v-icon dark>add</v-icon>
      </v-btn>
    </div>

    <v-dialog v-model="additionDialog" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">Новый промокод</span>
        </v-card-title>

        <v-card-text class="new-promo-dialog-content">
          <v-container class="pa-1">
            <v-form>
              <v-layout row justify-space-between>
                <v-flex xs8>
                  <v-text-field label="Промокод*"
                                v-model="newPromo.code">
                  </v-text-field>
                </v-flex>
                <v-flex xs3>
                  <v-btn color="green"
                         class="generate-code-btn"
                         @click="generatePromoCode">
                    Генерир.
                  </v-btn>
                </v-flex>
              </v-layout>
              <v-layout row justify-space-between>
                <v-flex xs6 class="pr-3">
                  <v-menu
                    ref="dateFromMenu"
                    :close-on-content-click="false"
                    v-model="dateFromMenu"
                    :nudge-right="20"
                    :return-value.sync="newPromo.valid_from"
                    lazy
                    transition="scale-transition"
                    offset-y
                    full-width
                    min-width="290px"
                  >
                    <v-text-field
                      slot="activator"
                      v-model="newPromo.valid_from"
                      label="Дата начала"
                      clear-icon="close"
                      clearable
                      readonly
                    ></v-text-field>
                    <v-date-picker v-model="newPromo.valid_from"
                                   no-title
                                   @input="$refs.dateFromMenu.save(newPromo.valid_from)">
                    </v-date-picker>
                  </v-menu>
                </v-flex>
                <v-flex xs6 class="pl-3">
                  <v-menu
                    ref="dateToMenu"
                    :close-on-content-click="false"
                    v-model="dateToMenu"
                    :nudge-right="20"
                    :return-value.sync="newPromo.valid_to"
                    lazy
                    transition="scale-transition"
                    offset-y
                    full-width
                    min-width="290px"
                  >
                    <v-text-field
                      slot="activator"
                      v-model="newPromo.valid_to"
                      label="Дата конца"
                      clear-icon="close"
                      clearable
                      readonly
                    ></v-text-field>
                    <v-date-picker v-model="newPromo.valid_to"
                                   no-title
                                   :min="newPromo.valid_from"
                                   @input="$refs.dateToMenu.save(newPromo.valid_to)">
                    </v-date-picker>
                  </v-menu>
                </v-flex>
              </v-layout>

              <v-layout row justify-space-between>
                <v-flex xs8>
                  <v-text-field label="Лимит по использованию"
                                v-model="newPromo.usage_limit">
                  </v-text-field>
                </v-flex>
              </v-layout>

              <v-layout row justify-space-between align-center>
                <v-flex xs8>
                  <v-text-field label="Скидка*"
                                v-model="newPromo.discount">
                  </v-text-field>
                </v-flex>
                <v-flex xs3>
                  <v-btn-toggle mandatory shaped v-model="newPromo.isFlatDiscount">
                    <v-btn flat>
                      <strong>%</strong>
                    </v-btn>
                    <v-btn flat>
                      <strong>BYN</strong>
                    </v-btn>
                  </v-btn-toggle>
                </v-flex>
              </v-layout>
              <v-layout row justify-space-between>
                <v-text-field label="Комментарий" v-model="newPromo.comment"></v-text-field>
              </v-layout>
            </v-form>
          </v-container>
        </v-card-text>

        <v-card-actions class="pb-3">
          <v-spacer></v-spacer>
          <v-btn @click="additionDialog = false">
            Отмена
          </v-btn>
          <v-btn color="green"
                 :disabled="!isValidNewPromo"
                 @click="addPromo">
            Сохранить
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deletionDialog" max-width="290">
      <v-card>
        <v-card-title class="headline">Уверены, что хотите удалить промокод {{ deletionPromo.code }}?</v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn olor="green darken-1"
                 flat="flat"
                 @click="deletionDialog = false">
            Отменить
          </v-btn>
          <v-btn color="green darken-1"
                 flat="flat"
                 @click="deletePromo()">
            Да
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </div>
</template>

<script>
  import axios from '@/utils/axios';

  export default {
    name: 'PromoCodesEditor',
    data(){
      return {
        promoCodes: [],
        headers: [
          {text: 'ID',          value: 'id', width: '5%'},
          {text: 'Значение',    value: 'code'},
          {text: 'Скидка',      value: 'discount'},
          {text: 'Дата с',      value: 'valid_from'},
          {text: 'Дата по',     value: 'valid_to'},
          {text: 'Статус',      value: 'status'},
          {text: 'Использован раз', value: 'usage_count'},
          {text: 'Лимит по использованию', value: 'usage_limit'},
          {text: 'Комментарий', value: 'comment', sortable: false},
        ],
        statusDescriptions: {
          active  : 'действует',
          pending : 'в ожидании',
          expired : 'истёк',
          used : 'использован',
        },

        dateFromMenu  : null,
        dateToMenu    : null,

        additionDialog: false,
        newPromo      : {},
        deletionDialog: false,
        deletionPromo : {},
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
      openDeleteDialog(promoItem){
        this.deletionPromo = promoItem;
        this.deletionDialog = true;
      },
      openAddDialog(){
        this.newPromo = {
          code          : '',
          discount      : '',
          valid_from    : '',
          valid_to      : '',
          comment       : '',
          isFlatDiscount: 0,
          usage_limit: null,
          usage_count: 0,
        };

        this.additionDialog = true;

      },
      generatePromoCode(){
        // https://gist.github.com/6174/6062387
        this.newPromo.code = (Math.random().toString(36).substring(2, 5) + Math.random().toString(36).substring(2, 5)).toUpperCase();
      },
      isValidNewPromo(){
        return this.newPromo.code && this.newPromo.discount;
      },


      getPromoCodes(){
        this.loading = true;
        axios.get('/api/promo/list')
          .then(this.thenHandler).finally(this.finallyHandler);
      },
      addPromo(){
        if (!this.isValidNewPromo()) {
        	return;
        }

        this.additionDialog = false;
        if (this.newPromo.isFlatDiscount === 0 && !this.newPromo.discount.includes('%')) {
          this.newPromo.discount += '%';
        }
        if (this.newPromo.isFlatDiscount === 1 && !this.newPromo.discount.includes('BYN')) {
          this.newPromo.discount += 'BYN';
        }

        this.loading = true;
        axios.post('/api/promo/add', this.newPromo)
          .then(this.thenHandler).finally(this.finallyHandler);
      },
      deletePromo(){
        this.deletionDialog = false;
        this.loading = true;
        axios.post('/api/promo/delete', {
          id: this.deletionPromo.id,
        })
          .then(this.thenHandler).finally(this.finallyHandler);
      },

      thenHandler(response){
        this.promoCodes = response.data;
      },
      finallyHandler(){
        this.loading = false;
      },
    },
    mounted(){
      this.getPromoCodes();
    }
  }
</script>

<style scoped>

</style>
