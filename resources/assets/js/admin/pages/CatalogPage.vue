<template>
  <div class="catalog-page">
    <!-- модальное редактирование категории -->
    <v-card>
      <v-dialog v-model="dialog" max-width="500px">
        <v-card>
          <v-card-text>
            <v-container grid-list-md>
              <v-layout wrap>
                <v-flex xs12 sm6 md6>
                  <v-text-field :hide-details="true"  dense
                    :error-messages="error('vendor_code')"
                    v-model="editedItem.vendor_code"
                    label="Код"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-select
                    :items="categories"
                    v-model="editedItem.category_id"
                    item-value="id"
                    item-text="name"
                    label="Категория"
                  ></v-select>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-select
                    :items="sub_categories"
                    v-model="editedItem.subcategory_id"
                    item-value="id"
                    item-text="name"
                    label="Субкатегория"
                  ></v-select>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-text-field :hide-details="true"  dense
                    :error-messages="error('name')"
                    v-model="editedItem.name"
                    label="Название"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-text-field :hide-details="true"  dense
                    :error-messages="error('photo')"
                    v-model="editedItem.photo"
                    label="Ссылка на фото"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-text-field :hide-details="true"  dense
                    :error-messages="error('type')"
                    v-model="editedItem.type"
                    label="Тип"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-text-field :hide-details="true"  dense
                    :error-messages="error('color')"
                    v-model="editedItem.color"
                    label="Цвет"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-text-field :hide-details="true"  dense
                    :error-messages="error('price')"
                    v-model="editedItem.price"
                    label="Цена"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-text-field :hide-details="true"  dense
                    :error-messages="error('family_price')"
                    v-model="editedItem.family_price"
                    label="Акция"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-text-field :hide-details="true"  dense
                    :error-messages="error('length')"
                    v-model="editedItem.length"
                    label="Длина"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-text-field :hide-details="true"  dense
                    :error-messages="error('discount')"
                    v-model="editedItem.discount"
                    label="Фикс. Наценка"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-checkbox v-model="editedItem.visible" label="Статус"></v-checkbox>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-text-field :hide-details="true"  dense
                    :error-messages="error('quantity')"
                    v-model="editedItem.quantity"
                    label="Кол-во"
                  ></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md6>
                  <v-checkbox v-model="editedItem.quantity_controll" label="Учитавать кол-во"></v-checkbox>
                </v-flex>
                <!-- <v-flex xs12 sm6 md6>
								   <v-checkbox v-model="editedItem.fixed_price" label="Фиксированная цена"></v-checkbox>
                </v-flex>-->
              </v-layout>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click.native="closeDialog">Отмена</v-btn>
            <v-btn color="blue darken-1" flat @click.native="saveProductDialog">Сохранить</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-card-title>
        <span class="headline">Каталог</span>
      </v-card-title>
    </v-card>

    <!-- редактирование наценки -->
    <v-card>
      <div class="top_catalog">
        <div class="top_catalog-range">
          <table class="top_catalog_table">
            <thead>
              <tr>
                <td>Диапазон, RUB</td>
                <td>Тип наценки</td>
                <td>Наценка</td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(charge, index) in charges">
                <td class="align-center pa-2 table_row">
                  <div class="d-flex charge-range">
                    <v-text-field :hide-details="true"
                                  dense
                                  :error-messages="error(charge, 'total_from')"
                                  label="От"
                                  v-model="charge.total_from"
                    ></v-text-field>
                    <v-spacer class="ml-3 mr-3 pt-3">-</v-spacer>
                    <v-text-field :hide-details="true"
                                  dense
                                  :error-messages="error(charge, 'total_to')"
                                  label="До"
                                  v-model="charge.total_to"
                    ></v-text-field>
                  </div>
                </td>
                <td class="table_row">
                  <v-radio-group v-model="charge.type">
                    <v-radio
                      :error-messages="error(charge, 'type')"
                      v-for="type in chargeTypes"
                      :key="type.type"
                      :label="`${type.label}`"
                      :value="type.type"
                    ></v-radio>
                  </v-radio-group>
                </td>
                <td class="table_row">
                  <v-text-field :hide-details="true"  dense  :error-messages="error(charge, 'amount')" v-model="charge.amount"></v-text-field>
                </td>
                <td>
                  <v-btn small
                         flat
                         style="min-width: auto;"
                         @click="removeCharge(index)">
                    <i class="material-icons">delete_forever</i>
                  </v-btn>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="top_catalog-range-button">
            <v-btn @click="addCharge()" color="primary">Добавить диапазон</v-btn>
            <v-btn @click="saveCharges()" color="success">Сохранить</v-btn>
            <!--<h3 class="headline mb-0 mt-2">Курс валют:</h3>-->
            <!--<v-text-field :hide-details="true"  dense  class='mt-3' label="Курс валют" id = 'exchange-rates'></v-text-field>-->
            <!--<v-btn @click='changeExchangeRates()' color="success">Сохранить курс валют</v-btn>-->
          </div>
        </div>
        <div class="ml-2 top_catalog-course">
          <div class="top_catalog-course-box">
            <h3 class="headline">Курс</h3>
            <input
              id="exchange-rates"
              type="number"
              step="0.0001"
              class="form-control new-input-50"
              v-model="exchange"
            />
            <v-btn @click="changeExchangeRates()" color="success">Сохранить</v-btn>
          </div>
        </div>
      </div>
      <!--<v-flex d-flex justify-space-between>-->
      <!--<v-text-field :hide-details="true"  dense  prepend-icon="fab fa-instagram" v-model="contacts.social.instagram.link" class='mt-3' label="Курс валют"></v-text-field>-->
      <!--<v-spacer></v-spacer>-->
      <!--<v-switch v-model="contacts.social.instagram.status"></v-switch>-->
      <!--</v-flex>-->
    </v-card>

    <!-- Добавление товаро в хедер или на главную -->
    <v-card class="mb-3">
      <h3 class="headline">Найденные товары для добавления в хедер/главная</h3>
      <v-layout>
        <v-text-field :hide-details="true"  dense
          @change="searchProduct()"
          class="mt-2"
          label="Поиск"
          v-model="searchProductField"
        ></v-text-field>
      </v-layout>
      <v-data-table
        class="box-shadow"
        :loading="loading"
        :headers="headers_search"
        :items="productsShowForMain"
        :rows-per-page-items="[10, 15, 20]"
      >
        <template slot="items" slot-scope="props">
          <td>{{ props.item.vendor_code }}</td>
          <td>
            <img :src="props.item.photo" style="width: 70px" />
          </td>
          <td>
            <v-list class="transparent">
              <v-list-tile class="item_info">
                <v-list-tile-content>
                  <v-list-tile-title>
                    <a target="_blank" :href="'/catalog/product/' + props.item.vendor_code">
                      {{ props.item.name
                      }}
                    </a>
                  </v-list-tile-title>
                  <v-list-tile-sub-title>Тип: {{ props.item.type }}</v-list-tile-sub-title>
                  <v-list-tile-sub-title>Цвет: {{ props.item.color }}</v-list-tile-sub-title>
                  <v-list-tile-sub-title>Длина: {{ props.item.length }}</v-list-tile-sub-title>
                </v-list-tile-content>
              </v-list-tile>
            </v-list>
          </td>
          <td>
            <v-flex d-flex justify-end>
              <v-icon @click="addToShowProduct(props.item,'header')">add</v-icon>
            </v-flex>
          </td>
          <td>
            <v-flex d-flex justify-end>
              <v-icon @click="addToShowProduct(props.item,'main')">add</v-icon>
            </v-flex>
          </td>
        </template>
      </v-data-table>
    </v-card>

    <!-- Товары в хедере -->
    <h3 class="headline">Товары в хедере:</h3>
    <v-data-table
      class="box-shadow"
      :loading="loading"
      :headers="headers_show"
      :items="products_top"
      v-bind:pagination.sync="paginationSettings"
      :disable-pagination="true"
      :rows-per-page-items="[1000]"
      items-per-page="1000"
    >
      <template slot="items" slot-scope="props">
        <td>{{ props.item.vendor_code }}</td>
        <td>
          <img :src="props.item.photo" style="width: 70px" loading="lazy"/>
        </td>
        <td>
          <v-list class="transparent">
            <v-list-tile class="item_info">
              <v-list-tile-content>
                <v-list-tile-title>
                  <a
                    target="_blank"
                    :href="'/catalog/product/' + props.item.vendor_code"
                  >{{ props.item.name }}</a>
                </v-list-tile-title>
                <v-list-tile-sub-title>Тип: {{ props.item.type }}</v-list-tile-sub-title>
                <v-list-tile-sub-title>Цвет: {{ props.item.color }}</v-list-tile-sub-title>
                <v-list-tile-sub-title>Длина: {{ props.item.length }}</v-list-tile-sub-title>
              </v-list-tile-content>
            </v-list-tile>
          </v-list>
        </td>
        <td>{{props.item.show_order}}</td>
        <td>
          <v-flex d-flex justify-end>
            <v-icon
              @click="moveItem(props.item, props.index, 'header', 'down', 'top')"
            >fas fa-arrow-down</v-icon>
            <v-icon @click="removeFromShowProduct(props.item,'header')">delete_forever</v-icon>
            <v-icon
              @click="moveItem(props.item, props.index, 'header', 'up', 'top')"
            >fas fa-arrow-up</v-icon>
          </v-flex>
        </td>
      </template>
    </v-data-table>

    <!-- Товары на главной -->
    <div class="mb-5">
      <h3 class="headline">Товары на главной странице</h3>
      <v-data-table
        class="box-shadow"
        :loading="loading"
        :headers="headers_show"
        v-bind:pagination.sync="paginationSettings"
        :disable-pagination="true"
        :items="products_main"
        :rows-per-page-items="[1000]"
        items-per-page="1000"
      >
        <template slot="items" slot-scope="props">
          <td>{{ props.item.vendor_code }}</td>
          <td>
            <img :src="props.item.photo" style="width: 70px" loading="lazy" />
          </td>
          <td>
            <v-list class="transparent">
              <v-list-tile class="item_info">
                <v-list-tile-content>
                  <v-list-tile-title>
                    <a target="_blank" :href="'/catalog/product/' + props.item.vendor_code">
                      {{ props.item.name
                      }}
                    </a>
                  </v-list-tile-title>
                  <v-list-tile-sub-title>Тип: {{ props.item.type }}</v-list-tile-sub-title>
                  <v-list-tile-sub-title>Цвет: {{ props.item.color }}</v-list-tile-sub-title>
                  <v-list-tile-sub-title>Длина: {{ props.item.length }}</v-list-tile-sub-title>
                </v-list-tile-content>
              </v-list-tile>
            </v-list>
          </td>
          <td>{{props.item.show_order}}</td>
          <td>
            <v-flex d-flex justify-end>
              <v-icon
                @click="moveItem(props.item, props.index, 'main', 'down', 'main')"
              >fas fa-arrow-down</v-icon>
              <v-icon @click="removeFromShowProduct(props.item,'main')">delete_forever</v-icon>
              <v-icon
                @click="moveItem(props.item, props.index, 'main', 'up', 'main')"
              >fas fa-arrow-up</v-icon>
            </v-flex>
          </td>
        </template>
      </v-data-table>
    </div>

    <!-- наценка для товара -->
    <div class="box-shadow">
      <v-card class="pa-3 table-main-top">
        <div class="search-and-parser">
          <v-flex d-flex justify-end>
            <v-select
                label="Категория"
              class="mr-2"
              v-model="category_id"
              item-value="id"
              item-text="name"
              :items="[{id:0, name: 'Все'}].concat(categories)"
            ></v-select>
          </v-flex>
          <v-flex d-flex justify-end>
            <v-select
              class="mr-2"
              label="Субкатегория"
              v-model="sub_category_id"
              item-value="id"
              @change="refresh()"
              item-text="name"
              :items="[{id:0, name: 'Все'}].concat(sub_categories)"
            >
              <template
                slot="selection"
                slot-scope="data"
              >{{ data.item.name }}: {{ data.item.products_count }}</template>
              <template
                slot="item"
                slot-scope="data"
              >{{ data.item.name }}: {{ data.item.products_count }}</template>
            </v-select>
          </v-flex>
          <v-text-field :hide-details="true"  dense  @change="refresh()" class="mt-2" label="Поиск" v-model="searchInCategory"></v-text-field>
          <v-spacer></v-spacer>
          <v-flex class="button-block parser-buttons" d-flex justify-end>
            <v-btn :disabled="loading" @click="startParser(false)" color="warning">Запуск парсера</v-btn>
            <v-btn :disabled="loading" @click="startParserCategories" color="cyan">Запуск парсера категорий</v-btn>
            <v-btn :disabled="loading" @click="startParser(true)" color="danger">Удаление старых товаров</v-btn>
            <v-btn :disabled="loading" @click="resetDB" color="red">Сброс БД</v-btn>
            <v-btn :disabled="loading" @click="stopParser" color="danger">Остановить парсер</v-btn>
            <img v-if="parsing" v-cloak src="/images/preloader.gif" class="hint-block" />
            <!--<div>{{data_progress}}</div>-->
          </v-flex>
        </div>
      </v-card>
      <div v-for="(progress, index) in data_progress" :key="index">{{progress}}</div>
      <!--<div class="progress" v-if="parsing==true">-->
      <!--<div class="progress-bar" :style="'height:30px;background:blue;width:' + (data_progress/17)*100 + '%'"></div>-->
      <!--</div>-->

      <!--      <h3 class="headline">Все товары</h3>-->
      <v-data-table
        :loading="loading"
        :headers="headers"
        :pagination.sync="pagination"
        :total-items="total"
        :items="products"
        :rows-per-page-items="[10, 50, 100]"
      >
        <template slot="items" slot-scope="props">
          <td v-if="category_id || sub_category_id" class="centered">
            <input
              class="checkbox"
              type="checkbox"
              :checked=" ! isVisibleInCategory(props.item.id)"
              @change="onToggleVisibleInCategory(props.item.id, ! isVisibleInCategory(props.item.id))"
            />
          </td>
          <td>{{ props.item.vendor_code }}</td>
          <td>
            <img :src="props.item.photo" style="width: 70px" loading="lazy"/>
          </td>
          <td>
            <v-list class="transparent">
              <v-list-tile class="item_info">
                <v-list-tile-content>
                  <v-list-tile-title>
                    <a target="_blank" :href="'/catalog/product/' + props.item.vendor_code">
                      {{ props.item.name
                      }}
                    </a>
                  </v-list-tile-title>
                  <v-list-tile-sub-title>Тип: {{ props.item.type }}</v-list-tile-sub-title>
                  <v-list-tile-sub-title>Цвет: {{ props.item.color }}</v-list-tile-sub-title>
                  <v-list-tile-sub-title>Длина: {{ props.item.length }}</v-list-tile-sub-title>
                </v-list-tile-content>
              </v-list-tile>
            </v-list>
          </td>
          <td style="min-width: 200px" class="text-xs-center">
            <v-flex d-flex justify-space-between>
              <v-text-field :hide-details="true"  dense
                v-if="props.item.quantity_controll"
                class="mr-2"
                label="Количество"
                :error-messages="error(props.item, 'quantity')"
                v-model="props.item.quantity"
              ></v-text-field>
              <v-text-field :hide-details="true"  dense
                label="Наценка"
                :error-messages="error(props.item, 'discount')"
                @input="changeDiscount(props.item, $event)"
                :value="props.item.discount"
              ></v-text-field>
              <v-btn
                v-if="props.item.discount"
                @click="clearDiscount(props.item)"
                class="sm-btn-my"
              >
                <i class="material-icons">cancel</i>
              </v-btn>
            </v-flex>
          </td>
          <td class="text-xs-center">
            {{ props.item.family_price ? props.item.family_price :
            props.item.price }} / {{ props.item.price_final }}
          </td>
          <!--<td>
						<v-checkbox v-model="props.item.fixed_price"></v-checkbox>
          </td>-->
          <td>
            <v-checkbox v-model="props.item.visible"></v-checkbox>
          </td>
          <td>
            <v-text-field :hide-details="true"  dense
              :error-messages="error(props.item.priority, 'priority')"
              v-model="props.item.priority"
            ></v-text-field>
          </td>
          <td>
            <v-flex d-flex justify-end>
              <v-icon @click="saveProduct(props.index)">save</v-icon>
              <v-icon @click="openDialog(props.item)">edit</v-icon>
              <v-icon @click="removeItem(props.item)">delete_forever</v-icon>
            </v-flex>
          </td>
        </template>
      </v-data-table>
    </div>

    <!-- наценка для категории -->
    <h3 class="headline">Категории</h3>
    <v-data-table
      class="box-shadow"
      :loading="loading"
      :headers="headers_cat"
      :items="categories"
      :rows-per-page-items="[300]"
    >
      <template slot="items" slot-scope="props">
        <td>
          <v-list class="transparent">
            <v-list-tile class="item_info">
              <v-list-tile-content>
                <v-list-tile-title>{{ props.item.name }}</v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>
          </v-list>
        </td>
        <td>
          <v-checkbox v-model="props.item.visible"></v-checkbox>
        </td>
        <td>
          <v-flex d-flex justify-end>
            <v-icon @click="saveCategory(props.item.id,props.item.visible)">save</v-icon>
          </v-flex>
        </td>
      </template>
    </v-data-table>

    <!-- наценка для категории -->
    <h3 class="headline">Субкатегории</h3>
    <v-flex d-flex justify-end>
      <v-select
          label="Категория"
          class="mr-2"
          v-model="category_id"
          item-value="id"
          item-text="name"
          :items="[{id:0, name: 'Все'}].concat(categories)"
      ></v-select>
    </v-flex>
    <v-data-table
      class="box-shadow"
      :loading="loading"
      :headers="headers_subcat"
      :items="subcategoriesByCategory"
      :rows-per-page-items="[300]"
    >
      <template slot="items" slot-scope="props">
        <td>
          <v-list class="transparent">
            <v-list-tile class="item_info">
              <v-list-tile-content>
                <v-list-tile-title>{{ props.item.name }}</v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>
          </v-list>
        </td>
        <td>
          <v-list class="transparent">
            <v-list-tile class="item_info">
              <v-list-tile-content>
                <v-list-tile-title>{{ props.item.category.name }}</v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>
          </v-list>
        </td>
        <td>
          <v-checkbox v-model="props.item.visible"></v-checkbox>
        </td>
        <td>
          <v-flex d-flex justify-end>
            <v-icon @click="saveSubCategories(props.item.id,props.item.visible)">save</v-icon>
          </v-flex>
        </td>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import axios from "@/utils/axios";
import * as mixins from "@/mixins";

export default {
  name: "CatalogPage",

  mixins: [mixins.table, mixins.dialog],

  mounted() {
    // this.loadDiscounts();
    this.loadCharges();
    this.loadCategories();
    this.loadSubCategories();
    this.loadExchanges();
    this.loadVisibles();

    // set headers
    this.setHeader();
  },
  data() {
    return {
      visibles: {},
      category_id: 0,
      sub_category_id: 0,
      errors: false,
      id_stop: null,
      loading: true,
      parsing: false,
      total: 0,
      // discounts: [],
      charges: [],
      categories: [],
      sub_categories: [],
      products: [],
      productsShowForMain: [],
      products_top: [],
      products_main: [],
      discount: "",
      chargeTypes: [
        { type: "fixed", label: "Фикс" },
        { type: "percentage", label: "%" }
      ],
      fixed_price: "",
      exchange: 0.0,
      searchInCategory: "",
      searchProductField: "",
      paginationSettings: {
        sortBy: "show_order",
        descending: false,
        rowsPerPage: -1
      },
      defaultItem: {
        category_id: 0,
        vendor_code: "",
        type: "",
        name: "",
        quantity: 0,
        quantity_controll: 0,
        fixed_price: 0,
        discount: 0,
        visible: 0,
        length: 0,
        color: ""
      },
      headers: [],
      headers_show: [
        { text: "Артикул", sortable: true, value: "vendor_code" },
        { text: "Фото", value: "photo", sortable: false },
        { text: "Описание", sortable: true, value: "description" },
        { text: "Порядок", sortable: true, value: "show_order" },
        {
          text: "Действие",
          align: "center",
          value: "delete_forever",
          sortable: true
        }
      ],
      headers_search: [
        { text: "Артикул", sortable: true, value: "vendor_code" },
        { text: "Фото", value: "photo", sortable: false },
        { text: "Описание", sortable: true, value: "description" },
        {
          text: "Добавить в хедер",
          align: "center",
          value: "delete_forever",
          sortable: true
        },
        {
          text: "Добавить на главную",
          align: "center",
          value: "delete_forever",
          sortable: true
        }
      ],
      headers_cat: [
        { text: "Название категории", sortable: false, value: "name" },
        { text: "Видимость", value: "visibility", sortable: false },
        {
          text: "Действие",
          align: "center",
          value: "delete_forever",
          sortable: false
        }
      ],
      headers_subcat: [
        { text: "Название субкатегории", sortable: true, value: "name" },
        { text: "Категория", sortable: true, value: "name" },
        { text: "Видимость", value: "visibility", sortable: false },
        {
          text: "Действие",
          align: "center",
          value: "delete_forever",
          sortable: false
        }
      ],
      data_from_server: "",
      data_progress: 0
    };
  },
  watch: {
    category_id() {
      this.refresh();
      let category = this.categories.find(item => item.id == this.category_id);
      this.discount = category ? category.discount : 0;

      this.setHeader();
    },
    sub_category_id() {
      this.setHeader();
    }
  },
  methods: {
    isVisibleInCategory(product_id) {
      if (this.visibles) {
        if (this.visibles[this.category_id]) {
          if (this.visibles[this.category_id][this.sub_category_id]) {
            const currentCategory = this.visibles[this.category_id][
              this.sub_category_id
            ];
            return !currentCategory.includes(product_id);
          }
        }
      }
      return false;
    },
    onToggleVisibleInCategory(product_id, visible) {
      axios.put("/api/products/toggle-visible", {
        product_id,
        category_id: this.category_id,
        sub_category_id: this.sub_category_id,
        toggle: visible,
        _method: "put"
      });
    },
    clearDiscount(target) {
      target.discount = 0;
    },
    changeDiscount(target, value) {
      target.discount = value;
    },
    startParser(clear = false) {
      this.getstatus();
      this.parsing = true;

      axios
        .post("/parser/start", {
          category_id: this.category_id,
          sub_category_id: this.sub_category_id,
          clear: clear
        })
        .then(responce => {
          alert("Парсинг запущен!");
        })
        .catch(responce => {
          //this.parsing = false
          //clearInterval(this.id_stop)
        });
    },
    startParserCategories() {
      this.loading = true;
      axios
        .post("/parser/categories")
        .then(responce => {
          alert("Парсинг категорий завершён!");
        }).finally(() => {
            this.loading = false;
          });
    },

    stopParser() {
      axios.get("/stop-parser").then(responce => {
        this.parsing = false;
        alert("Вы остановили парсинг");
        clearInterval(this.id_stop);
      });
    },

    resetDB() {
      this.getstatus();

      axios
        .post("/admin/db/reset")
        .then(responce => {
          alert("Удаление базы даных!");
        })
        .catch(responce => {});
    },

    getstatus() {
      this.id_stop = setInterval(() => {
        axios.get("/status-parser").then(responce => {
          this.data_progress = responce.data;
          // this.data_progress = parseInt(this.data_progress);
          if (this.data_progress == "Завершено!") {
            clearInterval(this.id_stop);
          } else if (!this.data_progress) {
            clearInterval(this.id_stop);
          }

          axios.interceptors.response.use(
            function(response) {
              // Do something with response data

              return response;
            },
            function(error) {
              // Do something with response error
              return Promise.reject(error);
            }
          );
        });
      }, 10000);
    },

    saveProduct(index) {
      this.loading = true;
      this.products[index].errors = false;
      axios
        .post("/api/products", { ...this.products[index] })
        .then(response => {
          this.loading = false;
          if (!response.data.success) {
            this.products[index].errors = response.data.errors;
          } else {
            this.refresh();
          }
        });
    },

    addToShowProduct(item, place) {
      axios
        .post("/api/products/" + item.id + "/set-in-main", { place: place })
        .then(response => {
          this.refresh();
        });
    },

    removeItem(item){
      if(!window.confirm("Вы точно хотите удалить этот товар?")){
        return
      }

      axios.get('/api/products/remove/' + item.id).then(() => {
        this.refresh();
      })
    },

    removeFromShowProduct(item, place) {
      axios
        .post("/api/products/delete-from-main", {
          vendor_code: item.vendor_code,
          place: place
        })
        .then(response => {
          this.refresh();
        });
    },

    moveItem(item, currentIndex, place, direction, type) {
      let newIndex;
      let that = this;
      let collection = that["products_" + type];
      let pairedItem;
      let pairedItemIndex;
      let pairedItemNewIndex;

      if (currentIndex == 0 && direction == "top") {
        return;
      }

      if (currentIndex == collection.count && direction == "down") {
        return;
      }

      switch (direction) {
        case "up": {
          pairedItemIndex = currentIndex - 1;
          pairedItem = collection[pairedItemIndex];
          pairedItemNewIndex = pairedItemIndex + 1;
          newIndex = currentIndex - 1;
          break;
        }
        case "down": {
          pairedItemIndex = currentIndex + 1;
          pairedItem = collection[currentIndex + 1];
          pairedItemNewIndex = pairedItemIndex - 1;
          newIndex = currentIndex + 1;
          break;
        }
        default: {
          console.error("wrong new index submitted");
          return false;
        }
      }

      if (!pairedItem) {
        return;
      }

      axios
        .post("/api/products/show/order", {
          place: place,
          vendor_code: item.vendor_code,
          paired_vendor_code: pairedItem.vendor_code,
          show_order: newIndex,
          paired_show_order: pairedItemNewIndex
        })
        .then(response => {
          if (type == "top") {
            that.refresh_top();
          }

          if (type == "main") {
            that.refresh_main();
          }
        });
    },

    saveCategory(id, visible) {
      this.loading = true;
      axios
        .post("/api/categories/change-visible", { id, visible })
        .then(response => {
          this.loading = false;
          if (!response.data.success) {
            this.categories[index].errors = response.data.errors;
          } else {
            this.refresh();
          }
        });
    },
    saveSubCategories(id, visible) {
      this.loading = true;
      axios
        .post("/api/subcategories/change-visible", { id, visible })
        .then(response => {
          this.loading = false;
          if (!response.data.success) {
            this.categories[index].errors = response.data.errors;
          } else {
            this.refresh();
          }
        });
    },

    saveProductDialog() {
      this.errors = false;

      axios.post("/api/products", { ...this.editedItem }).then(response => {
        if (response.data.success) {
          this.closeDialog();
          this.refresh();
        } else {
          this.errors = response.data.errors;
        }
      });
    },
    saveProductFixed() {
      this.loading = true;
      axios
        .get("/api/products/set/fixed", {
          params: {
            category_id: this.category_id,
            fixed_price: this.fixed_price
          }
        })
        .then(() => {
          this.refresh();
        });
    },
    saveProductDiscount() {
      this.loading = true;
      axios
        .get("/api/categories/set/discount", {
          params: {
            category_id: this.category_id,
            discount: this.discount
          }
        })
        .then(() => {
          this.refresh();
        });
    },
    loadCategories() {
      axios.get("/api/categories").then(response => {
        this.categories = response.data;
      });
    },
    loadSubCategories() {
      axios.get("/api/sub_categories").then(response => {
        this.sub_categories = response.data;
      });
    },
    addDiscount() {
      axios.get("/api/discounts/add").then(response => {
        this.discounts.push(response.data.entity);
      });
    },
    loadDiscounts() {
      axios.get("/api/discounts").then(response => {
        this.discounts = response.data;
      });
    },
    saveDiscounts() {
      axios.post("/api/discounts", this.discounts).then(response => {
        this.discounts = response.data;
      });
    },
    removeDiscount(index) {
      axios
        .get("/api/discounts/remove/" + this.discounts[index].id)
        .then(response => {
          this.discounts = this.discounts.filter((item, pos) => pos != index);
        });
    },
    addCharge() {
      axios.get("/api/charges/add").then(response => {
        this.charges.push(response.data.entity);
      });
    },
    loadCharges() {
      axios.get("/api/charges").then(response => {
        this.charges = response.data;
      });
    },
    saveCharges() {
      axios.post("/api/charges", this.charges).then(response => {
        this.charges = response.data;
      });
    },
    removeCharge(index) {
      axios
        .get("/api/charges/remove/" + this.charges[index].id)
        .then(response => {
          this.charges = this.charges.filter((item, pos) => pos != index);
        });
    },
    loadVisibles() {
      axios.get("/api/products/visibles").then(({ data }) => {
        this.visibles = data;
      });
    },
    refresh() {
      this.getstatus();
      this.loadVisibles();
      this.loading = true;
      this.refresh_top();
      this.refresh_main();
      axios
        .get("/api/products", {
          params: {
            ...this.pagination,
            category_id: this.category_id,
            sub_category_id: this.sub_category_id,
            search: this.searchInCategory
          }
        })
        .then(response => {
          this.loading = false;
          this.products = response.data.data;
          this.total = response.data.total;
        });
    },

    refresh_top() {
      this.getstatus();
      var place = "header";
      axios
        .get("/api/products/get-main", { params: { place: place } })
        .then(response => {
          this.products_top = response.data;
        });
    },

    refresh_main() {
      this.getstatus();
      var place = "main";
      axios
        .get("/api/products/get-main", { params: { place: place } })
        .then(response => {
          this.products_main = response.data;
        });
    },

    searchProduct() {
      this.getstatus();
      axios
        .get("/api/products/search", {
          params: { search: this.searchProductField }
        })
        .then(response => {
          this.productsShowForMain = response.data;
        });
    },

    changeExchangeRates() {
      this.exchange = document.getElementById("exchange-rates").value;
      axios
        .post("/exchange", {
          exchange: this.exchange
        })
        .then(function(response) {
          // handle success
        })
        .catch(function(error) {
          // handle error
        });
    },

    loadExchanges() {
      axios.get("/exchange").then(r => {
        this.exchange = r.data.exchange;
      });
    },
    error(target, field) {
      return target.errors && target.errors[field] ? target.errors[field] : [];
    },
    setHeader() {
      this.headers = [
        { text: "Артикул", sortable: true, value: "vendor_code" },
        { text: "Фото", value: "photo", sortable: false },
        { text: "Описание", sortable: true, value: "description" },
        {
          text: "Фикс. Наценка",
          align: "center",
          value: "over_price",
          sortable: true
        },
        {
          text: "Цена, руб. с наценкой и без",
          align: "center",
          value: "price",
          sortable: true
        },
        { text: "Статус", value: "visibility", sortable: true },
        {
          text: "Приоритет",
          align: "center",
          value: "priority",
          sortable: true
        },
        {
          text: "Действие",
          align: "center",
          value: "delete_forever",
          sortable: true
        }
      ];

      if (this.category_id || this.sub_category_id) {
        this.headers = [
          {
            text: "Отображение",
            value: "visibility",
            sortable: false
          },
          ...this.headers
        ];
      }
    }
  },
  computed: {
    classObject: function() {},
    subcategoriesByCategory(){
      if(!this.category_id){
        return this.sub_categories
      }

      return this.sub_categories.filter(s => s.category_id == this.category_id)
    }
  }
};
</script>

<style>
.table_row {
  box-shadow: none;
}

.top_catalog_table td {
  border: 1px solid #ccc;
  padding: 5px;
}

.top_catalog_table {
  border-spacing: 0;
}

.top_catalog_table thead td {
  text-align: center;
  font-weight: 600;
}

.item_info > .v-list__tile {
  height: auto !important;
  padding-left: 0;
}

button.sm-btn-my.v-btn {
  min-width: auto;
}

.v-progress-linear {
  height: 5px !important;
}

.v-progress-linear div {
  height: 5px !important;
}

.v-datatable__progress th .v-progress-linear {
  top: 0;
}

.hint-block {
  position: absolute;
  top: -61px;
  left: 50%;
  transform: translateX(-50%);
  width: auto;
  height: 60px;
  max-width: 100%;
}

.button-block {
  position: relative;
}

.progress {
  display: -ms-flexbox;
  display: flex;
  height: 1rem;
  overflow: hidden;
  font-size: 0.75rem;
  background-color: #e9ecef;
  border-radius: 0.25rem;
}

.progress-bar {
  display: -ms-flexbox;
  display: flex;
  -ms-flex-direction: column;
  flex-direction: column;
  -ms-flex-pack: center;
  justify-content: center;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  background-color: #007bff;
  transition: width 0.6s ease;
}

.centered {
  text-align: center;
}

.checkbox {
  width: 19px;
  height: 19px;
}
</style>
