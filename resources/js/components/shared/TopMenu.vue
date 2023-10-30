<template>
  <div class="forVue">
	<div>
		<b-navbar toggleable="lg" type="dark" variant="dark">
			<router-link to="/" class="navbar-brand" exact >Loteri Map</router-link>
	
			<b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

			<b-collapse id="nav-collapse" is-nav>
				<b-navbar-nav>
					<b-nav-item class="nav-link" @click="$bvModal.show('loginModal')" v-if="!logged">Login</b-nav-item>
					<b-nav-item class="nav-link" @click="$bvModal.show('cadastroModal')" v-if="!logged">Cadastre-se</b-nav-item>
					<b-nav-item><router-link :to="{name: 'home'}" class="nav-link" exact v-if="logged"><font-awesome-icon icon="fa-solid fa-home" /> Home</router-link></b-nav-item>
					<b-nav-item><router-link :to="{name: 'apostas'}" class="nav-link" exact v-if="logged"><font-awesome-icon icon="fa-solid fa-star" /> Apostas</router-link></b-nav-item>
					<b-nav-item><router-link :to="{name: 'sorteios'}" class="nav-link" exact v-if="logged"><font-awesome-icon icon="fa-solid fa-star" /> Sorteios</router-link></b-nav-item>
				</b-navbar-nav>

				<b-navbar-nav class="ml-auto" v-if="logged">
					<b-nav-item-dropdown v-bind:text="user.name" right>
						<b-dropdown-item href="#">
							<a class="nav-link nav-drop" @click="sair"><font-awesome-icon icon="fa-solid fa-close" /> Sair</a>
						</b-dropdown-item>
					</b-nav-item-dropdown>
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
				logged: false,
				user: null
			}
		},

		methods: {
			sair() {
				localStorage.removeItem('token');
				window.location.href = "/";
			},

			editUser(id) {
				console.log("Editando perfil");
			}
		},

		created: function() {
			if (localStorage.getItem('token')) {
				this.logged = true;

				this.user = JSON.parse(localStorage.getItem('user'));
			}
		}
	}
</script>

<style scoped>
	.navbar {
		padding-left: 15px;
	}

	.drop-user {
		position: absolute;
		float: right;
	}

	.nav-drop {
		color: black !important;
	}
</style>