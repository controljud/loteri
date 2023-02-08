<template>
	<div>
		<div v-if="!isAdmin">
			<top-menu></top-menu>

			<transition name="fade" mode="out-in">
				<router-view></router-view>
			</transition>
		</div>

		<main class="d-flex flex-nowrap" v-if="isAdmin">
			<top-menu-admin></top-menu-admin>

			<div class="col">
				<navbar-admin></navbar-admin>
				<transition name="fade" mode="out-in">
					<router-view></router-view>
				</transition>
			</div>
		</main>

		<!-- <top-menu v-if="!isAdmin"></top-menu>
		<top-menu-admin v-if="isAdmin"></top-menu-admin>

		<transition name="fade" mode="out-in">
			<router-view></router-view>
		</transition> -->
	</div>
</template>

<script>
	import TopMenu from './shared/TopMenu.vue';
	import TopMenuAdmin from './shared/TopMenuAdmin.vue';
	import NavbarAdmin from './shared/NavbarAdmin.vue';

	export default {
		data() {
			return {
				isAdmin: false,
			}
		},

		components: {
			'top-menu': TopMenu,
			'top-menu-admin': TopMenuAdmin,
			'navbar-admin': NavbarAdmin
		},

		created: function() {
			if (this.$route.name.indexOf('admin') > -1) {
				this.isAdmin = true;
			}
		},

		watch:{
			$route (to, from){
				if (to.name.indexOf('admin') > -1) {
					this.isAdmin = true;
				} else {
					this.isAdmin = false;
				}
			}
		}
	}
</script>

<style scoped>
.col {
	display: flex;
    flex-flow: column;
    height: 100vh;
}
</style>