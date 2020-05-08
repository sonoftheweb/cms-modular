<template>
	<v-navigation-drawer v-model="drawer" dark :mini-variant="mini" app clipped color="indigo darken-4">
		<v-list dense class="indigo darken-4 mt-10" nav>
			<v-list-item v-for="(item, i) in items" :key="i" @click="toLink(item.path)" link>
				<v-list-item-action>
					<v-icon>{{ item.icon }}</v-icon>
				</v-list-item-action>
				<v-list-item-content>
					<v-list-item-title class="grey--text">
						{{ item.name }}
					</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
		</v-list>
		<v-btn x-small text exact icon class="mx-4 mt-8 mb-1" @click="mini = !mini">
			<v-icon>mdi-arrow-left-circle-outline</v-icon>
		</v-btn>
	</v-navigation-drawer>
</template>

<script>
	export default {
		data: () => ({
			mini: false,
			drawer: null,
			items: []
		}),
		methods: {
			toLink(link) {
				if (link) {
					this.$router.push(link);
				}
			}
		},
		mounted() {
			this.$eventBus.$on('toggle-menu', () => {
				this.drawer = !this.drawer;
			});

			this.$router.options.routes.forEach(route => {
				if (Object.prototype.hasOwnProperty.call(route.meta, 'inMenu'))
					this.items.push({
						name: route.name,
						path: route.path,
						icon: route.meta.icon
					})
			})
		}
	}
</script>
