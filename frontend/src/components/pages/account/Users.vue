<template>
  <div>
    <con-page-header/>
    <p>
      This account has {{user.instance.seats}} seats. Manage all your users and their data.
      Note that you can only have as many as {{user.instance.seats}} managers and and unlimited amount of clients.
      If you run out of seats, you will be unable to add managers until you update the number of seats your account has.
    </p>
    <con-smart-table
      class="mt-10"
      :key="tableKey"
      :uri="uri"
      :smart-actions="['edit', 'delete']"
      @smart-table-row-edit="editUser($event)"
      @smart-table-row-clicked="editUser($event)"
      @smart-table-row-delete="editUser($event)"
    />
  
    <v-dialog v-if="editUserForm.user" persistent v-model="editUserForm.show" max-width="490">
      <v-card>
        <v-card-title class="headline">Edit User: {{ editUserForm.user.name }}</v-card-title>
        <v-card-text>
          Alter the details below. Updating password is not allowed.
          <v-form ref="updateUserForm">
            <v-row class="mt-3">
              <v-col md="12" sm="12">
                <v-text-field dense label="Email *" v-model="editUserForm.user.email" outlined disabled></v-text-field>
              </v-col>
            </v-row>
            <v-divider class="mb-7"/>
            <v-row>
              <v-col md="6" sm="12">
                <v-text-field dense label="Name *" v-model="editUserForm.user.name" outlined></v-text-field>
              </v-col>
              <v-col md="6">
                <v-text-field dense label="Job title" v-model="editUserForm.user.attribute.user_job_title" outlined></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col md="12">
                <v-textarea
                  dense
                  outlined
                  rows="2"
                  label="Job description"
                  v-model="editUserForm.user.attribute.user_job_description"
                  :value="editUserForm.user.attribute.user_job_description"
                ></v-textarea>
              </v-col>
            </v-row>
            <small>Is this user active?</small>
            <v-switch inset v-model="editUserForm.user.active" class="mx-2" label="Active"></v-switch>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn text @click="editUserForm.show = false">Cancel</v-btn>
          <v-spacer></v-spacer>
          <v-btn dark depressed color="indigo" @click="saveUser">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import ConPageHeader from '../../utils/ConPageHeader'
import ConSmartTable from '../../utils/ConSmartTable'

export default {
  name: "Users",
  components: { ConPageHeader, ConSmartTable },
  computed: {
    ...mapGetters(['priceFormatted', 'firstLetterCaps', 'user'])
  },
  data() {
    return {
      uri: '/api/users',
      editUserForm: {
        show: false,
        user: null
      },
      tableKey: 1
    }
  },
  methods: {
    editUser(user) {
      console.log(user)
      this.editUserForm.show = true
      this.editUserForm.user = user
    },
    saveUser() {
      if ((Object.prototype.hasOwnProperty.call(this.editUserForm.user, 'id'))) {
        // update a user
        this.$http.put(this.uri + '/' + this.editUserForm.user.id, this.editUserForm.user).then(response => {
          this.editUserForm.show = false
          this.$eventBus.$emit('alert', response.data)
          this.tableKey += 1
        });
      }
      else {
        // create a user
        this.$http.post(this.uri, this.editUserForm.user).then(response => {
          this.editUserForm.show = false
          this.$eventBus.$emit('alert', response.data)
          this.tableKey += 1
        });
      }
    }
  },
  mounted() {
  
  }
}
</script>