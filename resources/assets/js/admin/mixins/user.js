import { mapGetters } from "vuex"

var user = {
  methods: {
    isLogged() {
      return user ? true : false
    },
    isAdmin() {
      return user ? user.role == "GOD" : false
    }
  },
  computed: {
    ...mapGetters(['user'])
  }
}

export default user;
