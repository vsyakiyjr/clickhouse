<template>
  <div class="search-widget">
    <form action="/search" id="search-form" autocomplete="off" v-if="!mobile">
      <input type="hidden" name="full" value="true" autocomplete="off" />
      <vue-simple-suggest
        v-model="chosen"
        :list="autocomplete"
        :controls="controls"
        :prevent-submit="false"
        :destyled="true"
        ref="autocomplete"
        :display-attribute="'name'"
      >
        <div slot="suggestion-item" slot-scope="{ suggestion }">
          <a :href="`/catalog/product/${suggestion.vendor_code}`">
            <div
              class="search-result"
              :class="suggestion.added_to_cart ? 'added-to-cart' : ''"
            >
              <img :src="suggestion.photo" :alt="suggestion.name" />

              <div class="description">
                <div class="name">{{ suggestion.name }}</div>
                <div class="description">{{ suggestion.type }}</div>
              </div>

              <span
                class="item-add-to-cart"
                @click.stop="addToCart(suggestion)"
              >
                <img
                  class="search-widget__icon"
                  src="/images/redesign/icons/plus.svg"
                  alt="plus-icon"
                />
              </span>
            </div>
          </a>
        </div>
        <div slot="misc-item-below" slot-scope="{ suggestions }">
          <div class="loader search-loader" v-if="loading"></div>
          <div
            class="search-bottom all-results"
            v-show="suggestions.length > 0"
          >
            <a :href="'/search?full=true&search=' + search"> Все варианты</a>
          </div>
        </div>

        <div slot="misc-item-above" slot-scope="{ suggestions, query }">
          <div
            class="search-bottom no-results"
            v-show="query && suggestions.length == 0 && !loading"
          >
            Ничего не найдено
          </div>
        </div>
        <div class="search-input">
          <img
            class="search-icon"
            src="/images/redesign/icons/search-black.svg"
            alt="search"
          />
          <input
            type="text"
            autocomplete="off"
            name="search"
            class="form-control"
            v-model="search"
            id="search"
            @keypress.prevent.enter.stop="submit"
            placeholder="Поиск"
          />
        </div>
      </vue-simple-suggest>
    </form>
    <div
      class="mobile-search__button"
      v-if="mobile && !visible"
      @click="toggleVisible"
    >
      <img src="/images/redesign/icons/search.svg" alt="search" />
    </div>
    <div
      class="mobile-search__button"
      v-if="mobile && visible"
      @click="modalClose"
    >
      <img src="/images/redesign/icons/close-search.svg" alt="close" />
    </div>
    <div class="modal" v-if="mobile && visible">
      <div class="modal-content">
        <h2 class="search-modal__title">Какой товар ищите?</h2>
        <form action="/search" id="search-form" autocomplete="off">
          <input type="hidden" name="full" value="true" autocomplete="off" />
          <vue-simple-suggest
            v-model="chosen"
            :list="autocomplete"
            :controls="controls"
            :prevent-submit="false"
            :destyled="true"
            ref="autocomplete"
            :display-attribute="'name'"
          >
            <div slot="suggestion-item" slot-scope="{ suggestion }">
              <a
                :href="`/catalog/product/${suggestion.vendor_code}`"
                class="mobile-search__item"
                :class="suggestion.added_to_cart ? 'added-to-cart' : ''"
              >
                <div class="mobile-search__photo">
                  <img :src="suggestion.photo" :alt="suggestion.name" />
                </div>
                <div class="mobile-search__inner">
                  <div class="mobile-search__name">{{ suggestion.name }}</div>
                  <div class="mobile-search__price">
                    {{ suggestion.price }} <span>BYN</span>
                  </div>
                  <div
                    class="mobile-search__type"
                    v-if="!suggestion.fixed_price"
                  >
                    + комиссия за доствку
                  </div>
                  <div
                    class="mobile-search__type"
                    v-if="suggestion.fixed_price"
                  >
                    Фикс. цена: {{ suggestion.fixed_price }} BYN
                  </div>
                  <div class="mobile-search__description">
                    {{ suggestion.type }}
                  </div>
                </div>
              </a>
            </div>
            <div slot="misc-item-below" slot-scope="{ suggestions }">
              <div class="loader search-loader" v-if="loading"></div>
              <div
                class="search-bottom all-results"
                v-show="suggestions.length > 0"
              >
                <a :href="'/search?full=true&search=' + search">
                  Все результаты</a
                >
              </div>
            </div>

            <div slot="misc-item-above" slot-scope="{ suggestions, query }">
              <div
                class="search-bottom no-results"
                v-show="query && suggestions.length == 0 && !loading"
              >
                Ничего не найдено
              </div>
            </div>
            <div class="search-input">
              <img
                class="search-icon"
                src="/images/redesign/icons/search-black.svg"
                alt="search"
              />
              <div class="search-wrapper">
                <div
                  class="close-search"
                  v-if="search.length"
                  @click="search = ''; chosen = '';"
                >
                  <img
                  src="/images/redesign/icons/close-search.svg"
                  alt="close"
                  />
                </div>
                <input
                  type="text"
                  autocomplete="off"
                  name="search"
                  class="form-control"
                  v-model="search"
                  id="search"
                  @keypress.prevent.enter.stop="submit"
                  placeholder="Впишите название, категорию или артикул"
                />
              </div>
            </div>
          </vue-simple-suggest>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SearchWidget",
  data: function () {
    return {
      controls: {
        selectionUp: [38],
        selectionDown: [40],
        select: [], //[13],
        showList: [40],
        hideList: [27],
        autocomplete: [], //[32, 13]
      },
      mobile: false,
      visible: false,
      loading: false,
      results: [],
      ajaxRequest: null,
      chosen: "",
      search: "",
    };
  },
  mounted() {
    let searchParams = new URL(document.location).searchParams;
    this.search = searchParams.get("search");

    this.getWidth();
    window.addEventListener("resize", this.getWidth);
  },
  watch: {
    search() {
      this.autocomplete();
    },
  },
  methods: {
    toggleVisible() {
      const body = document.querySelector("body");
      this.visible = !this.visible;
      if (this.visible) {
        body.classList.add("body-fixed");
      } else {
        body.classList.remove("body-fixed");
      }
    },
    modalClose() {
      this.search = "";
      this.toggleVisible();
    },
    getWidth() {
      const width = window.innerWidth;
      if (width > 901) {
        this.mobile = false;
        return;
      }
      this.mobile = true;
    },
    clear() {
      this.$refs.autocomplete.clearSuggestions();
      this.results = [];
      this.search = "";
    },
    submit() {
      $("#search").val(this.search);
      return $("#search-form").submit();
    },
    // focus() {
    //   if ($(window).width() >= 768) {
    //     let cartHeight = $(".cart-widget").height() + 35;

    //     $(".attach-sticky-here").attr(
    //       "style",
    //       `position: fixed; top: -${cartHeight}px;`
    //     );
    //   }
    // },
    // blur() {
    //   if ($(window).width() >= 768) {
    //     $(".attach-sticky-here").attr("style", `position: fixed; top: 55px;`);
    //   }
    // },

    autocomplete() {
      if (!this.search) {
        this.loading = false;
        this.clear();

        return;
      }

      // cancel  previous ajax if exists
      if (this.ajaxRequest) {
        this.ajaxRequest.cancel();
      }

      this.loading = true;
      // creates a new token for upcomming ajax (overwrite the previous one)
      this.ajaxRequest = axios.CancelToken.source();

      return axios
        .get("/search?search=" + this.search, {
          cancelToken: this.ajaxRequest.token,
        })
        .then((response) => {
          this.$forceUpdate();
          this.loading = false;
          return (this.results = response.data);
        });
    },
    addToCart(item) {
      let eventPayload = {
        product_id: item.id,
        qty: 1,
      };

      let e = new CustomEvent("cart:add", {
        detail: eventPayload,
      });

      document.dispatchEvent(e);
      item.added_to_cart = true;
    },

    openUrl(item) {
      window.location.href = "/catalog/product/" + item.vendor_code;
    },
  },
};
</script>

<style lang="scss" scoped>
.search-widget__icon {
  width: 12px;
  height: 12px;
}
</style>