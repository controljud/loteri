<template>
    <div>
        <b-modal id="novoJogoModal" hide-footer hide-header-close>
            <template #modal-title>Novo Jogo</template>
            <div class="d-block text-left">
                <b-form>
                    <div class="row">
                        <div class="col-md-12 center dv_image">
                            <label>
                                <b-form-file
                                    accept="image/*"
                                    placeholder="Selecione a imagem do jogo"
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

                    <div class="row">
                        <div class="col-md-6">
                            <b-form-group label="Nome do Jogo *" label-for="numero">
                                <b-form-input id="nome" type="text" autocomplete="off" v-model="form.jogo"></b-form-input>
                            </b-form-group>
                        </div>

                        <div class="col-md-6">
                            <b-form-group label="Status *" label-for="status">
                                <b-form-checkbox
                                    id="checkbox-1"
                                    v-model="form.status"
                                    name="checkbox-1"
                                    value="1"
                                    unchecked-value="0"
                                >
                                </b-form-checkbox>
                            </b-form-group>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <b-form-group label="Quantidade de Dezenas *" label-for="premio">
                                <b-form-input type="number" autocomplete="off" v-model="form.quantidade_dezenas"></b-form-input>
                            </b-form-group>
                        </div>

                        <div class="col-md-6">
                            <b-form-group label="Quantidade de Acertos *" label-for="premio">
                                <b-form-input type="number" autocomplete="off" v-model="form.quantidade_acertos"></b-form-input>
                            </b-form-group>
                        </div>
                    </div>
                </b-form>

                <hr />

                <div class="row">
                    <div class="col-md-12 right">
                        <b-button type="button" variant="success" block @click="salvarJogo">
                            <font-awesome-icon icon="fa-solid fa-save" />
                        </b-button>

                        <b-button type="button" variant="light" block >
                            <font-awesome-icon icon="fa-solid fa-close" @click="$bvModal.hide('novoJogoModal');"/>
                        </b-button>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
    import {api} from '../../../config';
    export default {
        props: [
            'item', 'idJogo'
        ],

        data() {
            return {
                header: {
					headers: {
						Authorization: null
					}
				},

                form: {
                    id: null,
                    jogo: null,
                    quantidade_dezenas: null,
                    quantidade_acertos: null,
                    imagem: null,
                    status: null
                },

                activeClass: 'activeClass',
                inactiveClass: '',
                image: '/images/trevo.png',
                hide: 'hide'
            }
        },

        created: function() {
            this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');
        },

        methods: {
            salvarJogo() {
                if (
                    this.form.jogo != null
                    && this.form.quantidade_dezenas != null
                    && this.form.quantidade_acertos != null
                ) {
                    axios.post(api.jogo, this.form, this.header).then(response => {
                        if (response.data.status == 0) {
                            this.$toast.success(response.data.message);

                            this.zeraCampos();

                            this.$emit('atualizarTabela');
                        } else {
                            this.$toast.warning(response.data.mensage);
                        }
                    }).catch(error => {
                        if (error.response.status == 400) {
                            this.$toast.warning(error.response.data.message);
                        } else {
                            this.$toast.error(error.response.data.message);
                        }
                    });
                } else {
                    this.$toast.warning("Preencha todos os campos obrigatórios");
                }
            },
            
            preencheCampos(item) {
                this.form.id = item.id;
                this.form.jogo = item.jogo;
                this.form.quantidade_dezenas = item.quantidade_dezenas;
                this.form.quantidade_acertos = item.quantidade_acertos;
                this.form.status = item.status;

                if (item.imagem != null) {
                    this.image = item.imagem;
                }
            },

            zeraCampos() {
                this.form.id = null;
                this.form.jogo = null;
                this.form.quantidade_dezenas = null;
                this.form.quantidade_acertos = null;
                this.form.status = 1;

                this.image = '/images/trevo.png';
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
.right {
    text-align: right;
}

.center {
    text-align: center;
}

.v_dezena {
    width: 10%;
    height: 30px;
    margin: 1px auto;
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px solid #D4E6F1;
    border-radius: 5px;
    cursor: pointer;
    float: left;
}

.v_dezena:hover {
    background-color: #D4E6F1;
}

.activeClass {
    background-color: #2980B9;
    color: white;
}

.inactiveClass {
    background-color: white;
    color: black;
}

.caracteres {
    font-size: 9px;
}

.hide {
    display: none;
}

.image {
    width: 100px;
    height: 100px;
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
