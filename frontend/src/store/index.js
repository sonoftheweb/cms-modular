import Vue from 'vue'
import Vuex from 'vuex'
import modules from './modules'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        instance: {},
        subscription: {}
    },
    modules,
    mutations: {},
    actions: {},
    getters: {
        numericFieldRule: () => {
            return [
                (v) => !v || v === '' || new RegExp(/^-?[\d]+(\.[\d]+)?$/g).test(v) || "Please enter a numeric value",
            ]
        },
        requiredFieldRule: () => {
            return [
                (v) => !!v || "Please fill in this field.",
            ]
        },
        emailValidationRules: () => {
            return [
                (v) => !!v || "Please fill in your email address",
                (v) => /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@(([[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v) || "Please enter a valid email"
            ]
        },
        priceFormatted: () => {
            return price => {
                return new Intl.NumberFormat('en-US', {style: 'currency', currency: 'USD'}).format(price);
            }
        }
    }
})