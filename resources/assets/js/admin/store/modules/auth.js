import axios from '@/utils/axios'

// initial state
const state = {
  user: JSON.parse(window.localStorage.getItem('user'))
}
// getters
const getters = {
  user: state => state.user,
}

// actions
const actions = {
}

// mutations
const mutations = {
}

export default {
  state,
  getters,
  actions,
  mutations
}
