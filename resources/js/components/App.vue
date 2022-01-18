<template>
	<div>
		<top-menu></top-menu>
		<transition name="fade" mode="out-in">
			<router-view></router-view>
		</transition>
	</div>
</template>

<script>
	import TopMenu from './shared/TopMenu.vue';

	export default {
		components: {
			'top-menu': TopMenu
		}
	}
</script>