// initial state
const state = {
  loading: false,
}
// getters
const getters = {
  loading: state => state.loading,
}

// actions
const actions = {}

// mutations
const mutations = {
  loading(state, loading){
    state.loading = loading;
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}
