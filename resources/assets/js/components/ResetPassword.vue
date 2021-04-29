<template>
  <div style="display:block !important;" class="auth-modal">
    <div class="modal">
      <div class="pt65 modal-content">
        <div class="modal-close auth-close">
          <img src="/images/redesign/icons/modal-close.svg" alt="close" />
        </div>
        <form
          v-if="visible"
          class="login-form"
          method="POST"
          action="/password/request"
        >
          <div class="auth-modal__input">
            <input
              type="email"
              @focus="resetError = false"
              id="email"
              name="email"
              :class="{error: resetError && resetDetalis.email == ''}"
              placeholder="E-mail"
              required
              v-model="resetDetalis.email"
            />
            <p v-if="emailError">{{ emailMessages }}</p>
          </div>
          <div class="auth-modal__input">
            <input
              type="password"
              @focus="resetError = false"
              id="password"
              :class="{error: resetError && resetDetalis.password == ''}"
              name="password"
              placeholder="Введите новый пароль"
              required
              v-model="resetDetalis.password"
            />
            <p v-if="passwordError">{{ passwordMessages }}</p>
          </div>
          <div class="auth-modal__input">
            <input
              type="password"
              @focus="resetError = false"
              id="password-confirm"
              name="password_confirmation"
              :class="{error: resetError && resetDetalis.password_confirmation == ''}"
              required
              placeholder="Повторите пароль"
              v-model="resetDetalis.password_confirmation"
            />
            <p v-if="passwordError">{{ passwordMessages }}</p>
          </div>

          <button
            v-if="!loader"
            @click.prevent="resetPost();"
          >
            Сменить пароль
          </button>
          <button :class="{ active: loader }" v-if="loader">
            <div class="loader"></div>
          </button>
        </form>
      </div>
    </div>
    <div style="display:block;" v-if="visible" class="blocker"></div>
  </div>
</template>

<script>
export default {
  name: "ResetPassword",
  mounted() {
    let str = window.location.pathname;
    str = str.split("/password/reset/");
    this.resetDetalis.token = str[1];
    // console.log(this.resetDetalis.token);
  },
  data: () => ({
    visible: true,
    loader: false,
    resetDetalis: {
      token: "",
      email: "",
      password: "",
      password_confirmation: "",
    },
    resetError: false,
    emailMessages: 'Введён неверный email',
    passwordMessages: 'Введён неверный пароль',
    passwordError: false,
    emailError: false,
    Error: false
  }),
  methods: {
    resetPost() {
      if (
        this.resetDetalis.email != "" &&
        this.resetDetalis.password != "" &&
        this.resetDetalis.password_confirmation != ""
      ) {
        this.loader = true;
        axios
          .post("/password/reset", this.resetDetalis)
          .then((response) => {
            if (response.status === 200) {
              // console.log(response);
              window.location.replace("/");
            }
          })
          .catch((error) => {
            if (error.response.status === 422) {
              // console.log(error.response);
              this.Error = true;
              if (error.response.data.email) {
                this.emailError = true;
              }
              if (error.response.data.password) {
                this.passwordError = true;
              }
            }
          });
      } else {
        this.resetError = true;
        this.loader = false;
      } 
    },
  },
};
</script>
