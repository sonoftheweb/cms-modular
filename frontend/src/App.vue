<template>
  <div>
    <con-alerts :alert-data="alertData" class=""/>
    <v-app>
      <con-app-bar v-if="authenticated"/>
      <con-menu v-if="authenticated"/>
      <v-content>
        <v-container fluid class="pa-10" :class="{'fill-height': !authenticated}">
          <router-view></router-view>
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
};
</script>
