<template>
  <v-dialog v-model="display" persistent max-width="600px">
    <v-card>
      <v-card-title>
        <span class="headline">Payment</span>
      </v-card-title>
      <v-card-text>
        <v-progress-linear
          :active="loading"
          color="indigo accent-4"
          indeterminate
          rounded
          height="2"
        ></v-progress-linear>

        <div>
          <p>
            You have selected the <strong>{{ paymentMethodData.product_name }}</strong>. Your payment interval will be <strong>{{ selectedInterval }}ly</strong>.
            Please fill in the form below to process payment for your account.
          </p>

          <v-divider class="my-3 mx-10"></v-divider>

          <v-form ref="_make_payment">

            <div class="summary text-center my-7">
              <div class="headline">{{ paymentMethodData.product_name }}</div>
              <div>{{ price(paymentMethodData.price) }} per {{ selectedInterval }} for {{ paymentMethodData.seats }} seat{{ (paymentMethodData.seats > 1) ? 's' : '' }}.
              </div>
            </div>

            <div id="card-errors" role="alert"></div>
            <v-row>
              <v-col md="6" sm="12">
                How many seats do you plan to have available within this account?
              </v-col>
              <v-col md="6" sm="12">
                <v-text-field
                  min="1"
                  :max="selectedProduct.metadata.max_seats"
                  type="number"
                  v-model="seatsBuilder"
                  outlined label="Seats"
                  placeholder="1"
                />
              </v-col>
            </v-row>
            <v-row class="mb-0 pb-0">
              <v-col cols="12" md="6" sm="12">
                <v-text-field v-model="paymentMethodData.name" outlined label="Name" :rules="requiredFieldRule"
                              placeholder="John Doe"></v-text-field>
              </v-col>
              <v-col cols="12" md="6" sm="12">
                <v-text-field v-model="paymentMethodData.email" outlined label="Email address" :rules="emailValidationRules"
                              placeholder="john.doe@email.com"></v-text-field>
              </v-col>
            </v-row>

            <small>Note that this card will be billed per tenure of the plan selected.</small>
            <div class="mimic-input mb-5 mt-0">
              <div id="card"></div>
            </div>
          </v-form>
        </div>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          :loading="loading"
          color="indigo darken-1"
          text
          @click="makePayment">
          Make Payment
        </v-btn>
        <v-btn v-if="!loading" color="indigo darken-1" text @click="closePayment">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  /* global Stripe */
  import { mapGetters } from 'vuex'

  export default {
    props: {
      display: {type: Boolean, default: false},
      selectedProduct: {type: Object, default: null},
      selectedInterval: {type: String, default: null},
    },
    computed: {
      ...mapGetters(['priceFormatted', 'emailValidationRules', 'requiredFieldRule'])
    },
    watch: {
      seatsBuilder(newVal) {
        this.paymentMethodData.seats = newVal
        this.buildPaymentContext()
      }
    },
    data() {
      return {
        seatsBuilder: 1,
        loading: false,
        stripe: null,
        stripeStyle: {
          base: {
            color: "#32325d",
            fontSmoothing: "antialiased",
            fontSize: "16px",
            "::placeholder": {
              color: "#aab7c4"
            }
          },
          invalid: {
            color: "#fa755a",
            iconColor: "#fa755a"
          },
        },
        element: null,
        card: null,
        paymentIntent: null,
        paymentMethods: [],
        paymentMethodData: {
          name: null,
          email: null,
          seats: 1,
          price: 0, // cents
          plan_id: null, // plan identifier
          plan: null,
          product_name: null,
          product_id: null, // prod identifier
        },
        addPaymentMethod: false
      }
    },
    methods: {
      buildPaymentContext() {
        // lets get the plan from product
        this.paymentMethodData.product_name = this.selectedProduct.name
        this.paymentMethodData.product_id = this.selectedProduct.stripe_product_identifier
        let filteredPlan = this.selectedProduct.plans.filter(plan => {
          return plan.interval === this.selectedInterval
        })
        this.paymentMethodData.plan_id = filteredPlan[0].pid
        this.paymentMethodData.plan = filteredPlan[0]
        this.paymentMethodData.price = this.calculatePrice()
      },
      calculatePrice() {
        let tier_one = this.paymentMethodData.plan.tiers[0],
          tier_two = this.paymentMethodData.plan.tiers[1],
          calcPrice = 0
        
        if (this.paymentMethodData.seats <= tier_one.up_to) {
          calcPrice = this.paymentMethodData.seats * tier_one.flat_amount
        } else {
          calcPrice = tier_one.up_to * tier_one.flat_amount
          calcPrice += (this.paymentMethodData.seats - tier_one.up_to) * tier_two.unit_amount
        }
        
        return calcPrice
      },
      price(cents) {
        const formatter = new Intl.NumberFormat('en-US', {
          style: 'currency',
          currency: 'USD',
          minimumFractionDigits: 2
        })
        return formatter.format(cents / 100)
      },
      closePayment() {
        this.$emit('close_payment_dialog')
      },
      getIntent() {
        this.loading = true
        this.$http.get('/api/payment?getIntent').then((response) => {
          this.paymentIntent = response.data.intent
          this.loading = false

          this.mountStripe()
        }).catch(() => {
          this.loading = false
          this.closePayment()
          this.$eventBus.$emit('alert', {
            displayAlert: 'error',
            message: 'Something went bang! Contact support ASAP!'
          })
        })
      },
      async mountStripe() {
        this.card.mount('#card')
        this.card.addEventListener('change', ({error}) => {
          const displayError = document.getElementById('card-errors')
          if (error) {
            displayError.textContent = error.message
          } else {
            displayError.textContent = ''
          }
        })
      },
      async makePayment() {
        this.loading = true
        if (this.$refs._make_payment.validate()) {
          const { setupIntent, error } = await this.stripe.confirmCardSetup(
            this.paymentIntent.client_secret, {
              payment_method: {
                card: this.card,
                billing_details: {
                  name: this.paymentMethodData.name,
                  email: this.paymentMethodData.email
                }
              }
            }
          )

          if (error) {
            this.loading = false
          } else {
            // complete the subscription process
            let data = {
              pm: setupIntent.payment_method,
            }

            this.$http.post('/api/payment?spm', data).then(response => {
              this.paymentMethods = response.data.pm
              let data = {
                pm: this.paymentMethods[0],
                prod_id: this.paymentMethodData.product_id,
                plan_id: this.paymentMethodData.plan_id,
                seat_count: this.paymentMethodData.seats
              }

              this.$http.post('/api/payment?subscribe', data)
                .then(response => {
                  this.loading = true // keep loading at true
                  this.$eventBus.$emit('alert', response.data)
                  window.location.replace('/home')
                })
                .catch(err => {
                  this.loading = false
                  console.log(err)
                })
            }).catch(err => {
              this.loading = false
              console.log(err)
            })
          }
        }
      }
    },
    mounted() {
      this.stripe = Stripe(process.env.VUE_APP_STRIPE_KEY)
      this.elements = this.stripe.elements()
      this.card = this.elements.create('card')
      this.getIntent()
      this.buildPaymentContext()
    }
  }
</script>

<style scoped>
  .mimic-input {
    padding: 20px 12px;
    border: 1px solid rgba(16, 16, 16, 0.14);

    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
  }
</style>