<template>
  <v-layout v-if="confirmer.active" row justify-center>
    <v-dialog :value="confirmer.active" persistent max-width="290">
      <v-card>
        <v-card-title class="headline">{{confirmer.title}}</v-card-title>
        <v-card-text v-if="confirmer.body">{{confirmer.body}}</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click.native="confirm">Confirm</v-btn>
          <v-btn text @click.native="cancel">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
  export default {
    computed: {
      confirmer() {
        return this.$store.state.confirmer
      }
    },
    methods: {
      confirm() {
        this.confirmer.resolve(true);
        this.$store.commit('deactivateConfirm');
      },
      cancel() {
        this.confirmer.resolve(false);
        this.$store.commit('deactivateConfirm');
      }
    }
  }
</script>

<style scoped>

</style>
