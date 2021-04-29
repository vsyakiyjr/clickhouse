<template>
  <div class="cart-page">
    <div class="container">
      <div class="empty-cart" v-if="cart.itemsCount == 0">
        <h2 class="empty-cart__title">Корзина пуста</h2>
        <p class="empty-cart__subtitle">
          Найдите товар <span class="mobile-br"><br /></span> через
          <a href="#" @click="searchFocus">поиск</a> или
          <a href="/catalog">каталог</a> <span class="mobile-br"><br /></span> и
          добавьте его в корзину
        </p>
        <div class="empty-cart__image">
          <img src="images/redesign/empty-cart.svg" alt="empty-cart" />
        </div>
      </div>

      <div class="checkout_page_wrap" v-if="cart.itemsCount > 0">
        <h1 class="desktop-title">Товары в вашей корзине</h1>
        <h1 class="mobile-title">
          Корзина
          <p
            @click="
              clearAll();
              visible = !visible;
            "
            class="clear-head"
          >
            Очистить корзину
          </p>
        </h1>
        <div class="cart-page__items">
          <div
            class="cart-page__item desktop"
            v-for="item in sortedItems"
            :key="item.id"
            :vendor_code="item.id"
          >
            <div class="cart-page__img">
              <a
                class="ord_product_name text-uppercase"
                style="color: black"
                :href="'/catalog/product/' + item.vendor_code"
              >
                <img
                  :src="item.model.photo"
                  :alt="item.model.name"
                  class="orderImg"
                />
              </a>
            </div>
            <div class="cart-page__inner">
              <div class="cart-page__row">
                <p class="gray-text">Артикул {{ getArticule(item) }}</p>
                <p
                  @click="
                    removeItem(item.model.id);
                    visible = !visible;
                  "
                  class="gray-text cart-page__delete-item"
                >
                  Удалить
                </p>
              </div>
              <div class="cart-page__row">
                <h2 class="carg-page__title">
                  <a
                    style="color: inherit; text-decoration: none"
                    :href="'/catalog/product/' + item.vendor_code"
                    >{{ item.model.name }}</a
                  >
                </h2>
                <div class="cart-page__qty">
                  <span
                    class="font-quantity text-gray"
                    v-on:click="changeQty(item.model.id, item.qty - 1)"
                    type="minus"
                    ><svg
                      width="25"
                      height="28"
                      viewBox="0 0 10 10"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <circle
                        cx="5"
                        cy="5"
                        r="4.6"
                        stroke="#828282"
                        stroke-width="0.8"
                      />
                      <path
                        d="M8 5L2 5"
                        stroke="#828282"
                        stroke-width="0.8"
                      /></svg
                  ></span>
                  <input
                    type="text"
                    class="quantityInput m-1"
                    @blur="changeQty(item.model.id, item.qty)"
                    v-model="item.qty"
                  />
                  <span
                    class="font-quantity text-gray"
                    v-on:click="changeQty(item.model.id, item.qty - 1 + 2)"
                    type="plus"
                  >
                    <svg
                      width="25"
                      height="28"
                      viewBox="0 0 10 10"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <circle
                        cx="5"
                        cy="5"
                        r="4.6"
                        stroke="#828282"
                        stroke-width="0.8"
                      />
                      <path d="M5 2V8" stroke="#828282" stroke-width="0.8" />
                      <path d="M8 5L2 5" stroke="#828282" stroke-width="0.8" />
                    </svg>
                  </span>
                </div>
              </div>
              <div class="cart-page__col">
                <div class="cart-page__about">{{ item.model.type }}</div>
                <div class="cart-page__wrapper">
                  <div class="cart-page__price">
                    <h5>Товар стоит:</h5>
                    <div>
                      <p class="price-amount RUB">
                        {{ item.price }} <span>RUB</span>
                      </p>
                      <p class="price-amount BYN">
                        {{ item.price_byn }} <span>BYN</span>
                      </p>
                    </div>
                  </div>
                  <div class="cart-page__gray">
                    <template v-if="item.fixed_price">
                      <h5>Фикс. цена:</h5>
                      <h5 v-if="item.fixed_price">
                        {{ item.price_order_byn }} BYN
                      </h5>
                    </template>
                    <template v-else>
                      <h5>Комиссия:</h5>
                      <h5 style="display: flex">
                        {{ cart.charge.amount }}
                        {{ cart.charge.type == "percentage" ? "%" : "RUB" }}
                        <img
                          style="margin-left: 4px"
                          class="icon-i"
                          src="images/redesign/icon-i.svg"
                          alt="icon-i"
                        />
                      </h5>
                    </template>
                  </div>
                </div>
              </div>
              <div style="margin: 0" class="cart-page__row">
                <h4>Цена с доставкой:</h4>
                <div class="cart-page__total" v-if="item.fixed_price">
                  {{ (item.price_order_byn_with_qty || 0).toFixed(2) }}
                  <span>BYN</span>
                </div>
                <div class="cart-page__total" v-if="!item.fixed_price">
                  <div class="price-amount RUB">
                    {{ (item.price_order_with_qty || 0).toFixed(2) }}
                    <span>RUB</span>
                  </div>
                  <div class="price-amount BYN">
                    {{ (item.price_order_byn_with_qty || 0).toFixed(2) }}
                    <span>BYN</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-if="mobile" class="mobile-wrapper">
            <div
              class="cart-page__item mobile"
              v-for="item in sortedItems"
              :key="item.id"
              :vendor_code="item.id"
            >
              <div class="cart-page__row">
                <p
                  @click="
                    removeItem(item.model.id);
                    visible = !visible;
                  "
                  class="gray-text cart-page__delete-item"
                >
                  Удалить
                </p>
                <p class="gray-text">Артикул {{ getArticule(item) }}</p>
              </div>
              <div class="cart-page__mobile-wrap">
                <div class="cart-page__img">
                  <a
                    class="ord_product_name text-uppercase"
                    style="color: black"
                    :href="'/catalog/product/' + item.vendor_code"
                  >
                    <img
                      :src="item.model.photo"
                      :alt="item.model.name"
                      class="orderImg"
                    />
                  </a>
                </div>
                <div class="cart-page__inner">
                  <div class="cart-page__row">
                    <h2 class="carg-page__title">
                      <a
                        style="color: inherit; text-decoration: none"
                        :href="'/catalog/product/' + item.vendor_code"
                        >{{ item.model.name }}</a
                      >
                    </h2>
                    <div class="cart-page__qty">
                      <span
                        class="font-quantity text-gray"
                        v-on:click="changeQty(item.model.id, item.qty - 1)"
                        type="minus"
                        ><svg
                          width="25"
                          height="28"
                          viewBox="0 0 10 10"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <circle
                            cx="5"
                            cy="5"
                            r="4.6"
                            stroke="#828282"
                            stroke-width="0.8"
                          />
                          <path
                            d="M8 5L2 5"
                            stroke="#828282"
                            stroke-width="0.8"
                          /></svg
                      ></span>
                      <input
                        type="text"
                        class="quantityInput m-1"
                        @blur="changeQty(item.model.id, item.qty)"
                        v-model="item.qty"
                      />
                      <span
                        class="font-quantity text-gray"
                        v-on:click="changeQty(item.model.id, item.qty - 1 + 2)"
                        type="plus"
                      >
                        <svg
                          width="25"
                          height="28"
                          viewBox="0 0 10 10"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <circle
                            cx="5"
                            cy="5"
                            r="4.6"
                            stroke="#828282"
                            stroke-width="0.8"
                          />
                          <path
                            d="M5 2V8"
                            stroke="#828282"
                            stroke-width="0.8"
                          />
                          <path
                            d="M8 5L2 5"
                            stroke="#828282"
                            stroke-width="0.8"
                          />
                        </svg>
                      </span>
                    </div>
                  </div>
                  <div class="cart-page__col">
                    <div class="cart-page__about">{{ item.model.type }}</div>
                    <div class="cart-page__wrapper">
                      <div class="cart-page__price">
                        <h5>Товар стоит:</h5>
                        <div>
                          <p class="price-amount RUB">
                            {{ item.price }} <span>RUB</span>
                          </p>
                          <p class="price-amount BYN">
                            {{ item.price_byn }} <span>BYN</span>
                          </p>
                        </div>
                      </div>
                      <div class="cart-page__gray">
                        <h5 v-if="cart.charge.type == 'percentage'">
                          Комиссия:
                        </h5>
                        <h5 v-else>Доставка:</h5>
                        <h5>
                          {{ cart.charge.amount }}
                          {{ cart.charge.type == "percentage" ? "%" : "RUB" }}
                          <img
                            style="margin-left: 4px"
                            class="icon-i"
                            src="images/redesign/icon-i.svg"
                            alt="icon-i"
                          />
                        </h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div style="margin: 0" class="cart-page__row">
                <h4>Цена с доставкой:</h4>
                <div class="cart-page__total" v-if="item.fixed_price">
                  {{ (item.price_order_byn_with_qty || 0).toFixed(2) }}
                  <span>BYN</span>
                </div>
                <div class="cart-page__total" v-if="!item.fixed_price">
                  <div class="price-amount RUB">
                    {{ (item.price_order_with_qty || 0).toFixed(2) }}
                    <span>RUB</span>
                  </div>
                  <div class="price-amount BYN">
                    {{ (item.price_order_byn_with_qty || 0).toFixed(2) }}
                    <span>BYN</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="cart-page__bottom">
          <div class="back-button">
            <div>
              <svg
                width="6"
                height="9"
                viewBox="0 0 6 9"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M2.99945 1.49927L1.50028 2.99963L5.36619e-08 4.5L1.50028 6.00037L2.99945 7.49963L4.49973 9L6 7.49963L4.49973 6.00037L2.99945 4.5L4.49973 2.99963L6 1.49927L4.49973 1.00733e-06L2.99945 1.49927Z"
                  fill="#828282"
                />
              </svg>
              <p><a href="/">Вернуться к покупкам</a></p>
            </div>
          </div>
          <div class="cart-page__bottom-wrap">
            <div class="cart-page__bottom-row">
              <div class="gray-text">Комиссия за доставку:</div>
              <div class="gray-text">
                {{ cart.charge.amount }}
                {{ cart.charge.type == "percentage" ? "%" : "RUB" }}
              </div>
            </div>
            <div
              class="cart-page__bottom-row"
              v-if="cart.charge.amount > 0 && cart.nextCharge"
            >
              <div class="gray-text">
                Комиссия упадет до {{ cart.nextCharge.amount }}%, добавьте товар
                на:
              </div>
              <div class="gray-text price-amount RUB">
                {{ (cart.rubBeforeNextCharge || 0).toFixed(2) }} RUB
              </div>
              <div class="gray-text price-amount BYN">
                {{ (cart.bynBeforeNextCharge || 0).toFixed(2) }} BYN
              </div>
            </div>
            <div class="cart-page__bottom-row">
              <div class="cart-page__total-price">Сумма заказа:</div>
              <div class="cart-page__total-price">
                {{ (cart.totalPriceByn || 0).toFixed(2) }}
                <span>BYN</span>
              </div>
              <!-- <div class="cart-page__total-price price-amount RUB">
                {{ (cart.totalPriceRub || 0).toFixed(2) }} <span>RUB</span>
              </div>
              <div class="cart-page__total-price price-amount BYN">
                {{ (cart.totalPriceByn || 0).toFixed(2) }} <span>BYN</span>
              </div> -->
              <div
                @click="
                  orderModal = !orderModal;
                  setBodyClass();
                "
                class="cart-order__button mobile-none"
              >
                Заказать
              </div>
            </div>
            <div
              @click="
                orderModal = !orderModal;
                setBodyClass();
              "
              style="
                position: relative;
                right: unset;
                left: unset;
                top: unset;
                bottom: unset;
              "
              class="cart-order__button cart-mobile__button"
            >
              Заказать
            </div>
            <div class="cart-page__bottom-row mobile-none">
              <div
                @click="
                  clearAll();
                  visible = !visible;
                "
                class="cart-clear"
              >
                Очистить корзину
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- модальное подтверждения удаления -->
    <div
      v-if="visible"
      class="modal-dialog"
      id="removeItem"
      tabindex="-1"
      role="dialog"
      aria-labelledby="removeItemLabel"
      aria-hidden="true"
    >
      <div class="text-center">
        Вы уверены, что хотите {{ removeActionText }}?
      </div>
      <div class="modal-footer btn-group">
        <div class="btn btn-light" @click="visible = !visible">Отмена</div>
        <div
          class="btn btn-light"
          @click="
            removeFunction();
            visible = !visible;
          "
        >
          Да
        </div>
      </div>
    </div>
    <div style="display: block" v-if="visible" class="blocker"></div>

    <!-- модальное подтверждения создания заказа -->
    <!-- <div
      class="modal fade in"
      id="orderCreated"
      tabindex="-1"
      role="dialog"
      aria-labelledby="orderCreatedLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="orderCreatedLabel">Ваш заказ успешно создан!</h5>
          </div>

          <div class="modal-body">
            <div class="text-center">
              Копия заказа отправлена на указанный
              <br />при оформлении эл. адрес.
              <br />
            </div>
          </div>

          <div class="modal-footer">
            <a href="/" class="btn btn-block btn-light">OK</a>
          </div>
        </div>
      </div>
    </div> -->
    <div v-show="orderModal" class="orderCheck" v-if="!step2">
      <form class="orderForm" id="orderForm" @submit.prevent="createOrder">
        <div class="order-form__title ff">
          <span>Данные получателя</span>
          <p @click="orderModal = !orderModal">Вернуться в корзину</p>
        </div>
        <div class="order-form__input">
          <input
            placeholder="Email*"
            name="email"
            tabindex="1"
            type="email"
            v-model="orderData.email"
            required
          />
        </div>
        <div class="order-form__input">
          <input
            selectonfocus="true"
            placeholder="Телефон*"
            name="phone"
            tabindex="2"
            required
            id="phone"
            type="tel"
          />
        </div>
        <div class="order-form__input">
          <input
            placeholder="Имя"
            name="name"
            type="text"
            tabindex="3"
            v-model="orderData.name"
          />
        </div>
        <div class="order-form__req">* Поля обязательные для заполнения</div>
        <div class="order-form__title">Адрес:</div>
        <div
          class="order-select"
          required
          ref="selectItem"
          tabindex="4"
          @click="showSelect = !showSelect"
        >
          <input
            type="text"
            ref="selectCurrent"
            class="order-select__current"
            value="Выберите город"
            disabled
            required
          />
          <img
            class="arrow-down"
            src="images/redesign/arrow-down.svg"
            alt="arrow-down"
          />
          <ul v-if="showSelect">
            <li
              v-for="city in cities"
              :value="city.name"
              :key="city.index"
              @click="
                setOrderCity($event);
                setDeliveryPrice();
              "
            >
              {{ city.name }}
            </li>
          </ul>
        </div>
        <div class="order-form__input">
          <input
            placeholder="Улица*"
            required
            name="delivery_address"
            type="text"
            tabindex="5"
            v-model="orderData.delivery_address"
          />
        </div>
        <div class="order-adress">
          <div class="order-adress__input">
            <label for="delivery_house">Дом*</label>
            <input
              type="text"
              tabindex="6"
              required
              id="delivery_house"
              v-model="orderData.delivery_house"
              name="delivery_house"
            />
          </div>
          <div class="order-adress__input">
            <label for="delivery_stairs">Подъезд*</label>
            <input
              type="text"
              required
              tabindex="7"
              id="delivery_stairs"
              name="delivery_stairs"
              v-model="orderData.delivery_stairs"
            />
          </div>
        </div>
        <div class="order-adress">
          <div v-if="orderData.delivery_lifting" class="order-adress__input">
            <label for="delivery_floor">Этаж*</label>
            <input
              type="text"
              tabindex="8"
              id="delivery_floor"
              required
              name="delivery_floor"
              v-model="orderData.delivery_floor"
            />
          </div>
          <div v-if="orderData.delivery_lifting" class="order-adress__input">
            <label for="delivery_apartment">Квартира*</label>
            <input
              type="text"
              required
              tabindex="9"
              id="delivery_apartment"
              name="delivery_apartment"
              v-model="orderData.delivery_apartment"
            />
          </div>
        </div>
        <div style="display: flex" class="order-form__title">
          Дополнительно
          <img class="icon-i" src="images/redesign/icon-i.svg" alt="icon-i" />
        </div>
        <div class="order-form__checkbox">
          <input
            type="checkbox"
            id="delivery_lifting"
            name="delivery_lifting"
            @click="orderData.delivery_lifting = !orderData.delivery_lifting"
          />
          <label for="delivery_lifting">Подъем на этаж</label>
        </div>
        <div class="order-form__checkbox">
          <input
            type="checkbox"
            id="delivery_assemble"
            name="delivery_assemble"
            @click="orderData.delivery_assemble = !orderData.delivery_assemble"
          />
          <label class="order-form__second-label" for="delivery_assemble"
            >Сборка</label
          >
        </div>
        <div class="order-form__input">
          <input
            type="text"
            placeholder="Комментарий к заказу"
            name="comment"
            id="comment"
            v-model="orderData.comment"
          />
        </div>
        <div class="order-promo">
          <div class="order-form__input">
            <input
              type="text"
              placeholder="ПРОМОКОД"
              name="promoCode"
              @input="resetPromoCode"
              v-model="orderData.promo_code"
            />
          </div>
          <img
            class="order-promo__button"
            src="images/redesign/promo-button.svg"
            alt="button"
            :disabled="!orderData.promo_code"
            @click="applyPromoCode"
          />
        </div>
        <div v-if="promoCodeIsInvalid">
          <h5 class="text-danger">Промокод недействителен</h5>
        </div>
        <div v-if="orderData.promo_discount">
          <h5 class="text-success">Промокод применён</h5>
        </div>
        <div class="order-agreement">
          Нажимая “Оформить заказ”, <br />
          вы подтверждаете, что ознакомились <br />
          и согласны с условиями <a href="/deliverance">Доставки</a> и
          <a href="/payment">Оплаты</a>
        </div>
        <div class="order-bottom__label">
          <span>Курьерские услуги:</span>
          <span>{{ deliveryCost }} BYN</span>
        </div>
        <div class="order-bottom__label">
          <span>Скидка по промокоду:</span>
          <span>{{ orderData.promo_discount }}</span>
        </div>
        <div class="order-total">
          <div>Итого:</div>
          <div class="order-total-price">
            {{ totalPriceWithDelivery }} <span>BYN</span>
          </div>
        </div>
        <button type="submit" class="order-button" v-if="!loading">Оформить заказ</button>
        <button type="submit" class="order-button order-loader" v-else>
          <div class="loader"></div>
        </button>
      </form>
    </div>
    <div v-show="orderModal" class="order-success__modal" v-if="step2">
      <h4>Ваш заказ принят!</h4>
      <p>Копия заказа придет на ваш email</p>
      <a href="/">
        <img src="images/redesign/icons/cart-page__arrow.svg" alt="arrow" />
        Вернуться к покупкам
      </a>
      <img
        class="modal-check"
        src="images/redesign/icons/modal-check.svg"
        alt="check"
      />
    </div>
    <div
      style="display: block"
      @click="
        orderModal = !orderModal;
        setBodyClass();
      "
      v-if="orderModal"
      class="blocker"
    ></div>
  </div>
</template>

<script>
import { updateCartCountHeader } from "../app";

export default {
  mounted() {
    if (window.innerWidth < 1026) {
      this.mobile = true;
    } else {
      this.mobile = false;
    }

    if (fbq) {
      fbq("track", "InitiateCheckout");
    }

    this.cart_id = localStorage.getItem("cart_id");

    this.getCities();
    this.updateCart();

    setTimeout(() => {
      $("body").append($("#orderCreated"));
      $("body").append($("#removeItem"));

      let that = this;

      $(".form-control[tabindex]").on("keyup", function (e) {
        if (e.code !== "Enter") {
          return;
        }

        let tabIndex = parseInt($(this).attr("tabindex"));
        let nextTabindex = tabIndex + 1;

        let nextEl = $(".form-control[tabindex=" + nextTabindex + "]");
        if (nextEl.length > 0) {
          nextEl.focus();
        } else {
          $(this).blur();
        }
      });

      document.addEventListener("cart:add", function (event) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();

        if (that.loading) {
          return;
        }

        let payload = event.detail;

        that.add(payload);

        return false;
      });
    }, 50);

    setTimeout(() => {
      $(".phone-input").mask("+375 (99) 999-99-99");
    }, 5000);
  },
  name: "CartPage",
  data() {
    return {
      step2: false,
      loading: false,
      cart: {},
      cart_id: "",
      visible: false,
      orderModal: false,
      showSelect: false,
      mobile: false,
      removeActionText: "",
      removeFunction: () => {},
      cities: [],
      deliveryCost: 0,
      discountPrice: null,
      promoCodeIsInvalid: false,
      orderData: {
        email: "",
        phone: "",
        comment: "",
        name: "",
        city: "",
        delivery_address: "",
        delivery_house: "",
        delivery_stairs: "",
        delivery_floor: "",
        delivery_apartment: "",
        delivery_lifting: false,
        delivery_assemble: false,
        promo_code: "",
        promo_discount: "",
      },
    };
  },
  methods: {

    searchFocus() {
      document.querySelector("#search").focus();
    },

    getArticule(item) {
      return item.vendor_code;
    },

    setOrderCity($event) {
      let current = this.$refs.selectCurrent;
      let text = $event.target;
      text = text.getAttribute("value");
      current.value = text;
      this.orderData.city = text;
    },

    setBodyClass() {
      let body = $("body");
      if (body.hasClass("body-fixed")) {
        body.removeClass("body-fixed");
      } else {
        body.addClass("body-fixed");
      }
    },

    setDeliveryPrice() {
      for (let i = 0; i < this.cities.length; i++) {
        if (this.orderData.city == this.cities[i].name) {
          if (this.cities[i].freeShippingFrom) {
            this.deliveryCost =
              this.cart.totalPriceByn < this.cities[i].freeShippingFrom
                ? parseFloat(this.cities[i].price)
                : 0;
          } else {
            this.deliveryCost = parseFloat(this.cities[i].price);
          }
        }
      }
    },

    updateCart() {
      this.loading = true;

      axios.get("/cart?cart_id=" + this.cart_id).then((r) => {
        let data = r.data;
        this.loading = false;

        this.cart = data;
        this.cart_id = data.id;
        localStorage.setItem("cart_id", data.id);
        if (this.cart.charge) {
          this.commissionType = this.cart.charge.type == "fixed" ? "₽" : "%";
        }

        setTimeout(() => {
          let currency = localStorage.getItem("currency");
          $(window).trigger("currency:change_no_emit", currency);
        }, 100);

        updateCartCountHeader(this.cart.itemsCount);
      });
    },

    createOrder() {
      if(!this.orderData.city){
        return this.showSelect = true
      }

      if (this.loading) {
        return;
      }

      this.loading = true;

      try {
        ga("send", "event", "Форма", "Заказать");
      } catch (e) {}

      var form = $(".orderForm");

      this.orderData.phone = $("#phone").val();
      let request = {
        ...this.orderData,
        cart: this.cart,
        cart_id: this.cart_id,
      };

      axios.post("/cart/order", request).then((r) => {
        this.loading = false;
        $("._order_form_submit").text("Оформить заказ").removeAttr("disabled");

        let data = r.data;

        if (data.success) {
          $("#orderCreated").modal({
            backdrop: "static",
            show: true,
          });

          if (fbq) {
            fbq("track", "Purchase", {
              value: data.total,
              currency: "BYN",
            });
          }
          this.step2 = true;
          setTimeout(() => location.reload(), 1000);
          return true;
        }

        if (data.errors) {
          Object.keys(data.errors).forEach((error, i, c) => {
            form
              .find('input[name="' + error + '"]')
              .after("<span class='error'>" + data.errors[error] + "</span>");
          });
        }

        function addWarning(text) {
          var html =
            '<div class="my_alert my_warning">' +
            '<span class="alert_text">' +
            text +
            "</span>" +
            '<a class="alert_close"><img src="/images/icons/cross.png"></a>' +
            "</div>";

          $("body").append(html);
          setTimeout(() => {
            $(".my_alert").detach();
          }, 2000);
        }

        if (data.warning) {
          addWarning(data.warning);
        }
      });
    },

    goToAddressPart() {
      $(".cart_item__left").addClass("invisible");
      $(".cart_item__right").addClass("visible");
      $(".back-to-cart").show();
    },

    goToCartPart() {
      $(".cart_item__left").removeClass("invisible");
      $(".cart_item__right").removeClass("visible");
      $(".back-to-cart").hide();
    },

    getCities() {
      axios.get("/cart/cities").then((r) => {
        this.cities = r.data;
      });
    },

    add(payload) {
      if (!payload.vendor_code) {
        return;
      }

      this.loading = true;

      axios
        .post("/cart/add", {
          add_item: payload.vendor_code,
          quantity: payload.qty,
          cart_id: this.cart_id,
        })
        .then((r) => {
          this.loading = false;

          let data = r.data;

          updateCartCountHeader(data.count);

          this.updateCart();
          $(
            ".product-card[data-vendor-code='" + payload.vendor_code + "']"
          ).addClass("added-to-cart");

          if (typeof payload.callback == "function") {
            payload.callback();
          }

          if (fbq) {
            fbq("track", "AddToCart");
          }
        });
    },

    changeQty(itemIndex, quantity) {
      let id = this.cart.items[itemIndex].product_id;

      if (quantity <= 0) {
        this.removeItem(itemIndex, true);
      }

      axios
        .post("/cart/change_qty", {
          id: id,
          quantity: quantity,
          cart_id: this.cart_id,
        })
        .then((data) => {
          this.cart.items[itemIndex].qty = quantity;
          this.updateCart();
        });
    },

    removeItem(itemIndex, forceRemove = false) {
      $(document).trigger("cart:remove", {
        detail: itemIndex,
      });

      this.removeActionText = "удалить позицию из заказа";

      this.removeFunction = () => {
        let id = this.cart.items[itemIndex].product_id;
        let code = this.cart.items[itemIndex].vendor_code;

        this.loading = true;

        axios
          .post("/cart/remove_item", {
            id: id,
            cart_id: this.cart_id,
          })
          .then((r) => {
            let data = r.data;

            this.loading = false;
            updateCartCountHeader(data.count);

            $('.product-card[data-vendor-code="' + code + '"]').removeClass(
              "added-to-cart"
            );

            this.updateCart();

            if (data.count == 0 && window.location.href == "/order") {
              window.location.reload();
            }

            // $("#removeItem").modal("hide");
          });
      };

      if (forceRemove) {
        return this.removeFunction();
      }

      // $("#removeItem").modal("show");
    },
    clearAll() {
      this.removeActionText = "очистить корзину";
      this.removeFunction = () => {
        axios.get("/cart/clear?cart_id=" + this.cart_id).then(() => {
          this.updateCart();
          $(".add-to-cart._add_to_cart").removeClass("added");
          $(".add-to-cart._add_to_cart").removeClass("_remove_from_cart");
          $(".cart-count").text("0");
        });
      };

      // $("#removeItem").modal("show");
    },
    applyPromoCode() {
      this.discountPrice = null;
      this.orderData.promo_discount = null;

      axios
        .post("cart/get_discount", {
          cart_id: this.cart_id,
          promo_code: this.orderData.promo_code,
        })
        .then((response) => {
          if (!response.data.discount) {
            this.promoCodeIsInvalid = true;
            return;
          }

          this.orderData.promo_discount = response.data.discount;
          this.discountPrice = response.data.new_total;
          this.promoCodeIsInvalid = false;
        });
    },
    resetPromoCode() {
      this.orderData.promo_discount = null;
      this.discountPrice = null;
      this.promoCodeIsInvalid = false;
    },
  },
  computed: {
    sortedItems() {
      let values = Object.values(this.cart.items);
      values.sort((i1, i2) => {
        return i2.added_at - i1.added_at;
      });

      return values;
    },
    totalPriceWithDelivery() {
      let total =
        (this.discountPrice || this.cart.totalPriceByn) + this.deliveryCost;

      return total.toFixed(2) || 0;
    },
    onlyItemsWithFixedPrice() {
      return Object.keys(this.cart.itemsIdsWithCommonPrice).length == 0;
    },
  },
};
</script>
