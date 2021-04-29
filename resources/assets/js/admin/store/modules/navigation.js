// initial state
const state = {
  drawer: window.localStorage.getItem('drawer') == "true",
}
// getters
const getters = {
  drawer: state => state.drawer,
}

// actions
const actions = {
  toggleDrawer({ commit }) {
    commit('toggleDrawer')
  }
}

// mutations
const mutations = {
  toggleDrawer(state) {
    window.localStorage.setItem('drawer', !state.drawer)
    state.drawer = !state.drawer
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}
