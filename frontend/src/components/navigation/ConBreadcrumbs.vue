<template>
	<v-breadcrumbs :items="items" class="ma-0 pa-0">
		<template v-slot:divider>
			<v-icon>mdi-chevron-right</v-icon>
		</template>
	</v-breadcrumbs>
</template>

<script>
	export default {
		props: ["overRide"],
		data() {
			return {
				items: [
					{
						text: 'home',
						disabled: false,
						to: '/home',
					}
				],
			}
		},
		mounted() {
			if (this.overRide) {
				this.items = [{
					text: this.overRide,
					disabled: true,
					to: ''
				}];
			} else {
				for (let i = 0; i < this.$route.matched.length; i++) {
					this.items.push({
						text: this.$route.matched[i].name,
						disabled: this.$route.matched.length - 1 === i,
						to: this.$route.matched[i].path
					});
				}
			}
		}
	}
</script>