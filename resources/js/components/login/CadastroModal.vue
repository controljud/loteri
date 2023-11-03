<template>
    <div>
        <b-modal id="cadastroModal" hide-footer hide-header-close>
            <template #modal-title>Cadastro</template>
            <div class="d-block text-left">
                <b-form>
                    <div class="row">
                        <div class="col-md-12 center">
                            <label>
                                <b-form-file
                                    accept="image/*"
                                    placeholder="Selecione a imagem do usuário"
                                    drop-placeholder="Apague a imagem aqui"
                                    @change="uploadImage"
                                    :class="hide"
                                >
                                </b-form-file>
                                <img :src="image" class="image" />
                            </label><br />
                            <span class="texto_arquivo">
                                <b>Tipos de imagem permitidos: </b>png, jpg e jpeg / <b>Tamanho máximo permitido: </b>350kb
                            </span>
                        </div>
                    </div>

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
                    confirm: "",
                    imagem: null
                },
                
                image: '/images/user-admin.png',
                hide: 'hide'
            }
        },

        methods: {
            register() {
                if (this.form.name != '' && this.form.email != '' && this.form.password != '' && this.form.password == this.form.confirm) {
                    axios.post(api.cadastro, this.form).then(response => {
                        if (response.data.status == 0) {
                            this.$toast.success("Cadastro efetuado com sucesso");

                            // axios.post(api.login, this.form).then(response => {
                            //     console.log(response);
                            //     let dados = response.data;
                                
                            //     if (dados.status == 0) {
                            //         localStorage.setItem('user', dados.data.user);
                            //         localStorage.setItem('token', dados.data.token);

                            //         // TODO - Encaminhar para o home
                            //         this.$router.push('home');
                            //     }
                            // });
                            
                            // TODO - Login automático - JWT
                            // TODO - Encaminhar para o home
                            this.apagaDados();
                            this.$router.push('home');
                        } else {
                            this.$toast.danger(response.data.message);
                            // TODO - Tratamento de erros
                        }
                    }).catch(error => {
                        if (error.response.status == 400) {
                            this.$toast.warning(error.response.data.message);
                        } else {
                            this.$toast.danger("Erro no envio do formulário");
                        }
                    });
                } else {
                    this.$toast.warning("Preencha todos os campos");
                }
            },

            apagaDados() {
                this.form.name = '';
                this.form.email = '';
                this.form.password = '';
                this.form.confirm = '';
            },

            uploadImage(e) {
                const image = e.target.files[0];
                const reader = new FileReader();
                reader.readAsDataURL(image);
                reader.onload = e =>{
                    if (e.target.result.indexOf('png') > -1 || e.target.result.indexOf('jpg') > -1 || e.target.result.indexOf('jpeg') > -1) {
                        if (e.total <= 350000) {
                            this.form.imagem = e.target.result;
                            this.image = e.target.result;
                        } else {
                            this.$toast.warning("Tamanho de imagem não permitido");
                        }
                    } else {
                        this.$toast.warning("Tipo de arquivo não permitido");
                    }
                };
            }
        }
    }
</script>

<style scoped>
.hide {
    display: none;
}

.image {
    width: 150px;
    height: 150px;
    border-radius: 100%;
}

.center {
    text-align: center;
}

.texto_arquivo {
    font-size: 9px;
}

.dv_image {
    margin-bottom: 35px;
}
</style>