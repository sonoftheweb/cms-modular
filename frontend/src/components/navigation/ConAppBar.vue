<template>
	<v-app-bar dense app dark fixed flat clipped-left color="indigo darken-4">
		<v-app-bar-nav-icon @click="toggleMenu"/>
		<span class="title ml-3 mr-5">Fort&nbsp;<span class="font-weight-light">Con</span>
		</span>
		
		<v-progress-linear
			v-if="isLoading"
			active
			indeterminate
			absolute
			bottom
			color="indigo lighten-4"
		></v-progress-linear>
		
		<v-spacer/>
		
		<v-text-field
			dense
			solo-inverted
			flat
			hide-details
			label="Search everything..."
			prepend-inner-icon="mdi-magnify"
		/>
		
		<v-spacer/>
		
		<v-avatar class="ml-3 mr-2" color="indigo" size="24">
			<v-icon dark>mdi-account-circle</v-icon>
		</v-avatar>
		<v-btn icon dark small class="mx-1 indigo lighten-1" @click.stop="logout">
			<v-icon small>mdi-location-exit</v-icon>
		</v-btn>
	</v-app-bar>
</template>

<script>
	export default {
		data() {
			return {
				isLoading: false
			}
		},
		methods: {
			toggleMenu() {
				this.$eventBus.$emit('toggle-menu')
			},
			logout() {
				this.$store.dispatch('logout').then(() => {
					this.$router.push('/')
				})
			}
		},
		mounted() {
			this.$eventBus.$on('toggle-loading', (val) => {
				this.isLoading = val;
			});
		}
	}
</script>

<style scoped>

</style>