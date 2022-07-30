<template>
  <div class="forVue">
	<div>
		<b-navbar toggleable="lg" type="dark" variant="info">
			<router-link to="/" class="navbar-brand" exact >Loteri Map</router-link>
	
			<b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

			<b-collapse id="nav-collapse" is-nav>
				<!-- Right aligned nav items -->
				<b-navbar-nav class="ml-auto">
					<b-nav-item class="nav-link" @click="$bvModal.show('loginModal')" v-if="!logged">Login</b-nav-item>
					<b-nav-item class="nav-link" @click="$bvModal.show('cadastroModal')" v-if="!logged">Cadastre-se</b-nav-item>
					<b-nav-item><router-link :to="{name: 'home'}" class="nav-link" exact v-if="logged"><font-awesome-icon icon="fa-solid fa-home" /> Home</router-link></b-nav-item>
					<b-nav-item><router-link :to="{name: 'meus-jogos'}" class="nav-link" exact v-if="logged"><font-awesome-icon icon="fa-solid fa-star" /> Meus jogos</router-link></b-nav-item>
					<b-nav-item><a class="nav-link" v-if="logged" @click="sair"><font-awesome-icon icon="fa-solid fa-close" /> Sair</a></b-nav-item>
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
				modalLogin: false,
				logged: false
			}
		},

		methods: {
			sair() {
				localStorage.removeItem('token');
				window.location.href = "/";
			}
		},

		created: function() {
			if (localStorage.getItem('token')) {
				this.logged = true;
			}
		}
	}
</script>

<style scoped>
	.navbar {
		padding-left: 15px;
	}
</style>