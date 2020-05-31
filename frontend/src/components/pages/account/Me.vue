<template>
  <div>
    <con-page-header/>
    
    <v-row>
      <v-col sm="12" md="12" lg="6">
        <v-card v-if="userData">
          <v-card-title>
            {{ userData.name }}
            <v-spacer></v-spacer>
            <v-avatar color="indigo" size="38px">
              <span class="white--text headline" v-if="!userData.attribute.profile_picture">{{ userData.name.split(" ").map((n)=>n[0]).join("") }}</span>
              <img v-else :alt="userData.name" :src="userData.attribute.profile_picture">
            </v-avatar>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            
            <v-card class="d-flex flex-row" flat>
              <v-card class="pr-2 pt-2 pb-2" min-width="200px" flat tile>
                <v-icon class="mr-5">mdi-at</v-icon>
                Email
              </v-card>
              <v-card class="pa-2" flat tile>{{ userData.email }}</v-card>
            </v-card>
            
            <v-card class="d-flex flex-row" flat>
              <v-card class="pr-2 pt-2 pb-2" min-width="200px" flat tile>
                <v-icon class="mr-5">mdi-camera-timer</v-icon>
                Status
              </v-card>
              <v-card class="pa-2" flat tile>{{ userData.status }}</v-card>
            </v-card>
            
            <v-card class="d-flex flex-row" flat>
              <v-card class="pr-2 pt-2 pb-2" min-width="200px" flat tile>
                <v-icon class="mr-5">mdi-file-tree</v-icon>
                Job title
              </v-card>
              <v-card class="pa-2" flat tile>{{ userData.attribute.user_job_title }}</v-card>
            </v-card>
            
            <v-card class="d-flex flex-row" flat>
              <v-card class="pr-2 pt-2 pb-2" min-width="200px" flat tile>
                <v-icon class="mr-5">mdi-card-text-outline</v-icon>
                Description
              </v-card>
              <v-card class="pa-2" flat tile>{{ userData.attribute.user_job_description }}</v-card>
            </v-card>
            
          </v-card-text>
          
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text color="indigo">Edit</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col sm="12" md="12" lg="6">
        <v-card>
          <v-card-title>
            Your recent activities
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text>
          
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import ConPageHeader from '../../utils/ConPageHeader'

  export default {
    components: {
      ConPageHeader
    },
    data() {
      return {
        userData: null
      }
    },
    computed: {
      ...mapGetters(['user'])
    },
    mounted() {
      console.log(this.user)
      this.userData = this.user
    }
  }
</script>

<style scoped>

</style>