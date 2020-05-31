<template>
  <div style="width: 100%">
    <v-row v-if="!authenticated">
      <v-col md="5" offset-md="3">
        <h2 class="thin font-weight-light mb-3">Welcome back :)</h2>
        <v-card color="grey lighten-5" light>
          <v-card-text class="px-5">
            <v-form ref="form" lazy-validation>
              <v-text-field
                v-model="auth.email"
                type="email"
                label="Email address"
                placeholder="email@example.com"
                prepend-inner-icon="mdi-at"
                :rules="emailValidationRules"
                outlined
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
                outlined
              ></v-text-field>
              <v-checkbox class="mt-0" v-model="auth.remember_me" label="Remember me"></v-checkbox>
              <v-btn depressed large dark color="indigo" class="mr-3 float-md-left" @click="login">Let's go!</v-btn>
              <v-btn depressed large text class="float-right">Forgot Password</v-btn>
              <div style="clear: both"></div>
              <v-divider class="my-8 mx-10"></v-divider>
              <v-btn @click="showRegisterForm = true" class="mt-5" color="success" depressed large block>Register an account</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-dialog persistent v-model="firstTimeLogin.show" :max-width="$modalMaxWidths.alerts">
      <v-card>
        <v-card-title>
          Confirm your password
        </v-card-title>
        <v-card-text>
          <p>{{ firstTimeLogin.message }}</p>
          <v-text-field
            v-model="auth.confirm_password"
            label="Password"
            placeholder="Password"
            prepend-inner-icon="mdi-key-variant"
            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
            :type="showPassword ? 'text' : 'password'"
            :rules="pwdConfirm"
            @click:append="showPassword = !showPassword"
            outlined
            dense
          ></v-text-field>
        </v-card-text>
        <v-card-actions class="px-6 py-6">
          <v-btn text @click="firstTimeLogin.show=false">Cancel</v-btn>
          <v-spacer></v-spacer>
          <v-btn depressed color="success" @click="login">Continue</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <con-registration-form :show="showRegisterForm" @close="showRegisterForm = false"/>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import ConRegistrationForm from '../forms/ConRegistrationForm'

  export default {
    components: {
      ConRegistrationForm
    },
    computed: {
      ...mapGetters(['emailValidationRules','requiredFieldRule', 'authenticated'])
    },
    data() {
      return {
        showPassword: false,
        auth: {
          email: null,
          password: null,
          confirm_password: null,
          remember_me: false
        },
        firstTimeLogin: {
          show: false,
          message: null
        },
        pwdConfirm: [
          v => !!v || "Confirm password",
          v => v === this.auth.password || "Passwords do not match"
        ],
        showRegisterForm: false
      }
    },
    methods: {
      login() {
        // validate form entry
        if (this.$refs.form.validate()) {
          this.$http.post('/api/auth/login', this.auth).then(response => {
            if (response.data.status === 'action_required') {
              this.firstTimeLogin.message = response.data.message
              this.firstTimeLogin.show = true
            } else {
              this.$store.dispatch('loggedIn', response.data.token).then(async () => {
                window.location.replace('/home')
              })
            }
          }).catch(err => {
            console.log('do something here', err)
          })
        }
      }
    }
  }
</script>
