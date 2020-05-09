<template>
  <div>
    <con-page-header/>
    <div v-if="!loading && Object.keys(currentSubscription).length">
      <v-row>
        <v-col md="3" sm="12">
          <v-card flat class="px-0 mx-0">
            <v-card-text class="px-0 mx-0">
              <div class="title my-5">Your current subscription</div>
              <div class="font-weight-bold">
                {{ firstLetterCaps(currentSubscription.plan.nickname) }} Plan
              </div>
              <div>
                <v-icon class="mr-2 my-3">mdi-account-supervisor-circle</v-icon>
                {{ currentSubscription.quantity }} seats
              </div>
              <div v-if="currentSubscription.collection_method === 'charge_automatically'">
                <v-icon class="mr-2 mb-3">mdi-calendar-sync</v-icon>
                Auto renews on
              </div>
              <div>
                <v-icon class="mr-2 mb-3">mdi-cash-multiple</v-icon>
                {{ priceFormatted(computedPrice()) }} billable on renewal
              </div>
              <div>
                <v-icon class="mr-2 mb-3">mdi-clock-start</v-icon>
                {{ computedDate(currentSubscription.current_period_start * 1000) }}
              </div>
              <div>
                <v-icon class="mr-2 mb-3">mdi-clock-end</v-icon>
                {{ computedDate(currentSubscription.current_period_end * 1000) }}
              </div>
              <div v-if="currentSubscription.cancel_at">
                <v-icon class="mr-2 mb-3">mdi-table-cancel</v-icon>
                {{ computedDate(currentSubscription.cancel_at * 1000) }}
              </div>

              <v-btn depressed color="warning" class="mt-5" @click="cancelSubs(false)">
                <v-icon class="mr-2">mdi-alert-box-outline</v-icon>
                Cancel subscription
              </v-btn>

              <div v-if="previousSubscriptions.length">
                <div class="title mt-10 mb-5">Previous subscriptions</div>
                <v-data-table
                  :headers="subscriptionHeader"
                  :items="previousSubscriptions"
                  :items-per-page="5"
                  class="elevation-1"
                >
                  <template v-slot:item.status="{ item }">
                    <v-chip small :color="getColor(item.status)" dark>{{ item.status }}</v-chip>
                  </template>
                </v-data-table>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col md="6" sm="12">
          <v-card class="py-0" flat>
            <v-card-text>
              <div class="title my-5">Update Seats</div>
              <p>You may add seats to your account or reduce the number of seats you have available. Please note that only seats not occupied will be removed.</p>
              <v-row>
                <v-col md="5" sm="12">
                  <v-text-field outlined min="1" type="number" v-model="seats" label="Seats"
                                placeholder="1"></v-text-field>
                </v-col>
                <v-col md="7" sm="12">
                  <div class="mt-2">
                    <span class="title">{{ priceFormatted(additionalCostCalc(currentSubscription)) }}</span>
                    <span v-if="additionalCostCalc(currentSubscription) > 0"> will be added</span>
                    <span v-if="additionalCostCalc(currentSubscription) < 0"> will be deducted</span>
                  </div>
                </v-col>
              </v-row>
              <v-btn depressed :disabled="additionalCostCalc(currentSubscription) === 0" color="success" @click="updateSeats(false)">
                <v-icon class="mr-2">mdi-playlist-check</v-icon>
                Save
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>
  </div>
</template>

<script>
  import {mapGetters} from 'vuex'
  import ConPageHeader from '../../utils/ConPageHeader'

  export default {
    components: {
      ConPageHeader
    },
    data() {
      return {
        loading: true,
        currentSubscription: {},
        subscriptionHeader: [
          { text: 'Plan', align: 'start', sortable: false, value: 'plan_name' },
          { text: 'Start', value: 'start', sortable: true },
          { text: 'End', value: 'end', sortable: true },
          { text: 'Status', value: 'status', sortable: false },
        ],
        previousSubscriptions: [],
        seats: 0
      }
    },
    computed: {
      ...mapGetters(['priceFormatted', 'firstLetterCaps'])
    },
    methods: {
      additionalCostCalc(subscription) {
        subscription = (subscription) ? subscription : this.currentSubscription
        let price = subscription.plan.tiers[1].unit_amount / 100
        return price * (this.seats - this.currentSubscription.quantity)
      },
      computedPrice(subscription) {
        subscription = (subscription) ? subscription : this.currentSubscription
        let price = subscription.plan.tiers[0].unit_amount / 100
        if (subscription.quantity > subscription.plan.tiers[0].up_to) {
          price += (subscription.plan.tiers[1].unit_amount / 100) * (subscription.quantity - subscription.plan.tiers[0].up_to)
        } else {
          price = subscription.plan.tiers[0].unit_amount / 100
        }
        return price
      },
      getSubs() {
        if (!this.loading) this.loading = true
        this.$http.get('/api/subscription').then(response => {
          this.currentSubscription = response.data.subscription
          this.seats = this.currentSubscription.quantity
          this.previousSubscriptions = response.data.all_subscriptions.map((subs) => {
            return {
              plan_name: `${this.firstLetterCaps(subs.plan.nickname)} plan, ${subs.quantity} seats, ${this.priceFormatted(this.computedPrice(subs))}`,
              start: this.computedDate(subs.current_period_start * 1000),
              end: this.computedDate(subs.current_period_end * 1000),
              status: subs.status,
            }
          })
          this.loading = false
        })
      },
      computedDate(timestamp) {
        let date = new Date(timestamp)
        return `${date.toDateString()} ${date.getHours()}:${date.getMinutes()}:${date.getSeconds()}`
      },
      getColor (status) {
        if (status === 'active') return 'success'
        else return 'warning'
      },
      cancelSubs(confirmed, indefinite) {
        // @todo add indefinite flag
        indefinite = indefinite || false
        if (!confirmed) {
          let bodyMessage = 'Are you sure you want to cancel this subscription?'
          bodyMessage += (indefinite) ? 'Note that this will hinder your access to any resource you have already in the application.' : ' Note that the subscription will remain valid till the end of it\'s tenure, but you will not be billed again.'
          this.$store.dispatch('askConfirmation', {
            title: 'Confirm action',
            body: bodyMessage,
          }).then(confirmation => {
            if (confirmation)
              this.cancelSubs(true, indefinite)
          })
        } else {
          let data = {}
          if (indefinite) {
            data.cancel_now = true
          }
          this.$http.post('/api/subscription/cancel', data).then(response => {
            this.$eventBus.$emit('alert', response.data)
            if (indefinite)
              this.$store.dispatch('loggedOut')
          })
        }
      },
      updateSeats(confirmed) {
        if (!confirmed) {
          this.$store.dispatch('askConfirmation', {
            title: 'Confirm action',
            body: 'This action will alter your subscription by ' + this.priceFormatted(this.additionalCostCalc(this.currentSubscription)) + '. Do you wish to continue?'
          }).then(confirmation => {
            if (confirmation)
              this.updateSeats(true)
          })
        }
        else {
          this.$http.post('/api/subscription/update', {seats: this.seats}).then(response => {
            this.$eventBus.$emit('alert', response.data)
            this.getSubs()
          })
        }
      }
    },
    mounted() {
      this.getSubs()
    }
  }
</script>

<style scoped>

</style>