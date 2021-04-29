<template>
  <div class="socials-wrapper">
    <span class="socials-wrapper-title">Войти через социальную сеть</span>
    <div class="socials">
      <div class="social" v-for="(social, index) in socials" :key="index" @click="onClick(social)">
        <span :class="{[social.icon] : true}"></span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "social-auth",

  data() {
    return {
      props: {
        type: {
          type: String,
          validator: val => ["login", "register"].includes(val)
        }
      },
      socials: [
        {
          provider: "facebook",
          icon: "facebook-square"
        },
        {
          provider: "vkontakte",
          icon: "vk-icon"
        },
        {
          provider: "google",
          icon: "google-plus-square"
        }
      ]
    };
  },

  created() {},

  methods: {
    onClick(social) {
      axios.post(`/socialite/redirect`, social).then(({ data: { url } }) => {
        window.location = url;
      });
    }
  }
};
</script>