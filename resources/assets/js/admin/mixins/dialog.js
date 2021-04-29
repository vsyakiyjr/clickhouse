var dialog = {
  data() {
    return {
      dialog: false,
      editedItem: false,
      defaultItem: {},
      errors: false,
    }
  },
  methods: {
    openDialog(item = this.defaultItem) {
      this.dialog = true
      this.editedItem = Object.assign({}, item)
    },
    closeDialog() {
      this.dialog = false
      this.editedItem = false
    },
    error(field){
      return this.errors ? this.errors[field] : []
    }
  }
}

export default dialog;
