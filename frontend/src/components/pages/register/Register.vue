<template>
  <div v-if="!loading">
    <con-title/>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    
    <div class="my-7">
      <v-card flat class="d-flex justify-center">
        <div class="float-left mt-5 mr-4">Monthly Plans</div>
        <v-switch color="indigo" flat inset v-model="is_annual"/>
        <div class="float-right mt-5">Annual Plans</div>
      </v-card>
    </div>
    
    
    <div class="d-flex" :class="flexClass">
      <v-card
        class="mb-auto mx-auto"
        :width="cardWidth"
        v-for="(product, index) in products"
        :key="index"
        :elevation="elevationById(product.id)"
      >
        <v-card-title class="align-center">{{ product.name }}</v-card-title>
  
        <v-list-item class="indigo darken-2" dark dense v-if="product.statement_descriptor !== 'FORTCON_FREE'">
          <v-list-item-content>
            {{ displayInitialPrice(product) }}
          </v-list-item-content>
        </v-list-item>
  
        <v-list-item class="indigo darken-1" dark dense v-if="product.statement_descriptor !== 'FORTCON_FREE'">
          <v-list-item-content>
            {{ displayAdditionalPrice(product)}}
          </v-list-item-content>
        </v-list-item>
    
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
          <v-btn class="indigo lighten-1" :loading="loading" dark block @click="registerAccount(product)">Register</v-btn>
        </v-card-text>
      </v-card>
    </div>
    <con-registration-form :show="showRegisterForm" @close="showRegisterForm = false"/>
  </div>
</template>

<script>
  import ConTitle from '../../utils/ConTitle'
  import ConRegistrationForm from '../../forms/ConRegistrationForm'
  
  export default {
    components: {
      ConTitle,
      ConRegistrationForm
    },
    computed: {
      flexClass() {
        let flexClass = 'flex-row';
        switch (this.$vuetify.breakpoint.name) {
          case 'xs':
            flexClass = 'flex-column'
            break
          case 'sm':
            flexClass = 'flex-column'
            break
        }
        return flexClass
      },
      cardWidth() {
        let maxWidth = '32%'
        switch (this.$vuetify.breakpoint.name) {
          case 'xs':
            maxWidth = '100%'
            break
          case 'sm':
            maxWidth = '100%'
            break
        }
        return maxWidth
      },
    },
    data() {
      return {
        loading: true,
        products: [],
        selected: {
          product_id: null,
          plan: {}
        },
        is_annual: false,
        showRegisterForm: false
      }
    },
    methods: {
      displayInitialPrice(product) {
        let self = this,
          plan = product.plans.filter(function (p) {
            if (self.is_annual) {
              return p.interval === 'year'
            }
            else {
              return p.interval === 'month'
            }
          }),
          price_in_cents
        
        price_in_cents = plan[0].tiers[0].flat_amount * parseInt(product.metadata.min_seats)
        
        return `${this.price(price_in_cents)} for first ${product.metadata.min_seats} seats / ${plan[0].interval}`
      },
      displayAdditionalPrice(product) {
        let self = this,
          plan = product.plans.filter(function (p) {
            if (self.is_annual) {
              return p.interval === 'year'
            }
            else {
              return p.interval === 'month'
            }
          }),
          price_in_cents
        
        price_in_cents = plan[0].tiers[1].unit_amount * parseInt(product.metadata.min_seats)
  
        return `${this.price(price_in_cents)} / additional seats / ${plan[0].interval}`
      },
      price(cents) {
        const formatter = new Intl.NumberFormat('en-US', {
          style: 'currency',
          currency: 'USD',
          minimumFractionDigits: 2
        })
        return formatter.format(cents / 100)
      },
      elevationById(id) {
        return Math.round(this.products.length / id)
      },
      fetchProducts() {
        this.$http.get('/api/products').then(response => {
          this.products = response.data.data
          this.loading = false
        })
      }
    },
    mounted() {
      this.fetchProducts()
    }
  }
</script>

<style scoped>

</style>