import './bootstrap'
import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify'
import store from './store/index'
import router from './router/router'
import stripe from './stripe/stripe'

import './styles/styles.scss'

Vue.config.productionTip = false
stripe.initialize()
router.initialize()

new Vue({
  vuetify,
  router,
  store,
  stripe,
  render: h => h(App)
}).$mount('#app')
