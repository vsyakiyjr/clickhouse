var table = {
  data() {
    return {
      search: "",
      pagination: {},
      timer: false,
    }
  },
  watch: {
    pagination() {
      this.refresh()
    },
    search() {
      clearInterval(this.timer)
      this.timer = setTimeout(this.refresh, 500)
    }
  }
}

export default table;
