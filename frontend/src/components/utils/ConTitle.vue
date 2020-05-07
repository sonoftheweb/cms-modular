<template>
	<p class="headline">{{ title }}</p>
</template>

<script>
	export default {
		props: ["providedTitle"],
		data() {
			return {
				title: null
			}
		},
		mounted() {
			if (this.providedTitle) {
				this.title = this.providedTitle;
			} else {
				if (Object.prototype.hasOwnProperty.call(this.$route.meta, 'displayableName')) {
					this.title = this.$route.meta.displayableName;
				}
			}
		}
	}
</script>
