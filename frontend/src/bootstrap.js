import Vue from 'vue'
import lodash from 'lodash'
import axios from 'axios'
import Cookies from 'js-cookie'
import * as router from 'vue-router'
import v_router from 'vue-router'
import store from './store'

const eventBus = new Vue();

axios.defaults.baseURL = 'http://api.fortcon.local'
axios.defaults.withCredentials = true

Vue.prototype.$http = axios
Vue.prototype.$bus = eventBus;
Vue.prototype._ = lodash

axios.get("/sanctum/csrf-cookie").then(response => {
    console.log(response);
})

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.withCredentials = true

axios.interceptors.request.use(config => {
    // when a request is sent
    eventBus.$emit('toggle-loading', true)

    // If token exist as a cookie add it to request
    if (Cookies.get('token')) {
        config.headers.common['Authorization'] = Cookies.get('token')
    }

    return config
}, error => {
    return Promise.reject(error)
})

axios.interceptors.response.use(response => {
    // request yielded a response, hide the loading bar
    eventBus.$emit('toggle-loading', false )

    return response
}, error => {
    // request yielded a response (error), hide the loading bar
    eventBus.$emit('toggle-loading', false )

    let data = error.response.data

    // if error.response is 404 and the message states that no account is set
    if (error.response.status === 404 && Object.prototype.hasOwnProperty.call(data, 'no-account')) {
        // show an error and ask them to contact admin.
        console.log('show "please contact admin"')
    }

    // if error.response.status is 400 and the message states that the account has no subscription
    if (error.response.status === 404 && Object.prototype.hasOwnProperty.call(data, 'no-subscription') && !['subscription'].includes(v_router.name)) {
        // take them to the subscription page and to complete subscription payment
        console.log('go to subscription page')
    }

    // you do not have access to the application hence we clean all cookies and log you out
    if (error.response.status === 401) {
        store.dispatch('loggedOut').then(() => {
            router.push("/login")
        })
        return
    }

    return Promise.reject(error)
})
