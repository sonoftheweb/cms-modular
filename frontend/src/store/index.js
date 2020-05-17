import Vue from 'vue'
import Vuex from 'vuex'
import modules from './modules'
import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    openRequests: 0
  },
  modules,
  mutations: {
    putSettingsInStore(state, data) {
      for (let config in data) {
        let configValue = data[config]
        if (Object.prototype.hasOwnProperty.call(state, config)) {
          Object.keys(configValue).forEach(conf => {
            state[config][conf] = configValue[conf]
          })
        } else {
          state[config] = configValue && configValue.data ? configValue.data : configValue
        }
      }
    },
    deleteUser(state) {
      this.replaceState({})
      let curState = state
      curState.user = {}
      this.replaceState(curState)
    },
    updateState(state) {
      // Force a state refresh to put it to an empty object {} and then send it back to the previous state.
      // Same solution used than in the deleteUser function
      let curState = state
      this.replaceState({})
      this.replaceState(curState)
    }
  },
  actions: {
    bootstrapApp(context) {
      return new Promise((resolve) => {
        axios.get('/api/app').then(response => {
          context.commit('putSettingsInStore', response.data)
          if (context.getters.needsUserData) {
            context.dispatch('fetchUserData').then(() => {
              resolve()
            })
          } else {
            resolve()
          }
        }).catch(err => {
          if (err.response && err.response.status !== 200) {
            // @todo do a router push to an error page here
            return
          }
          context.dispatch('loggedOut').then(() => {
            window.location.replace('/')
            return
          })
        })
      })
    }
  },
  getters: {
    authenticated: state => state.user.authenticated,
    user: state => state.user,
    isLoggedIn: state => Object.prototype.hasOwnProperty.call(state.user, 'id'),
    subscribed: state => state.user.subscribed,
    needsUserData: state => {
      return !state.user.dataset && state.user.authenticated
    },
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
        return new Intl.NumberFormat('en-US', {style: 'currency', currency: 'USD'}).format(price)
      }
    },
    firstLetterCaps: () => {
      return string => {
        return string.charAt(0).toUpperCase() + string.slice(1)
      }
    }
  }
})