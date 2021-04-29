<template>
  <div class="auth-modal">
    <div class="modal">
      <div class="modal-content">
        <div class="modal-close auth-close">
          <img src="/images/redesign/icons/modal-close.svg" alt="close">
        </div>
        <div class="auth-modal__header">
          <span @click="formType = 'login'" :class="{ active: formType == 'login' }"
            >Вход</span
          >
          <span>|</span>
          <span @click="formType = 'register'" :class="{ active: formType == 'register' }"
            >Регистрация</span
          >
        </div>
        <div class="form-group">
          <form
            v-if="formType == 'login'"
            class="login-form"
            method="POST"
            action="/login"
          >
          <div class="auth-modal__input">
            <input
              type="email"
              id="email"
              @focus="loginErrorsEmail = false"
              :class="{error: loginErrorsEmail}"
              placeholder="Email / Телефон"
              required
              v-model="loginDetalis.email"
            />
            <p v-if="loginErrorsEmail">{{ loginEmailError }}</p>
          </div>
          <div class="auth-modal__input">
            <input
              type="password"
              id="password"
              @focus="loginErrorsPassword = false"
              :class="{error: loginErrorsPassword}"
              placeholder="Пароль"
              required
              v-model="loginDetalis.password"
            />
            <p v-if="loginErrorsPassword">{{ loginPasswordError }}</p>
          </div>

            <button v-if="!loader" @click.prevent="loader=!loader; loginPost();">
              Войти
            </button>
            <button :class="{active:loader}" v-if="loader">
              <div class="loader"></div>
            </button>
            <div class="auth-modal__footer">
              <div class="auth-modal__col">
                <div>
                  <span @click="formType = 'password'">Восстановить пароль</span>
                </div>
                <div>
                  <span @click="formType = 'register'">Регистрация</span>
                </div>
              </div>
              <SocialsAuth></SocialsAuth>
            </div>
          </form>

          <form
            v-if="formType == 'register'"
            class="register-form"
            method="POST"
            action="/register"
          >
            <div class="auth-modal__input">
              <input
              type="email"
              id="email"
              autofocus
              :class="{error: registerErrorsEmail}"
              placeholder="Email"
              required
              v-model="registerDetalis.email"
            />
            <p v-if="registerErrorsEmail">{{ loginEmailError }}</p>
            </div>
            <div class="auth-modal__input">
              <input
              type="tel"
              id="phone"
              placeholder="Телефон"
              v-model="registerDetalis.phone"
            />
            <p v-if="registerErrorsPhone">{{ loginPhoneError }}</p>
            </div>
            <div class="auth-modal__input">
              <input
              type="password"
              id="password"
              :class="{error: registerErrorsPassword}"
              placeholder="Пароль"
              required
              v-model="registerDetalis.password"
            />
            <p v-if="registerErrorsPassword">{{ loginPasswordError }}</p>
            </div>
            <div class="auth-modal__input">
              <input
              type="password"
              id="password-confirm"
              name="password_confirmation"
              :class="{error: registerErrorsPassword}"
              required
              placeholder="Повторите пароль"
              v-model="registerDetalis.password_confirmation"
            />
            <p v-if="registerErrorsPassword">{{ loginPasswordError }}</p>
            </div>
            <div class="auth-modal__checkbox">
              <input type="checkbox" id="terms" value="1" required checked> 
              <label @click="registerDetalis.terms=!registerDetalis.terms" for="terms">Я даю согласие на <a href="#">обработку персональных данных</a></label>
            </div>
            <button v-if="!loader" @click.prevent="loader=!loader; registerPost();">
              Регистрация
            </button>
            <button :class="{active:loader}" v-if="loader">
              <div class="loader"></div>
            </button>
            <div class="auth-modal__footer footer-reg">
              <div class="socialReg">
                <SocialsAuth></SocialsAuth>
              </div>
              <div class="auth-modal__col reg-col">
                <p>Уже зарегестрированы?</p>
                <span @click="formType = 'login'">Войти</span>
              </div>
            </div>
          </form>
          <form
            v-if="formType == 'password' && passwordStep == 1"
            class="password-form"
            method="POST"
            action="/password/email"
          >
            <div class="auth-modal__input">
              <input
              type="email"
              id="email"
              placeholder="Email"
              required
              v-model="passwordDetalis.email"
            />
            </div>
            <button v-if="!loader" @click.prevent="loader=!loader; passwordPost();">
              Сбросить пароль
            </button>
            <button :class="{active:loader}" v-if="loader">
              <div class="loader"></div>
            </button>
            <div class="auth-modal__footer footer-reg">
              <div class="auth-modal__col reg-col">
                <p>Вспомнили пароль?</p>
                <span @click="formType = 'login'">Войти</span>
              </div>
            </div>
          </form>
          <div v-if="formType == 'password' && passwordStep == 2" class="step">
            <p>Ссылка на востановление пароля отправлена на ваш Email</p>
            <img src="/images/redesign/icons/modal-check.svg" alt="check">
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SocialsAuth from "./SocialsAuth.vue";
export default {
  name: "Auth",
  components: {
    SocialsAuth,
  },
  data: () => ({
    formType: 'login',
    loader: false,
    loginDetalis: {
      email: "",
      password: "",
      terms: false,
    },
    loginErrorsEmail: false,
    loginErrorsPassword: false,
    loginEmailError: "Введён неверный email",
    loginPasswordError: "Введён неверный пароль",

    registerDetalis: {
      email: "",
      phone: "",
      password: "",
      password_confirmation: "",
      terms: false,
    },
    registerErrorsEmail: false,
    registerErrorsPhone: false,
    registerErrorsPassword: false,
    registerEmailError: null,
    registerPhoneError: null,
    registerPasswordError: null,

    passwordDetalis: {
      email: ""
    },
    passwordStep: 1,
    passwordError: false,
    resetDetalis: {
      password: "",
      password_confirmation: ""
    },
  }),
  methods: {
    loginPost() {
      axios
        .post("/login", this.loginDetalis)
        .then((response) => {
          if (response.status === 200) {
            setTimeout(() => {
              this.loader = !this.loader;
              window.location.reload();
            }, 1000);
          }
        })
        .catch((error) => {
          // console.log(error.response);
          setTimeout(() => {
              this.loader = !this.loader;
          }, 1000);
          if (error.response.status === 422) {
            if (error.response.data.errors.email) {
              this.loginErrorsEmail = true;
            }
            if (error.response.data.errors.password) {
              this.loginErrorsPassword = true;
            }
          }
        });
    },
    registerPost() {
      axios
        .post("/register", this.registerDetalis)
        .then((response) => {
          if (response.status === 200) {
            setTimeout(() => {
              this.loader = !this.loader;
              window.location.reload();
            }, 1000);
          }
        })
        .catch((error) => {
          // console.log(error.response);
          setTimeout(() => {
              this.loader = !this.loader;
            }, 1000);
          if (error.response.status === 422) {
            if (error.response.data.errors.email) {
              this.registerErrorsEmail = true;
            }
            if (error.response.data.errors.password) {
              this.registerErrorsPassword = true;
            }
          }
        });
    },
    passwordPost() {
      axios.post('/password/email', this.passwordDetalis)
        .then(response => {
          if (response.status === 200) {
            this.passwordStep = 2;
            setTimeout(() => {
              window.location.reload();
            }, 4000);
          }
        })
        .catch(error => {
          if (error.response.status == 422) {
            this.passwordError = true;
          }
        })
    }
  }
};
</script>
