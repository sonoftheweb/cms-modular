<template>
  <v-row>
    <v-col md="4" offset-md="4">
      <h2 class="thin font-weight-light mb-3">Welcome back :)</h2>
      <v-card color="grey lighten-5" light>
        <v-card-text class="px-6">
          <v-row>
            <v-col md="8">
              <v-form ref="form" lazy-validation class="mr-1">
                <v-text-field
                  v-model="auth.email"
                  label="Email address"
                  placeholder="email@example.com"
                  prepend-inner-icon="mdi-at"
                  :rules="emailValidationRules"
                  filled
                ></v-text-field>
                <v-text-field
                  v-model="auth.password"
                  label="Password"
                  placeholder="Password"
                  prepend-inner-icon="mdi-key-variant"
                  :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="showPassword ? 'text' : 'password'"
                  :rules="requiredFieldRule"
                  @click:append="showPassword = !showPassword"
                  filled
                ></v-text-field>
                <v-checkbox class="mt-0" v-model="auth.remember_me" label="Remember me"></v-checkbox>
                <v-btn depressed large dark color="indigo" class="mr-3" @click="login">Let's go!</v-btn>
                <v-btn depressed large light class="float-right">Forgot Password</v-btn>
              </v-form>
            </v-col>
            <v-col md="4" class="hidden-sm-and-down">
              <p class="pb-3 mb-10">Fill in the form to access your account.</p>
              <h3 class="mb-4">Your first time here?</h3>
              <p>Setup an account and access all features for free for a month.</p>
              <v-btn large light block color="success">Get trial</v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
  import { mapGetters } from 'vuex'

  export default {
    computed: {
      ...mapGetters(['emailValidationRules','requiredFieldRule'])
    },
    data() {
      return {
        showPassword: false,
        auth: {
          email: null,
          password: null,
          remember_me: false
        },
      }
    },
    methods: {
      login() {
        // validate form entry
        if (this.$refs.form.validate()) {
          this.$http.post('/api/auth/login', this.auth)
          .then(response => {
            console.log(response)
          })
        }
      }
    }
  }
</script>
