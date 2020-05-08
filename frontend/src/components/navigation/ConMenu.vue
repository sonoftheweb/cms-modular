<template>
	<v-navigation-drawer v-model="drawer" dark :mini-variant="mini" app clipped color="indigo darken-4">
		<v-list dense class="indigo darken-4 mt-10" nav>
			<div v-for="(item, i) in items" :key="i">
				<v-list-item @click="toLink(item.path)" link>
					<v-list-item-action>
						<v-icon>{{ item.icon }}</v-icon>
					</v-list-item-action>
					<v-list-item-content>
						<v-list-item-title class="grey--text">
							{{ item.name }}
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item v-if="item.children.length" v-for="(child, c) in item.children" :key="c" @click="toLink(child.path)" link>
					<v-list-item-action>
						<v-icon>{{ child.icon }}</v-icon>
					</v-list-item-action>
					<v-list-item-content>
						<v-list-item-title class="grey--text">
							{{ child.name }}
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-divider v-if="item.children.length"></v-divider>
			</div>
		</v-list>
		<v-btn x-small text exact icon class="mx-4 mt-8 mb-1" @click="mini = !mini">
			<v-icon v-if="!mini">mdi-arrow-left-circle-outline</v-icon>
			<v-icon v-else>mdi-arrow-right-circle-outline</v-icon>
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
			},
			childNames (name) {
				name = name.split('.')
				name = name[name.length - 1].replace('-', ' ')
				return name
			}
		},
		mounted() {
			this.$eventBus.$on('toggle-menu', () => {
				this.drawer = !this.drawer;
			});

			this.$router.options.routes.forEach(route => {
				if (Object.prototype.hasOwnProperty.call(route.meta, 'inMenu')) {
					let children = (Object.prototype.hasOwnProperty.call(route, 'children')) ? route.children : []
					children = children.map((child) => {
						return {
							name: this.childNames(child.name),
							path: child.path,
							icon: child.meta.icon
						}
					})
					this.items.push({
						name: route.name,
						path: route.path,
						icon: route.meta.icon,
						children: children
					})
				}
			})
		}
	}
</script>
