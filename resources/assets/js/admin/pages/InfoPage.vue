<template>
  <div class="information-page">
    <v-card>
      <v-card-title>
        <span class="headline">Информация</span>
      </v-card-title>
    </v-card>
    <v-tabs class="box-shadow" color="gray" light slider-color="black">
      <v-tab v-for="(page, index) in pages" :key="index">{{ page.title }}</v-tab>
      <!--<v-tab v-for="page in pages">{{ page.title }}</v-tab>-->
      <v-tab>Контакты</v-tab>
      <v-tab>Города</v-tab>
      <v-tab-item v-for="(page, index) in pages" :key="page.id">
        <!--<v-tab-item v-for="(page, index) in pages">-->
        <v-card class='info__content'>
          <v-form>

              <span class="headline">Блоки</span>
              <v-card v-for="(block, blockIndex) in page.blocks" :key="'block'+block.id">
                  <v-card>
                      <v-textarea :error-messages="error(block, 'title')" label="Название блока" v-model="block.title"
                                  hint="Вводите название"></v-textarea>
                  </v-card>
                  <v-card>
                      <v-textarea :error-messages="error(block, 'text')" label="Текст блока" v-model="block.text"
                                  hint="Вводите текст"></v-textarea>
                  </v-card>
                  <v-flex d-flex justify-end>
                      <v-btn @click='deleteBlock(index, blockIndex)' color="error">Удалить блок</v-btn>
                  </v-flex>
              </v-card>
              <v-flex d-flex justify-end>
                  <v-btn @click='addBlock(index)' color="success">Добавить блок</v-btn>
              </v-flex>

            <v-textarea :error-messages="error(page, 'text')" label="Текст страницы" v-model="page.text"
                        hint="Вводите текст"></v-textarea>
<!--            <input type="checkbox" id="visible" v-model="page.visible">-->
            <v-checkbox label="Отображать" v-model="page.visible" id="visible"></v-checkbox>
<!--            <label for="visible">Отображать</label>-->
            <v-flex d-flex justify-end>
              <v-btn @click='savePage(index)' color="error">Сохранить</v-btn>
            </v-flex>
          </v-form>
        </v-card>
      </v-tab-item>
      <v-tab-item class="contacts-tab">
        <v-form class="d-flex contacts-tab-form">
          <div class="pa-2 contacts-col">
            <div class="contacts-info">
              <div class="contacts-phone mt-3">
  <!--              <h3 class="headline mb-0 mt-2">Телефоны:</h3>-->
                <v-icon class="m-icon">phone</v-icon>
                <v-text-field :hide-details="true"  dense  :key="index + 'phone'" v-for="(phone, index, key) in contacts.phones"
                              @click:append-outer="removeEntity('phones', index)" append-outer-icon="delete_forever"
                               class='new-input-175'
                              v-model="contacts.phones[index]"></v-text-field>
                <v-btn @click="addEntity('phones')" class="add-btn" >
                  еще
                </v-btn>
              </div>
              <div class="contacts-viber mt-3">
                <v-icon class="m-icon">fab fa-viber</v-icon>
  <!--              <h3 class="headline mb-0 mt-2">Viber:</h3>-->
                <v-text-field :hide-details="true"  dense  :key="index + 'viber'" v-for="(viber, index, key) in contacts.vibers"
                              @click:append-outer="removeEntity('vibers', index)" append-outer-icon="delete_forever"
                              v-model="contacts.vibers[index]" class='new-input-175'>
                </v-text-field>
                <v-btn @click="addEntity('vibers')"  class="add-btn">
                  еще
                </v-btn>
              </div>
              <div class="contacts-email mt-3">
                <v-icon class="m-icon">mail_outline</v-icon>
  <!--              <h3 class="headline mb-0 mt-2">E-mail:</h3>-->
                <v-text-field :hide-details="true"  dense  :key="index + 'email'" v-for="(email, index, key) in contacts.emails"
                              @click:append-outer="removeEntity('emails', index)" append-outer-icon="delete_forever"
                              class='new-input-175' v-model="contacts.emails[index]"
                              ></v-text-field>
                <v-btn @click="addEntity('emails')" class="add-btn">
                  еще
                </v-btn>
              </div>
              <div class="contacts-social mt-3">

                <v-flex d-flex justify-space-between>
                  <v-text-field :hide-details="true"  dense  prepend-icon="fab fa-facebook-f" v-model="contacts.social.facebook.link" class='new-input-230'
                                ></v-text-field>
  <!--                <v-spacer></v-spacer>-->
                  <v-switch v-model="contacts.social.facebook.status" class="mrg-top-0"></v-switch>
                </v-flex>

                <v-flex d-flex justify-space-between>
                  <v-text-field :hide-details="true"  dense  prepend-icon="fab fa-telegram-plane" v-model="contacts.social.telegram.link" class='new-input-230'></v-text-field>
  <!--                <v-spacer></v-spacer>-->
                  <v-switch v-model="contacts.social.telegram.status" class="mrg-top-0"></v-switch>
                </v-flex>

                <v-flex d-flex justify-space-between>
                  <v-text-field :hide-details="true"  dense  prepend-icon="fab fa-vk" v-model="contacts.social.vkontakte.link" class='new-input-230'></v-text-field>
  <!--                <v-spacer></v-spacer>-->
                  <v-switch v-model="contacts.social.vkontakte.status" class="mrg-top-0"></v-switch>
                </v-flex>

                <v-flex d-flex justify-space-between>
                  <v-text-field :hide-details="true"  dense  prepend-icon="fab fa-instagram" v-model="contacts.social.instagram.link" class='new-input-230'></v-text-field>
  <!--                <v-spacer></v-spacer>-->
                  <v-switch v-model="contacts.social.instagram.status" class="mrg-top-0"></v-switch>
                </v-flex>
              </div>
            </div>
          </div>

          <div class="pa-2 mt-4 contacts-col">

              <div class="contacts-bate_time">
                <div class="contacts-bate_time-name">Рабочее время:</div>
                <v-text-field :hide-details="true"  dense  v-model='contacts.work_time' placeholder="Введите рабочее время"></v-text-field>
              </div>

            <div class="contacts-pickup">
              <div class="contacts-pickup-name">Пункт самовывоза:</div>
              <v-text-field :hide-details="true"  dense   v-model='contacts.address'
                             placeholder="Введите адрес пункта самовывоза:" ></v-text-field>
            </div>

            <v-flex d-flex justify-space-between>
              <v-text-field :hide-details="true"  dense  class='mt-4' v-model='contacts.map' label="Карта:"></v-text-field>
            </v-flex>

            <v-flex class='mt-5' d-flex justify-end>
              <v-btn @click='saveSettings' color="success">Сохранить</v-btn>
            </v-flex>
          </div>
        </v-form>
      </v-tab-item>
      <v-tab-item>
        <v-form class='d-flex'>
          <v-flex xs6 class='pa-2'>
<!--            <h3 class="headline mb-0 mt-2">Города:</h3>-->
            <div class="city-table">
              <div class="box-table">
                <div class="box-tr">
                  <div class="box-td">Город</div>
                  <div class="box-td">Стоимость</div>
                  <div class="box-td">Бесплатно при сумме заказа BYN</div>
                  <div class="box-td"> </div>
                </div>
                <v-card class="box-tr city_item" v-for="(city, index) in cities" :key="index">
                  <div class="box-td">
                    <v-text-field :hide-details="true"  dense  class="input-text-center" v-model="city.name"></v-text-field>
                  </div>
                  <div class="box-td">
                    <v-text-field :hide-details="true"  dense  class="new-input-50" v-model="city.price"></v-text-field>
                  </div>
                  <div class="box-td">
                    <v-text-field :hide-details="true"  dense  v-model="city.freeShippingFrom"></v-text-field>
                  </div>
                  <div class="box-td">
                    <v-icon @click="removeCity(index)">delete_forever</v-icon>
                  </div>
                </v-card>
              </div>
            </div>

            <div class="add-city" @click="addCity('cities')">
              Добавить город
            </div>
          </v-flex>
          <v-flex class='mt-5' d-flex justify-end>
            <v-btn @click='saveCities' color="success">Сохранить</v-btn>
          </v-flex>
        </v-form>
      </v-tab-item>
    </v-tabs>
  </div>
</template>

<script>
    import axios from '@/utils/axios'

    export default {
        data() {
            return {
                pages: [],
                cities: [],
                contacts: {
                    map: '',
                    phones: [],
                    vibers: [],
                    emails: [],
                    social: {
                        facebook: {
                            link: '',
                            status: false
                        },
                        telegram: {
                            link: '',
                            status: false
                        },
                        vkontakte: {
                            link: '',
                            status: false
                        },
                        instagram: {
                            link: '',
                            status: false
                        },
                    },
                    address: '',
                    work_time: '',
                },
                e1: null,
                e2: null,
                e3: null,
                e4: null,
                e5: null,
                e6: null,
                e7: null,
                e8: null,
                e9: null,
                e10: null,
                e11: null,
                e12: null,
                e13: null,
                e14: null,
                e15: null,
                e16: null,
            }
        },
        methods: {
            addEntity(entity) {
                this.contacts[entity].push('')
            },
            addCity(entity) {
                this.cities.push({name: '', price: '', freeShippingFrom: ''})
            },
            removeEntity(entity, index) {
                this.contacts[entity] = this.contacts[entity].filter((item, pos) => pos != index)
            },
            removeCity(index) {
                this.cities = this.cities.filter((item, pos) => pos != index)
            },
            saveSettings() {
                axios.post('/api/settings', {key: 'contacts', value: JSON.stringify(this.contacts)})
            },
            saveCities() {
                axios.post('/api/settings', {key: 'cities', value: JSON.stringify(this.cities)})
            },
            loadSettings() {
                axios.get('/api/settings').then((response) => {
                    let contacts = response.data.find((item) => item.key == 'contacts')
                    if (contacts) {
                        this.contacts = JSON.parse(contacts.value)
                    }

                    let cities = response.data.find((item) => item.key == 'cities')
                    if (cities) {
                        this.cities = JSON.parse(cities.value)
                    }
                })
            },
            loadPages() {
                axios.get('/api/pages').then((response) => {
                    this.pages = response.data
                })
            },
            addBlock(index) {
                this.pages[index].blocks.push({
                    id: null,
                    title: '',
                    text: ''
                })
            },
            deleteBlock(index, blockIndex) {
                this.pages[index].blocks.splice(blockIndex, 1)
            },
            savePage(index) {
                this.pages[index].errors = false
                axios.post('/api/pages', {...this.pages[index]}).then((response) => {
                    if (!response.data.success) {
                        this.pages[index].errors = response.data.errors
                    }
                })
            },
            error(target, field) {
                return (target.errors && target.errors[field]) ? target.errors[field] : []
            }
        },
        mounted() {
            this.loadPages()
            this.loadSettings()
        }
    }
</script>
