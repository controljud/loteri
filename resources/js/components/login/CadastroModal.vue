<template>
    <div>
        <b-modal id="cadastroModal" hide-footer hide-header-close>
            <template #modal-title>Cadastro</template>
            <div class="d-block text-left">
                <b-form>
                    <b-form-group label="Nome" label-for="nome">
                        <b-form-input id="nome" type="text" placeholder="Digite seu nome" autocomplete="off" v-model="form.name"></b-form-input>
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
    import {api} from './../../config';

    export default {
        data() {
            return {
                form: {
                    name: "",
                    email: "",
                    password: "",
                    confirm: ""
                }
            }
        },

        methods: {
            register() {
                if (this.form.name != '' && this.form.email != '' && this.form.password != '' && this.form.password == this.form.confirm) {
                    axios.post(api.cadastro, this.form).then(response => {
                        if (response.data.status == 0) {
                            this.$toast.success("Cadastro efetuado com sucesso");
                            
                            // TODO - Login automático - JWT
                            // TODO - Fechar janela de cadastro
                        } else {
                            this.$toast.danger("Falha no cadastro");
                            // TODO - Tratamento de erros
                        }
                    });
                } else {
                    this.$toast.warning("Preencha todos os campos");
                }

                this.apagaDados();
            },

            apagaDados() {
                this.form.name = '';
                this.form.email = '';
                this.form.password = '';
                this.form.confirm = '';
            }
        }
    }
</script>