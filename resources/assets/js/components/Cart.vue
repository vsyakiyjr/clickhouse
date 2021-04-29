<template>
  <div class="cart-widget__inner">
    <div style="display:none;" id="cartWidget" class="cart-widget" v-if="type == 'widget'">
    <div class="cart-widget__wrap">
      <p
        class="empty-cart-text"
        v-show="cart.itemsCount == 0"
      >Корзина пуста <br>
      Найдите товар через <a class="cart-widget__search-link" href="#" @click="searchFocus">поиск</a> или <br>
      <a href="/catalog">каталог</a> и добавьте его в корзину</p>

      <div class="cart-items" v-show="cart.itemsCount > 0">
      <div class="cart-item" v-for="item in sortedItems" :key="item.model.id">
        <a class="title name" :href="'/catalog/product/' + item.vendor_code">
          <img :src="item.model.photo" :alt="item.model.name" />
        </a>
        <div class="middle-part">
          <div class="title-inner">
          <a class="title name" :href="'/catalog/product/' + item.vendor_code">{{item.model.name}}
          </a>
          <span class="remove-item cart-button" @click="changeQty(item.model.id, item.qty - 100); removeAllClasses();">×</span>
          </div>
          <div class="description">{{item.model.type}}</div>
          <div class="price-row">
            <div class="price-without-commission price">
              <span class="price-amount RUB">{{ item.price }} <span class="value">RUB</span></span>
              <span class="price-amount BYN">{{ item.price_byn }} <span class="value">BYN</span></span>
            </div>
            <div class="qty-controls">
              <img
              src="/images/redesign/icons/minus.svg"
              alt="minus"
              class="minus-circle cart-button qty-button"
              @click="changeQty(item.model.id, item.qty - 1); removeAllClasses();"
              >
              <span class="qty">{{item.qty}}</span>
              <img
              src="/images/redesign/icons/plus.svg"
              alt="plus"
              class="plus-circle cart-button qty-button"
              @click="changeQty(item.model.id, item.qty + 1)"
              >
            </div>
          </div>
          <div class="price-row">
            <div class="total-price-order">С доставкой:</div>
            <!-- <span class="price-amount RUB">{{ item.price_order_byn_with_qty }} <span class="value">RUB</span></span> -->
              <span class="price-amount">{{ item.price_order_byn_with_qty }} <span class="value">BYN</span></span>
          </div>
        </div>
      </div>
    </div>
    </div>
    <div class="cart-widget__bottom">
        <div class="commission" v-if="cart.itemsCount > 0">
        <div class="commission-text">
          Комиссия
        </div>
        <div class="commission-amount">{{cart.charge.amount}} <span>{{ commissionType }}</span></div>
      </div>
      <div class="total-price price-amount BYN" v-if="cart.itemsCount > 0">Сумма заказа: <span>{{ cart.totalPriceByn.toFixed(2) }} <p>BYN</p></span></div>
      <div class="total-price price-amount RUB" v-if="cart.itemsCount > 0">Сумма заказа: <span>{{ cart.totalPriceRub.toFixed(2) }} <p>RUB</p></span></div>
      <div class="go-to-order text-center" v-if="cart.itemsCount > 0">
        <a href="/order" class="btn btn-lg btn-orange order-button">Оформить</a>
        <span
          class="clear-cart cart-button"
          v-show="cart.itemsCount > 0"
          @click="clearAll()"
        >Очистить</span>
      </div>
    </div>
    </div>
    <div v-if="visible" class="modal-dialog" role="document">
          <div class="text-center">Хотите {{removeActionText}}?</div>
          <div class="modal-footer btn-group">
            <div class="btn btn-light" @click="visible=!visible">Отмена</div>
            <div class="btn btn-light" @click="removeFunction(); removeAllClasses(); visible=!visible;">Да</div>
          </div>
      </div>
      <div style="display:block;" v-if="visible" class="blocker"></div>
  </div>
</template>

<script>
import { updateCartCountHeader } from "../app";

export default {
  name: "Cart",
  props: {
    type: {
      type: String,
      validator: val => ["full", "widget"].includes(val)
    }
  },
  mounted() {
    this.cart_id = localStorage.getItem("cart_id");

    this.updateCart(true);

    if ($(window).width() >= 1024) {
      setTimeout(function() {
        $(".attach-sticky-here").sticky({ topSpacing: 55 + 85 }); // compensate ark on top of cart
      }, 400);
    }

    let that = this;

    this.currency = localStorage.getItem("currency") || "BYN";

    $(window).on("currency:change", function(event, currency) {
      that.currency = currency;
    });

    document.addEventListener(
      "cart:refresh",
      () => {
        this.updateCart();

        return false;
      },
      this,
      false
    );

    document.addEventListener("cart:add", function(event) {
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

    document.addEventListener("cart:remove", function(event) {
      event.stopPropagation();
      event.preventDefault();
      event.stopImmediatePropagation();

      if (that.loading) {
        return;
      }

      let productId = event.detail;
      that.removeItem(productId, true);

      return false;
    });

    setTimeout(() => {
      $("body").append($("#removeItem"));
    }, 500);
  },

  data: function() {
    return {
      visible: false,
      cart_id: "",
      loading: false,
      commissionType: "%",
      cart: {},
      currency: "BYN",
      removeFunction: () => {},
      removeActionText: ""
    };
  },
  methods: {
    searchFocus() {
      document.querySelector('#search').focus();
    },

    removeAllClasses() {
      $(".product-card .add-to-cart._add_to_cart.added._remove_from_cart").each(function(indx, element){
        $(element).removeClass('added');
        $(element).removeClass('_remove_from_cart');
        // console.log(element);
      });
      $('.cart-count').text('0');
    },
    updateCart() {
      this.loading = true;

      axios.get("/cart?cart_id=" + this.cart_id).then(r => {
        let data = r.data;
        this.loading = false;

        this.cart = data;
        this.cart_id = data.id;
        localStorage.setItem("cart_id", data.id);
        this.$forceUpdate();

        setTimeout(() => {
          let currency = localStorage.getItem("currency");
          $(window).trigger("currency:change_no_emit", currency);
        }, 100);

        updateCartCountHeader(this.cart.itemsCount);

        if (this.cart.charge) {
          this.commissionType = this.cart.charge.type === "fixed" ? "₽" : "%";
        }
      });
    },
    add(payload) {
      if (!payload.product_id) {
        return;
      }

      const targetElement = $(
            ".product-card[data-product-id='" + payload.product_id + "']"
          ).find(".add-to-cart");
      const loader = document.createElement('div');
      $(loader).addClass('loader');
      $(targetElement).addClass('loading');
      targetElement.append(loader);

      this.loading = true;

      axios
        .post("/cart/add", {
          cart_id: this.cart_id,
          product_id: payload.product_id,
          quantity: payload.qty
        })
        .then(r => {
          this.loading = false;

          let data = r.data;

          updateCartCountHeader(data.count);
          this.updateCart();

          setTimeout(() => {
            $(loader).remove();
            $(targetElement).removeClass('loading');
            $(targetElement).addClass("added");
            $(targetElement).addClass("_remove_from_cart");
          }, 350);
          // $(targetElement).addClass("added-to-cart");

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
        return this.removeItem(itemIndex, true);
      }

      axios
        .post("/cart/change_qty", {
          cart_id: this.cart_id,
          id: id,
          quantity: quantity
        })
        .then(data => {
          this.cart.items[itemIndex].qty = quantity;
          this.updateCart();
        });
    },
    removeItem(itemIndex, forceRemove = false) {
      this.removeActionText = "удалить позицию из заказа";
      this.removeFunction = () => {
        let id = this.cart.items[itemIndex].product_id;

        this.loading = true;

        axios
          .post("/cart/remove_item", {
            id: id,
            cart_id: this.cart_id
          })
          .then(r => {
            let data = r.data;

            this.loading = false;
            updateCartCountHeader(data.count);

            let targetElement = $(
              '.product-card[data-product-id="' + id + '"]'
            );

            // $(targetElement)
            //   .find(".add-to-cart")

            $(targetElement)
              .find(".added")

            $(targetElement).removeClass("added");

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
      this.visible = !this.visible;
      this.removeActionText = "очистить корзину";
      this.removeFunction = () => {
        axios.get("/cart/clear?cart_id=" + this.cart_id).then(() => {
          this.updateCart();
          $(".product-card .add-to-cart").removeClass("added");
        });
      };
    }
  },
  computed: {
    sortedItems(){
      let values = {};

      if (this.cart.items) {
        values = Object.values(this.cart.items);
        values.sort((i1, i2) => {
          return i2.added_at - i1.added_at
        })
      }

      return values;
    },
    leftBeforeNextCommission() {
      if (!this.cart.bynBeforeNextCharge) {
        return 0;
      }

      if (this.currency == "BYN") {
        return this.cart.bynBeforeNextCharge;
      } else {
        return this.cart.rubBeforeNextCharge;
      }
    }
  }
};
</script>
