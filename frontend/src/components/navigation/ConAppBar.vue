<template>
	<div>
		<v-app-bar app dark fixed flat clipped-left color="indigo darken-4">
			<v-app-bar-nav-icon v-if="authenticated" @click="toggleMenu"/>
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
				v-if="authenticated"
				dense
				solo-inverted
				flat
				hide-details
				label="Search everything..."
				prepend-inner-icon="mdi-magnify"
			/>

			<v-spacer/>

			<v-menu close-delay="30" open-on-hover offset-y v-if="authenticated">
				<template v-slot:activator="{ on }">
					<v-btn text class="mr-5" v-on="on">
						<div class="body-2">{{ user.name }}</div>
						<v-avatar class="ml-3" color="indigo" size="24">
							<v-icon dark>mdi-account-circle</v-icon>
						</v-avatar>
					</v-btn>
				</template>
				<v-list>
					<v-list-item @click.stop="gotoPage('/me')">
						<v-list-item-title>Profile</v-list-item-title>
					</v-list-item>
					<v-list-item @click.stop="gotoPage('/manage-subscription')">
						<v-list-item-title>Subscription</v-list-item-title>
					</v-list-item>
				</v-list>
			</v-menu>
			<v-btn v-if="authenticated" icon dark small class="mx-1 indigo lighten-1" @click.stop="logout">
				<v-icon small>mdi-location-exit</v-icon>
			</v-btn>
		</v-app-bar>
		<con-confirm/>
	</div>
</template>

<script>
	import {mapGetters} from 'vuex'
	import ConConfirm from '../utils/ConConfirm'

	export default {
		components: {
			ConConfirm
		},
		computed: {
			...mapGetters(['user', 'authenticated'])
		},
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
				this.$store.dispatch('logout')
			},
			gotoPage(page) {
				this.$router.push(page)
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