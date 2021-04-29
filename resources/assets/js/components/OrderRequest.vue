<template>
  <div class="order-request">
    <div class="modal">
      <div :class="{'modal-content__body' : step !== 2}" class="modal-content">
        <div id="orderClose" class="modal-close" @click="removeFile(); step = 1;">
          <img src="/images/redesign/icons/modal-close.svg" alt="close">
        </div>
        <form v-on:submit.prevent="submit()">
          <div>
            <h5 class="modal-title" id="orderRequestLabel">
              {{ step == 1 ? "Вставьте список артикулов" : "" }}
              {{ step == 2 ? "Оставьте свои данные" : "" }}
              {{ step == 3 ? "Запрос отправлен!" : "" }}
            </h5>
          </div>
          <div class="modal-body">
            {{ step == 3 ? "Менеджер свяжется с вами в ближайшее время!" : "" }}

            <div class="input-group-1" v-show="step == 1">
              <div class="form-group">
                <textarea
                  name="list"
                  id="list"
                  class="form-control"
                  cols="30"
                  v-model="list"
                  rows="10"
                ></textarea>
              </div>
              <div class="form-group text-center">
                <div class="attach-text">
                  Прикрепите файл со списком покупок
                </div>
                <input
                  type="file"
                  id="addFile"
                  multiple="multiple"
                  v-on:change="addFile($event)"
                  style="display: none"
                />

                <div class="file-name my-2" v-if="file">
                  {{ cutText(file.name, 30) }}
                  <i
                      class="fa fa-times text-danger"
                      style="cursor: pointer"
                      v-on:click="removeFile()"
                  ></i>
                </div>

                <label class="order__add-file" for="addFile">
                  Добавить файл
                </label>
              </div>
            </div>

            <div class="input-group-2" v-show="step == 2">
              <div class="order-input">
                <input
                  type="text"
                  class="form-control"
                  name="name"
                  v-model="name"
                  placeholder="Имя"
                  autocomplete="off"
                  required
                />
              </div>
              <div class="order-input">
                <masked-input
                  v-model="phone"
                  mask="\+\375 (11) 111-11-11"
                  class="form-control form-control-lg phone-input"
                  placeholder="Телефон"
                  required
              />
              </div>
              <div class="order-input">
                <input
                  type="email"
                  class="form-control"
                  name="email"
                  v-model="email"
                  placeholder="Email"
                  autocomplete="off"
                  required
                />
              </div>
              <div class="order-input">
                <input
                  type="text"
                  class="form-control"
                  name="address"
                  v-model="address"
                  placeholder="Адрес доставки"
                  autocomplete="off"
                  required
                />
              </div>
            </div>
          </div>

          <div :class="{'order-footer' : step !== 2}" class="modal-footer">
            <!-- <button type="button" v-if="step == 1" @click="cancel">
              Отмена
            </button> -->
            <!-- <button
              class="btn btn-light"
              type="button"
              v-on:click.prevent.stop="step = 1"
              v-if="step == 2"
            >
              Назад
            </button> -->
            <button
              class="btn-order"
              v-if="step == 1"
              v-on:click="goToStep2()"
              type="button"
            >
              Далее
            </button>
            <button class="btn-order__send" v-if="step === 2 && !loading" type="submit">
              Отправить
            </button>
            <div class="button-green" v-if="step === 2 && loading">
              <div class="loader"></div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import MaskedInput from 'vue-masked-input';
export default {
  name: "OrderRequest",
  components: {
    MaskedInput
  },
  data: function () {
    return {
      step: 1,
      list: "",
      file: '',
      name: "",
      phone: "",
      email: "",
      address: "",
      visible: true,
      loading: false
    };
  },
  methods: {
    modalToggle(event) {
      event.preventDefault();
      this.visible = !this.visible;
    },

    cancel() {
      this.visible = false;
      this.removeFile();
    },

    goToStep2() {
      if (!this.list && !this.file) {
        return;
      }

      this.step = 2;
    },

    removeFile() {
      this.file = '';
      $("#addFile").val("");
    },

    cutText(text, limit) {
      if(!text){
        return ''
      }

      if (text.length > limit) {
        return text.slice(0, limit) + '...';
      }

      return text;
    },

    addFile($event) {
      this.file = $event.target.files[0];
    },

    clearAll() {
      this.visible = false;
      this.file = [];
      this.name = '';
      this.email = '';
      this.address = '';
      this.phone = '';
      this.list = [];
    },

    submit() {
      let formData = new FormData();
      formData.append("file", this.file);
      formData.append("name", this.name);
      formData.append("email", this.email);
      formData.append("address", this.address);
      formData.append("phone", this.phone);
      formData.append("list", this.list);

      this.loading = true;

      axios
        .post("/order_request", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then(() => {
          this.step = 3;
          this.clearAll();

          setTimeout(() => {
            this.step = 1;
          }, 10000);
        });
    },

    openModalWithParams() {
      const modal = document.querySelector('#orderModal');
      const blocker = document.querySelector('#blocker');
      const url = new URL(window.location).searchParams.get('order');

      if (url && modal && blocker) {
        this.visible = true;
        modal.classList.add('active');
        blocker.classList.add('active-order');
      }
    }
  },
  mounted() {
    this.openModalWithParams();
  }
};
</script>
