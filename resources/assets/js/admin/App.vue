<template>
  <v-app>
    <Navigation app />
    <Toolbar app/>
    <v-content>
      <v-container fluid>
        <router-view></router-view>
      </v-container>
    </v-content>

    <transition name="fade"
                mode="out-in">
      <div class="loader" v-show="loading">
        Загрузка
      </div>
    </transition>

  </v-app>
</template>

<script>
  import Toolbar from "@/components/Toolbar"
  import Navigation from "@/components/Navigation"

  export default {
    name: 'App',
    components: {
      Toolbar: Toolbar,
      Navigation: Navigation,
    },
    computed: {
      loading(){
        return this.$store.getters.loading;
      }
    }
  }
</script>

<style>
  .loader {
    background-color: rgba(0, 0, 0, 0.5);
    position: absolute;
    z-index: 100000;
    bottom: 0;
    right: 0;
    left: 0;
    top: 0;

    padding-top: 300px;
    text-align: center;
    font-size: 50px;
    color: rgba(0, 0, 0, 0.4);
  }
</style>
