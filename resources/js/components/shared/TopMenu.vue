<template>
  <div class="forVue">
	<div>
	<b-navbar toggleable="lg" type="dark" variant="info">
		<router-link to="/" class="navbar-brand" exact >Loteri Map</router-link>

		<b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

		<b-collapse id="nav-collapse" is-nav>
			<!-- Right aligned nav items -->
			<b-navbar-nav class="ml-auto">
				<router-link to="/home" class="nav-link" exact >Home</router-link>
				<router-link to="/login" class="nav-link" exact>Login</router-link>
				<router-link to="/cadastro" class="nav-link" exact>Cadastre-se</router-link>
			</b-navbar-nav>
		</b-collapse>
	</b-navbar>
	</div>
  </div>
</template>

<script>
	export default {

	}
</script>

<style scoped>
	.navbar {
		padding-left: 15px;
	}
</style>