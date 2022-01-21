<template>
	<div class="d-flex justify-content-center align-items-center left-login">
		<div class="col-10">
			<h2 class="text-center mb-5 title-login">Faça aqui seu login</h2>
			<b-form>
				<b-form-group label="E-mail" label-for="email">
					<b-form-input id="email" type="email" placeholder="Digite seu e-mail" autocomplete="off" v-model="form.email"></b-form-input>
				</b-form-group>
		
				<b-form-group label-for="password">
					<label class="d-flex justify-content-between">
						Senha
						<small><a href="#">Esqueceu sua senha?</a></small>
					</label>
		
					<b-form-input id="password" type="password" placeholder="Digite sua senha" v-model="form.password"></b-form-input>
				</b-form-group>

				<hr />
		
				<b-button type="button" variant="primary" block @click="login">
					<i class="fas fa-sign-in-alt"></i> Entrar
				</b-button>

				<b-button type="button" variant="outline-secondary" block @click="register">
					<i class="fas fa-user-plus"></i> Não tenho conta
				</b-button>
			</b-form>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			form: {
				email: "",
				password: ""
			}
		}
	},
	
	methods: {
		login() {},
	
		register() {
			this.$router.push('/cadastro');
		},
	}
}
</script>
 
<style>
	*, /*resetar o estilo da página*/
	*::after,
	*::before {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
		text-decoration: none;
	}
	
	.row-login {
		margin-left: 0;
	}
	
	.title-login {
		font-weight: bold;
	}
</style>