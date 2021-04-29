<template>
  <v-toolbar app>
    <v-toolbar-side-icon @click="toggleDrawer"></v-toolbar-side-icon>
    <v-spacer></v-spacer>
    
    <v-toolbar-items>
      <v-btn color="error" flat @click="logout">Выход</v-btn>
    </v-toolbar-items>
    
  </v-toolbar>
</template>

<script>
  import axios from 'axios'
  import { mapActions } from 'vuex'
  
  export default {
    name: 'Toolbar',
    methods: {
      ...mapActions(['toggleDrawer', 'openModal']),
      logout() {
        axios.post('/logout').then(() => {
          window.location = "/"
        })
      }
    }
  }
</script>
