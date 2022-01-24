<template>
    <div>
        <b-modal id="cadastroModal" hide-footer>
            <template #modal-title>Cadastro</template>
            <div class="d-block text-left">
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
                </b-form>

                <b-button type="button" variant="success" block @click="register">
					<i class="fas fa-sign-in-alt"></i> Salvar
				</b-button>
            </div>
        </b-modal>
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

<style scoped>
    .close {
        display: none !important;
    }
</style>