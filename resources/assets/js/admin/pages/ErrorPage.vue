<template>
  <v-jumbotron color="primary" dark>
    <v-container fill-height>
      <v-layout align-center>
        <v-flex text-xs-center>
          <h3 class="display-3">Тупик...</h3>
          <v-btn outline color="white" to="/home">Вернуться к началу</v-btn>
        </v-flex>
      </v-layout>
    </v-container>
  </v-jumbotron>
</template>

<script>
  export default {
    name: 'ErrorPage',
  }
</script>
