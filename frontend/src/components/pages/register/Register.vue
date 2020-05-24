<template>
  <div>
    <con-title/>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    
    <v-row>
      <v-col v-for="(product, index) in products" :key="index" md="4">
        <v-card :elevation="elevationById(product.id)">
          <v-card-title class="align-center">
            {{ product.name }}
          </v-card-title>
  
          <!--<v-card-text>
            <v-btn
              class="indigo lighten-1"
              v-if="Object.prototype.hasOwnProperty.call(product.metadata, 'has_monthly')"
              dark
              block
            >
              Choose Monthly
            </v-btn>
            <div style="clear: both"></div>
            <v-btn
              class="indigo darken-1 mt-3"
              v-if="Object.prototype.hasOwnProperty.call(product.metadata, 'has_annually')"
              dark
              block
            >
              Choose Annually
            </v-btn>
            <v-btn
              class="indigo darken-1 mt-3"
              v-if="!Object.prototype.hasOwnProperty.call(product.metadata, 'has_annually') && !Object.prototype.hasOwnProperty.call(product.metadata, 'has_monthly')"
              dark
              block
            >
              Register
            </v-btn>
          </v-card-text>-->
  
          <v-divider></v-divider>
          <v-list-item two-line>
            <v-list-item-content>
              <v-list-item-title v-if="product.metadata.feature_tier === '0'">{{ product.metadata.max_seats }} admin user</v-list-item-title>
              <v-list-item-title v-else >Up to {{ product.metadata.max_seats }} admin users per account</v-list-item-title>
              <v-list-item-subtitle v-if="product.metadata.feature_tier === '0'">Maximum number of seats available for this plan</v-list-item-subtitle>
              <v-list-item-subtitle v-else>Initially comes with {{ product.metadata.min_seats }} seats. Extra costs applies.</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          
          <v-divider></v-divider>
          
          <div v-if="product.metadata.feature_tier === '0'">
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Create and manage up to 5 contracts</v-list-item-title>
                <v-list-item-subtitle>Manage contracts with client support.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Manage up to 5 clients</v-list-item-title>
                <v-list-item-subtitle>Manage up to 5 clients.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Live contract collaboration</v-list-item-title>
                <v-list-item-subtitle>Contract live, realtime updates.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
          </div>
  
          <div v-if="product.metadata.feature_tier === '1'">
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Create and manage up to 50 contracts / month</v-list-item-title>
                <v-list-item-subtitle>Manage contracts with client support.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Manage as many clients as you have</v-list-item-title>
                <v-list-item-subtitle>Manage unlimited clients.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Live contract collaboration</v-list-item-title>
                <v-list-item-subtitle>Contract live, realtime updates.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Spell checker enabled</v-list-item-title>
                <v-list-item-subtitle>Loose those typos and grammatical errors.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Extended editor</v-list-item-title>
                <v-list-item-subtitle>Build out fanciful contracts. No more boring stuff.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Contract template management</v-list-item-title>
                <v-list-item-subtitle>Store up templates and call them up when needed.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
          </div>
  
          <div v-if="product.metadata.feature_tier === '2'">
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Create and manage up to 100 contracts / month</v-list-item-title>
                <v-list-item-subtitle>Manage contracts with client support.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Manage as many clients as you have</v-list-item-title>
                <v-list-item-subtitle>Manage unlimited clients.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Live contract collaboration</v-list-item-title>
                <v-list-item-subtitle>Contract live, realtime updates.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Spell checker enabled</v-list-item-title>
                <v-list-item-subtitle>Loose those typos and grammatical errors.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Contract Buddy AI</v-list-item-title>
                <v-list-item-subtitle>Reviews all your contracts and informs you of issues.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Extended editor</v-list-item-title>
                <v-list-item-subtitle>Build out fanciful contracts. No more boring stuff.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Contract template management</v-list-item-title>
                <v-list-item-subtitle>Store up templates and call them up when needed.</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>And much... much more!</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
          </div>
          
          <v-card-text>
            <v-btn
              class="indigo lighten-1"
              v-if="Object.prototype.hasOwnProperty.call(product.metadata, 'has_monthly')"
              dark
              block
            >
              Begin Monthly Registration
            </v-btn>
            <div style="clear: both"></div>
            <v-btn
              class="indigo darken-1 mt-3"
              v-if="Object.prototype.hasOwnProperty.call(product.metadata, 'has_annually')"
              dark
              block
            >
              Begin Annual Registration
            </v-btn>
            <v-btn
              class="indigo darken-1 mt-3"
              v-if="!Object.prototype.hasOwnProperty.call(product.metadata, 'has_annually') && !Object.prototype.hasOwnProperty.call(product.metadata, 'has_monthly')"
              dark
              block
            >
              Register
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
  import ConTitle from '../../utils/ConTitle'
  export default {
    components: {
      ConTitle
    },
    data() {
      return {
        products: [],
        selected: {
          product_id: null,
          plan: {}
        }
      }
    },
    methods: {
      elevationById(id) {
        return Math.round(this.products.length / id)
      },
      fetchProducts() {
        this.$http.get('/api/products').then(response => {
          this.products = response.data
        })
      },
      productMeta(id) {
        console.log(id)
      }
    },
    mounted() {
      this.fetchProducts()
    }
  }
</script>

<style scoped>

</style>