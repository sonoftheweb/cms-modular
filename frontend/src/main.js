import './bootstrap'
import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify'
import store from './store/index'
import router from './router/router'

Vue.config.productionTip = false
router.initialize().then(() => {
  console.info('Router Init');
})

new Vue({
  vuetify,
  router,
  store,
  render: h => h(App)
}).$mount('#app')
