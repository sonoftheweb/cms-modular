import Vue from 'vue'
import lodash from 'lodash'
import axios from 'axios'
import Cookies from 'js-cookie'
import router from './router/router'
import store from './store/index'

const $eventBus = new Vue();

axios.defaults.baseURL = 'http://api.fortcon.local'
axios.defaults.withCredentials = true

Vue.prototype.$http = axios
Vue.prototype.$eventBus = $eventBus;
Vue.prototype._ = lodash

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.withCredentials = true

axios.interceptors.request.use(config => {
    // when a request is sent
    $eventBus.$emit('toggle-loading', true)

    // If token exist as a cookie add it to request
    if (Cookies.get('token')) {
        config.headers.common['Authorization'] = 'Bearer ' + Cookies.get('token')
    }

    return config
}, error => {
    return Promise.reject(error)
})

axios.interceptors.response.use(response => {
    // request yielded a response, hide the loading bar
    $eventBus.$emit('toggle-loading', false )

    // if error.response.status is 404 and the message states that the account has no subscription
    if (Object.prototype.hasOwnProperty.call(response.data, 'error_type') && response.data.error_type === 'has_no_subscription') {
        // take them to the subscription page and to complete subscription payment
        store.commit('subscribed', false)
        router.push('/subscription')
        $eventBus.$emit('toggle-menu')
        $eventBus.$emit('alert', {
            status: 'warning',
            message: 'Your account does not have a plan associated with it. To use FortCon, you will need to subscribe to a plan. Review the plans below and select the plan that suites your needs.'
        })
    }

    return response
}, error => {
    // request yielded a response (error), hide the loading bar
    $eventBus.$emit('toggle-loading', false )

    let data = error.response.data

    // if error.response is 404 and the message states that no account is set
    if (error.response.status === 404 && Object.prototype.hasOwnProperty.call(data, 'no-account')) {
        // show an error and ask them to contact admin.
        $eventBus.$emit({
            status: 'error',
            message: 'You have no account. Please contact the admin.'
        })
    }

    // you do not have access to the application hence we clean all cookies and log you out
    /*if (error.response.status === 401) {
        store.dispatch('loggedOut').then(() => {
            window.location.replace("/")
        })
        return
    }*/
    
    if (error.response.status === 401 && Object.prototype.hasOwnProperty.call(data, 'message') && data.message === 'Unauthenticated') {
        store.dispatch('loggedOut').then(() => {
            window.location.replace("/")
        })
        return
    }
    
    $eventBus.$emit({
        status: 'error',
        message: 'Something went wrong.'
    })

    return Promise.reject(error)
})
