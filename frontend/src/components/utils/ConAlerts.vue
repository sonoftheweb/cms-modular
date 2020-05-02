<template>
	<div>
		<v-row v-if="alertData.status">
			<v-col cols="12" md="8" sm="12" xs="12"></v-col>
			<v-col cols="12" md="4" sm="12" xs="12" @click="show = !show">
				<v-alert
					class="ma-0 elevation-4"
					:type="alertData.status"
					:icon="alertIcon"
					v-model="show"
					transition="scale-transition"
					style="cursor:pointer"
					dismissible
					dense
				>{{ alertData.message }}</v-alert>
			</v-col>
		</v-row>
	</div>
</template>

<script>
    export default {
        props: ["alertData"],
        data() {
            return {
                alertIcon: "mdi-checkbox-marked-circle-outline",
                timeout: 5000,
                show: false
            }
        },
        watch: {
            alertData: function() {
                if (this.alertData.status === "error") {
                    this.alertIcon = "mdi-alert-circle-outline";
                } else if (this.alertData.status === "success") {
                    this.alertIcon = "mdi-checkbox-marked-circle-outline";
                } else if (this.alertData.status === "info") {
                    this.alertIcon = "mdi-information-outline";
                } else if (this.alertData.status === "warning") {
                    this.alertIcon = "mdi-alert-outline";
                }
                this.show = true;

                setTimeout(() => {
                    // () => allows us to reference this as the parent rather than the function, cool stuff
                    this.show = false;
                }, this.timeout);
            }
        },
    }
</script>