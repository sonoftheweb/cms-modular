<template>
  <div>
    <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
      <v-card>
        <v-toolbar dense dark flat color="indigo darken-4">
          <div class="container">
            <v-toolbar-title>Register a new account</v-toolbar-title>
          </div>
        </v-toolbar>
        <div class="container">
          <v-form ref="regform" lazy-validation>
            <v-list three-line subheader>
              <v-subheader>User details</v-subheader>
            </v-list>
            <v-divider></v-divider>
            
            <v-row class="pt-5">
              <v-col sm="12" md="6">
                <v-text-field
                  v-model="registration.email"
                  label="Email *"
                  placeholder="john@doe.com"
                  :rules="emailValidationRules"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col sm="12" md="6">
                <v-text-field
                  v-model="registration.name"
                  label="Full name *"
                  placeholder="John Doe"
                  outlined
                  dense
                  :rules="requiredFieldRule"
                ></v-text-field>
              </v-col>
              <v-col sm="12" md="6">
                <v-text-field
                  v-model="registration.password"
                  label="Password *"
                  placeholder="Enter password"
                  outlined
                  dense
                  :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="showPassword ? 'text' : 'password'"
                  :rules="requiredFieldRule"
                  @click:append="showPassword = !showPassword"
                ></v-text-field>
              </v-col>
              <v-col sm="12" md="6">
                <v-text-field
                  v-model="registration.confPassword"
                  label="Password confirmation *"
                  placeholder="Confirm password"
                  outlined
                  dense
                  :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="showPassword ? 'text' : 'password'"
                  :rules="[passwordConfirmationRule]"
                  @click:append="showPassword = !showPassword"
                ></v-text-field>
              </v-col>
              <v-col sm="12" md="6">
                <v-text-field
                  v-model="registration.user_attributes.user_job_title"
                  label="Job title"
                  placeholder="What's do you do?"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
            </v-row>
  
            <v-list three-line subheader>
              <v-subheader>Account details</v-subheader>
            </v-list>
            <v-divider></v-divider>
            
            <v-row class="pt-5">
              <v-col sm="12" md="6">
                <v-text-field
                  v-model="registration.instance.instance_name"
                  label="Company name *"
                  placeholder="Company name"
                  :rules="requiredFieldRule"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col sm="12" md="6"></v-col>
              <v-col sm="12" md="6">
                <v-text-field
                  v-model="registration.instance.address_line_1"
                  label="Address line 1 *"
                  placeholder="Address"
                  :rules="requiredFieldRule"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col sm="12" md="6">
                <v-text-field
                  v-model="registration.instance.address_line_2"
                  label="Address line 2"
                  placeholder="Address"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col sm="12" md="4">
                <v-text-field
                  v-model="registration.instance.city"
                  label="City *"
                  placeholder="City"
                  :rules="requiredFieldRule"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col sm="12" md="4">
                <v-text-field
                  v-model="registration.instance.state"
                  label="State / Province *"
                  placeholder="State"
                  :rules="requiredFieldRule"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col sm="12" md="4">
                <v-text-field
                  v-model="registration.instance.country"
                  label="Country *"
                  placeholder="country"
                  :rules="requiredFieldRule"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              
              <v-col sm="12" md="4">
                <v-text-field
                  v-model="registration.instance.website"
                  label="Website"
                  placeholder="https://www.foobar.com"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col sm="12" md="4">
                <v-text-field
                  v-model="registration.instance.direct_phone"
                  label="Contact phone number"
                  placeholder="Direct phone number to contact company"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col sm="12" md="4">
                <v-text-field
                  v-model="registration.instance.direct_email"
                  label="Contact email address *"
                  placeholder="Direct email contact"
                  :rules="emailValidationRules"
                  outlined
                  dense
                ></v-text-field>
              </v-col>
            </v-row>
            <div class="mb-7">
              <small>By submitting this form, you have agreed to the terms of service and will abide by the rules set there in. See terms and conditions here.</small>
            </div>
            
            <v-btn color="indigo" dark @click="registerAccount()">Register</v-btn>
            <v-btn text class="float-right" @click="dialog = false">Cancel</v-btn>
          </v-form>
          
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
  import {mapGetters} from "vuex";

  export default {
    props: {
      show: {type: Boolean, default: false}
    },
    computed: {
      ...mapGetters(['emailValidationRules','requiredFieldRule', 'authenticated']),
      passwordConfirmationRule() {
        return this.registration.password === this.registration.confPassword && !!this.registration.confPassword || "Password must match!";
      }
    },
    watch: {
      show(newValue) {
        this.dialog = newValue
      },
      dialog(newValue) {
        if (!newValue)
          this.$emit('close')
      }
    },
    data() {
      return {
        dialog: false,
        notifications: false,
        sound: true,
        widgets: false,
  
        valid: false,
        showPassword: false,
        registration: {
          name: null,
          email: null,
          password: null,
          confPassword: null,
          user_attributes: {
            user_job_title: null
          },
          instance: {
            instance_name: null,
            address_line_1: null,
            address_line_2: null,
            city: null,
            state: null,
            country: null,
            website: null,
            direct_phone: null,
            direct_email: null,
          }
        }
      }
    },
    methods: {
      registerAccount() {
        if (this.$refs.regform.validate()) {
          this.$http.post('/api/auth/registration', this.registration).then(response => {
            this.$store.dispatch('loggedIn', response.data.token).then(async () => {
              window.location.replace('/home')
            })
          })
        }
      }
    }
  }
</script>

<style scoped>

</style>