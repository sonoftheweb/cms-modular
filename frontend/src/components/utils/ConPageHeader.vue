<template>
	<div class="mb-5">
		<con-title :provided-title="providedTitle"/>
		<con-breadcrumbs :over-ride="bcOverride"/>
	</div>
</template>

<script>
	import ConBreadcrumbs from '../navigation/ConBreadcrumbs'
	import ConTitle from './ConTitle'
	
	export default {
		props: ["providedTitle", "bcOverride"],
		components: {
			ConTitle,
			ConBreadcrumbs
		},
	}
</script>