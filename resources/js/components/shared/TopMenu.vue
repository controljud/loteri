<template>
  <div class="forVue">
	<div>
	<b-navbar toggleable="lg" type="dark" variant="info">
		<router-link to="/" class="navbar-brand" exact >Loteri Map</router-link>

		<b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

		<b-collapse id="nav-collapse" is-nav>
			<!-- Right aligned nav items -->
			<b-navbar-nav class="ml-auto">
				<b-nav-item class="nav-link" @click="$bvModal.show('loginModal')">Login</b-nav-item>
				<b-nav-item class="nav-link" @click="$bvModal.show('cadastroModal')">Cadastre-se</b-nav-item>
			</b-navbar-nav>
		</b-collapse>
	</b-navbar>
	</div>
	<login-modal></login-modal>
	<cadastro-modal></cadastro-modal>
  </div>
</template>

<script>
	import LoginModal from '../login/LoginModal.vue'
	import CadastroModal from '../login/CadastroModal.vue'
	
	export default {
		components: {
			'login-modal': LoginModal,
			'cadastro-modal': CadastroModal,
		},

		data() {
			return {
				modalLogin: false
			}
		},

		methods: {

		}
	}
</script>

<style scoped>
	.navbar {
		padding-left: 15px;
	}
</style>