<template>
	<div class="d-flex justify-content-center align-items-center left-login">
		<div class="col-10">
			<h2 class="text-center mb-5 title-login">Faça aqui seu cadastro</h2>
			<b-form>
				<b-form-group label="Nome" label-for="nome">
					<b-form-input id="nome" type="nome" placeholder="Digite seu nome" autocomplete="off" v-model="form.nome"></b-form-input>
				</b-form-group>

				<b-form-group label="E-mail" label-for="email">
					<b-form-input id="email" type="email" placeholder="Digite seu e-mail" autocomplete="off" v-model="form.email"></b-form-input>
				</b-form-group>
		
				<b-form-group label-for="password">
					<label class="d-flex justify-content-between">
						Senha
					</label>
		
					<b-form-input id="password" type="password" placeholder="Digite sua senha" v-model="form.password"></b-form-input>
				</b-form-group>

				<b-form-group label-for="confirm">
					<label class="d-flex justify-content-between">
						Confirme a senha
					</label>
		
					<b-form-input id="confirm" type="password" placeholder="Digite novamente sua senha" v-model="form.confirm"></b-form-input>
				</b-form-group>

				<hr />
		
				<b-button type="button" variant="success" block @click="login">
					<i class="fas fa-sign-in-alt"></i> Salvar
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
				nome: "",
				email: "",
				password: "",
				confirm: ""
			}
		}
	},
	
	methods: {
		login() {},
	
		register() {},
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