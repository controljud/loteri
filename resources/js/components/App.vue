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

			<div class="col" v-if="isAdmin">
				<navbar-admin></navbar-admin>
				<transition name="fade" mode="out-in">
					<router-view></router-view>
				</transition>
			</div>
		</main>
	</div>
</template>

<script>
	import {api} from '../config';
	import TopMenu from './shared/TopMenu.vue';
	import TopMenuAdmin from './shared/TopMenuAdmin.vue';
	import NavbarAdmin from './shared/NavbarAdmin.vue';

	export default {
		data() {
			return {
				isAdmin: false,
				usuario: null
			}
		},

		components: {
			'top-menu': TopMenu,
			'top-menu-admin': TopMenuAdmin,
			'navbar-admin': NavbarAdmin
		},

		created: function() {
			this.usuario = JSON.parse(localStorage.getItem('user'));

			if (window.location.href.indexOf('admin') > -1) {
				this.isAdmin = true;
			}

			this.checkAdminUser();
		},

		watch:{
			$route (to, from){
				if (to.name.indexOf('admin') > -1) {
					this.isAdmin = true;
				} else {
					this.isAdmin = false;
				}
			}
		},

		methods: {
			checkAdminUser() {
				axios.get(api.usuario_admin + '/' + this.usuario.id, this.header).then(response => {
					let isAdmin = response.data.data;
					if (this.isAdmin && !isAdmin) {
						localStorage.removeItem('token');
						localStorage.removeItem('user');

						window.location.href = "/";
					}
				}).catch(error => {
					localStorage.removeItem('token');
					localStorage.removeItem('user');

					window.location.href = "/";
				});
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