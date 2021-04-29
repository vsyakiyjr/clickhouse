import axios from 'axios'

let headers = {
  'X-Requested-With': 'XMLHttpRequest'
}

let token = document.head.querySelector('meta[name="csrf-token"]')
if (!token) {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

if (token) {
  headers['X-CSRF-TOKEN'] = token.content
}

let user = JSON.parse(window.localStorage.getItem('user'))
if (user) {
  headers['Authorization'] = 'Bearer ' + user.api_token
}

export default axios.create({
  timeout: 200000,
  headers: headers,
  withCredentials: true,
  validateStatus: function (status) {
    return status >= 200 && status < 300; // default
  },
});
