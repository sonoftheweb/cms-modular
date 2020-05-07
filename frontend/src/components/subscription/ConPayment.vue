<template>
  <v-dialog v-model="display" persistent max-width="600px">
    <v-card>
      <v-card-title>
        <span class="headline">Plan Payment</span>
      </v-card-title>
      <v-card-text>
        <v-progress-linear
          :active="loading"
          color="deep-purple accent-4"
          indeterminate
          rounded
          height="2"
        ></v-progress-linear>

        <div>
          <p>
            You have selected the <strong>{{ selectedPlan.nickname }}</strong> plan.
            Please fill in the form below to process payment for your account.
          </p>

          <v-divider class="my-3 mx-10"></v-divider>

          <v-form ref="_make_payment">

            <div class="summary text-center my-7">
              <div class="headline">{{ selectedPlan.nickname.toUpperCase() }} Plan</div>
              <div>{{ priceFormatted(computedPrice) }} per
                {{ tenure() }}
                for {{ paymentMethodData.seats }} seat{{ (paymentMethodData.seats > 1) ? 's' : '' }}.
              </div>
            </div>

            <div id="card-errors" role="alert"></div>
            <v-row>
              <v-col md="6" sm="12">
                How many seats do you plan to have available within this account?
              </v-col>
              <v-col md="6" sm="12">
                <v-text-field min="0" type="number" v-model="paymentMethodData.seats" filled label="Seats"
                              placeholder="1"></v-text-field>
              </v-col>
            </v-row>
            <v-row class="mb-0 pb-0">
              <v-col cols="12" md="6" sm="12">
                <v-text-field v-model="paymentMethodData.name" filled label="Name" :rules="requiredFieldRule"
                              placeholder="John Doe"></v-text-field>
              </v-col>
              <v-col cols="12" md="6" sm="12">
                <v-text-field v-model="paymentMethodData.email" filled label="Email address" :rules="emailValidationRules"
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
  import {mapGetters} from 'vuex'

  export default {
    props: ["display", "selectedPlan"],
    computed: {
      ...mapGetters(['priceFormatted', 'emailValidationRules', 'requiredFieldRule']),
      computedPrice() {
        let price = this.selectedPlan.tiers[0].unit_amount / 100;
        if (this.paymentMethodData.seats > this.selectedPlan.tiers[0].up_to) {
          price += (this.selectedPlan.tiers[1].unit_amount / 100) * (this.paymentMethodData.seats - this.selectedPlan.tiers[0].up_to);
        } else {
          price = this.selectedPlan.tiers[0].unit_amount / 100;
        }
        return price;
      }
    },
    data() {
      return {
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
          seats: 1
        },
        addPaymentMethod: false
      }
    },
    methods: {
      tenure(relative) {
        if (relative)
          return (this.selectedPlan.nickname === 'monthly' ? 'per month' : 'annually')
        else
          return (this.selectedPlan.nickname === 'monthly' ? 'month' : 'year')
      },
      closePayment() {
        this.$emit('close_payment_dialog')
      },
      getIntent() {
        this.loading = true;
        this.$http.get('/api/payment/intent').then((response) => {
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
            displayError.textContent = error.message;
          } else {
            displayError.textContent = '';
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
                  name: this.paymentMethodData.name
                }
              }
            }
          )

          if (error) {
            this.loading = false;
          } else {
            // complete the subscription process
            let data = {
              payment_method: setupIntent.payment_method,
            };

            this.$http.post('/api/payment/savePaymentMethod', data).then(response => {
              this.paymentMethods = response.data.data.payment_methods;
              let data = {
                pm: this.paymentMethods[0],
                plan: this.selectedPlan.nickname,
                seat_count: this.paymentMethodData.seats
              };

              this.$http.post('/api/payment/subscribe', data)
                .then(response => {
                  this.loading = false;
                  this.$eventBus.$emit('alert', response.data);
                  this.$store.dispatch("fetchUserData").then(() => {
                    this.loading = false;
                    this.$router.push('/dashboard');
                  });
                }).catch(() => {
                this.loading = false;
                this.$eventBus.$emit('alert', {
                  displayAlert: 'error',
                  message: 'Something went bang! Contact support ASAP!'
                })
              });

              this.loading = false;
            });
          }
        }
      }
    },
    mounted() {
      this.stripe = Stripe(process.env.VUE_APP_STRIPE_KEY)
      this.elements = this.stripe.elements()
      this.card = this.elements.create('card')
      this.getIntent();
    }
  }
</script>

<style scoped>
  .mimic-input {
    padding: 20px 12px;
    background: rgba(0, 0, 0, 0.06);
    border-bottom: 1px solid rgba(0, 0, 0, 0.15);

    -webkit-border-top-left-radius: 5px;
    -webkit-border-top-right-radius: 5px;
    -moz-border-radius-topleft: 5px;
    -moz-border-radius-topright: 5px;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
  }
</style>