<template>
    <div>
        <b-modal id="novoUsuarioModal" hide-footer hide-header-close>
            <template #modal-title>Novo Usuário</template>
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
                            </label>
                        </div>
                    </div>
                    <div class="row">
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
                        <div class="col-md-6">
                            <b-form-group label="Tipo *" label-for="tipo">
                                <b-select v-model="tipo" class="form-control">
                                    <option v-for="tp in tipos" v-bind:key="tp.id" :selected="tp.id == 2 ? true : false" >{{ tp.tipo }}</option>
                                </b-select>
                            </b-form-group>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <b-form-group label="Nome" label-for="nome">
                                <b-form-input id="nome" type="text" autocomplete="off" v-model="form.name"></b-form-input>
                            </b-form-group>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <b-form-group label="E-mail" label-for="email">
                                <b-form-input id="email" type="text" autocomplete="off" v-model="form.email"></b-form-input>
                            </b-form-group>
                        </div>

                        <div class="col-md-4">
                            
                        </div>
                    </div>
                </b-form>

                <hr />

                <div class="row">
                    <div class="col-md-12 right">
                        <b-button type="button" variant="success" block @click="salvarUsuario">
                            <font-awesome-icon icon="fa-solid fa-save" />
                        </b-button>

                        <b-button type="button" variant="light" block >
                            <font-awesome-icon icon="fa-solid fa-close" @click="$bvModal.hide('novoUsuarioModal');"/>
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
                    name: null,
                    email: null,
                    status: null,
                    tipo: null,
                    imagem: null
                },

                activeClass: 'activeClass',
                inactiveClass: '',
                hoje: null,
                tipos: [],
                tipo: null,
                
                image: '/images/user-admin.png',
                hide: 'hide'
            }
        },

        created: function() {
            this.zeraCampos();
            this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');
            this.getUserTypes();
        },

        methods: {
            salvarUsuario() {
                if (
                    this.form.name != ""
                    && this.form.email != ""
                ) {
                    this.form.tipo = this.tipo;
                    axios.post(api.usuario, this.form, this.header).then(response => {
                        if (response.data.status == 0) {
                            this.$toast.success(response.data.message);

                            this.$emit('atualizarTabela');
                        } else {
                            this.$toast.warning(response.data.message);
                        }

                        this.zeraCampos();
                    }).catch(error => {
                        if (error.response.status == 400) {
                            this.$toast.warning(error.response.data.message);
                        } else {
                            this.$toast.error(error.response.data.message);
                        }
                        
                        this.zeraCampos();
                    });
                } else {
                    this.$toast.warning("Preencha todos os campos obrigatórios");
                }
            },
            
            preencheCampos(item) {
                this.form.id = item.id;
                this.form.name = item.name;
                this.form.email = item.email;
                this.form.status = item.status;
                this.form.administrador = item.administrador;
                
                if (item.imagem != null) {
                    this.image = item.imagem;
                }
            },

            zeraCampos() {
                this.form.id = null;
                this.form.name = null;
                this.form.email = null;
                this.form.status = null;
                this.form.tipo = null;
                
                this.image = '/images/user-admin.png';

                this.tipo = 'Comum';
            },

            getUserTypes() {
                axios.get(api.usuario_tipo, this.header).then(response => {
                    this.tipos = response.data.data;
                }).catch(error => {
                    if (error.response.status == 400) {
                        this.$toast.warning(error.response.data.message);
                    } else {
                        this.$toast.error(error.response.data.message);
                    }
                });
            },

            preencheTipo(item) {
                if (item != null) {
                    this.tipo = item.tipo;
                }
            },

            uploadImage(e) {
                const image = e.target.files[0];
                const reader = new FileReader();
                reader.readAsDataURL(image);
                reader.onload = e =>{
                    this.form.imagem = e.target.result;
                    this.image = e.target.result;
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
    margin-bottom: 35px;
}

.center {
    text-align: center;
}
</style>
