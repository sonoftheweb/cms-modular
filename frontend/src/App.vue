<template>
  <div>
    <con-alerts :alert-data="alertData" class=""/>
    <v-app>
      <con-app-bar/>
      <con-menu v-if="authenticated"/>
      <v-content>
        <v-container class="pa-10" :class="containerClass()">
          <transition name="fade" mode="out-in">
            <router-view></router-view>
          </transition>
        </v-container>
      </v-content>
    </v-app>
  </div>
</template>

<script>
import ConAlerts from './components/utils/ConAlerts'
import ConAppBar from './components/navigation/ConAppBar'
import ConMenu from './components/navigation/ConMenu'
import { mapGetters } from 'vuex'

export default {
  components: {
    ConAlerts,
    ConAppBar,
    ConMenu
  },
  computed: {
    ...mapGetters(['authenticated'])
  },
  data: () => ({
    alertData: {}
  }),
  methods: {
    containerClass() {
      let className = ''
      if (!this.authenticated)
        className = 'fill-height container'
  
      if (this.$route.name === 'registration')
        className = ''
      
      return className
    }
  },
  mounted() {
    this.$eventBus.$on('alert', alertData => {
      this.alertData = alertData
    })
  }
};
</script>

<style scoped lang="scss">
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 1s;
  }
  
  .fade-enter,
  .fade-leave {
    opacity: 0;
  }
</style>
