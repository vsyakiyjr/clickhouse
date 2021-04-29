<template>
  <div class="currency-switch" :class="view">
    <div class="mobile" v-if="view == 'mobile'">
      <ul class="mobile-val-list">
        <li
        :class="{'current': !value, 'not-current': value}"
        @click="setCurrency('RUB'); visible=!visible"
        >RUB</li>
        <li
        :class="{'current': value, 'not-current': !value}"
        @click="setCurrency('BYN'); visible=!visible"
        >BYN</li>
      </ul>
    </div>
    <div class="popup" v-if="view == 'popup'">
      <div @click="visible=!visible" class="current-val">
        <template v-if="!value">RUB</template>
        <template v-else>BYN</template>
        <img src="/images/redesign/icons/arrow-down_gray.svg" alt="arrow">
      </div>
      <ul v-if="visible" class="switch-list">
        <li
        :class="{'current': !value, 'not-current': value}"
        @click="setCurrency('RUB'); visible=!visible"
        >RUB</li>
        <li
        :class="{'current': value, 'not-current': !value}"
        @click="setCurrency('BYN'); visible=!visible"
        >BYN</li>
      </ul>
    </div>

    <div class="tabs" v-if="view == 'tabs'">
      <span
        class="rub"
        @click="setCurrency('RUB')"
        :class="{'selected': !value, 'not-selected': value}"
      >RUB</span>
      <span
        class="byn"
        @click="setCurrency('BYN')"
        :class="{'selected': value, 'not-selected': !value}"
      >BYN</span>
    </div>

  </div>
</template>

<script>
export default {
  props: {
    view: String
  },
  mounted() {
    let currency = localStorage.getItem("currency");

    if (currency) {
      this.value = currency == "BYN";
    } else {
      this.setCurrency('BYN')
    }

    setTimeout(() => {
      this.toggleCurrency();

      let that = this;

      $(window).on("currency:change", function(event, currency) {
        that.value = currency == "BYN";
      });

      $(window).on("currency:change_no_emit", function(event, currency) {
        that.value = currency == "BYN";
        that.toggleCurrency(false);
      });
    }, 50);
  },
  name: "CurrencySwitch",
  data() {
    return {
      value: true,
      visible: false
    };
  },
  methods: {
    setCurrency(currency) {
      this.value = currency == "BYN";
      this.toggleCurrency();
    },
    toggleCurrency(emit = true) {
      if (this.value) {
        $(".price-amount.RUB").hide();
        $(".price-amount.BYN").show();
      } else {
        $(".price-amount.BYN").hide();
        $(".price-amount.RUB").show();
      }

      let currency = this.value ? "BYN" : "RUB";
      localStorage.setItem("currency", currency);

      if (emit) {
        $(window).trigger("currency:change", currency);
      }
    }
  }
};
</script>

<style scoped>
</style>
