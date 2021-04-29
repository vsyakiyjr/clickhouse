<template>
  <div class="callback">
    <div class="mobile-phone" @click="openModal">
      <img src="/images/redesign/icons/phone.svg" alt="phone" />
    </div>
    <a class="callback-button" @click="openModal">Заказать звонок</a>
    <div class="modal modal-phone" id="callbackModal">
      <div
        v-if="visible"
        class="modal-dialog modal-dialog-centered"
        role="document"
      >
        <form
          class="modal-content"
          v-on:submit.prevent="callBackRequest()"
          v-show="!sent"
        >
          <div class="modal-body">
            <div @click="closeModal()" class="modal-close">
              <img src="images/redesign/icons/modal-close.svg" alt="close" />
            </div>
            <div class="modal-footer mobile-show">
            <h4 class="modal-title">Свяжитесь с нами любым способом</h4>
            <div class="modal-phones">
              <a :href="`tel:${phones[0]}`" v-if="phones[0]">{{ phones[0] }}</a>
              <a :href="`tel:${phones[1]}`" v-if="phones[1]">{{ phones[1] }}</a>
            </div>
          </div>
            <h5 class="modal-subtitle">На какой номер перезвонить?</h5>
            <div class="mobile-wrapper">
              <masked-input
                v-model="phone"
                mask="\+\375 (11) 111-11-11"
                class="form-control form-control-lg phone-input"
                placeholder="+375 (__) ___-__-__"
                required
                autofocus
              />
              <button class="modal-button mobile-show" :disabled="loading">
                Отправить
              </button>
            </div>
          </div>
          <div class="modal-footer mobile-hidden">
            <button class="modal-button" :disabled="loading">Отправить</button>
          </div>
        </form>
        <div class="modal-content" v-show="sent">
          <div @click="closeModal()" class="modal-close">
            <img src="images/redesign/icons/modal-close.svg" alt="close" />
          </div>
          <div class="modal-body">
            <h4 class="modal-title modal-title__end">Спасибо за заявку!</h4>
            <p class="modal-subtitle">Мы скоро вам перезвоним</p>
            <img
              class="modal-check"
              src="images/redesign/icons/modal-check.svg"
              alt="check"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import MaskedInput from 'vue-masked-input';

export default {
  name: "Callback",
  components: {
    MaskedInput
  },
  data() {
    return {
      visible: false,
      phone: "",
      sent: false,
      loading: false,
    };
  },
  props: {
    phones: {
      type: Array,
      default: () => [],
    },
  },
  methods: {
    openModal() {
      if (this.sent) {
        this.sent = !this.sent;
      }
      $("#callbackModal").show();
      this.visible = !this.visible;
      $(".blocker").addClass("active-phone");
      $("body").addClass("body-phone");
    },

    closeModal() {
      this.visible = !this.visible;
      $(".blocker").removeClass("active-phone");
      $("body").removeClass("body-phone");
    },
    
    callBackRequest() {
      const phone = this.phone;
      this.loading = true;

      axios.get("/callback?phone=" + phone).then(() => {
        this.loading = false;
        this.sent = true;
      });
    },
  },
};
</script>