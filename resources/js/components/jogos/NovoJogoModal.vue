<template>
    <div>
        <b-modal id="novoJogoModal" hide-footer hide-header-close>
            <template #modal-title>Novo Jogo</template>
            <div class="d-block text-left">
                <b-form>
                    <div class="row">
                        <div class="col-md-6">
                            <b-form-group label="Numero do sorteio" label-for="numero">
                                <b-form-input id="numero" type="text" autocomplete="off" v-model="form.numero"></b-form-input>
                            </b-form-group>
                        </div>
                        <div class="col-6">
                            <b-form-group label="Data da aposta" label-for="data">
                                <b-form-input type="date" v-model="form.data"></b-form-input>
                            </b-form-group>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <b-form-group label="Descrição" label-for="descricao">
                                <b-form-input type="text" autocomplete="off" placeholder="Que tipo de aposta é? Pessoal, Bolão..." v-model="form.descricao"></b-form-input>
                            </b-form-group>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Dezenas</label>
                            <div class="v_dezenas" :key="v_dezenas">
                                <div v-for="y in tamanho" :key="y" class="v_dezena" v-on:click="editDezenas(y)" :class="[dezenas.indexOf(y) > -1 ? activeClass : inactiveClass]" :ref="'dezena_' + y" :id="'dezena_' + y">
                                    <span v-if="y < 10">0{{ y }}</span>
                                    <span v-if="y >= 10">{{ y }}</span>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    {{ dezenas.length }}<span v-if="dezenas.length == 1"> dezena selecionada</span><span v-if="dezenas.length != 1"> dezenas selecionadas</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-light" v-on:click="limparDezenas">Limpar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </b-form>
                
                <hr />

                <div class="row">
                    <div class="col-md-12 right">
                        <b-button type="button" variant="success" block @click="salvarSorteio">
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
    import {api} from './../../config';

    export default {
        props: [
            'item'
        ],

        data() {
            return {
                header: {
					headers: {
						Authorization: null
					}
				},

                dezenaIndex: 0,
                dezenas: [],
                tamanho: 60,
                form: {
                    id: null,
                    numero: null,
                    data: null,
                    descricao: null,
                    dezenas: null
                },

                ultimoSorteio: null,
                activeClass: 'activeClass',
                inactiveClass: '',
                v_dezenas: 0, 
                hoje: null
            }
        },

        created: function() {
            this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');

            axios.get(api.sorteio_atual, this.header).then(response => {
                this.ultimoSorteio = response.data.data[0];
                this.hoje = response.data.data[1];

                if (response.data.status == 0 && this.ultimoSorteio.dezenas == null) {
                    this.form.numero = this.ultimoSorteio.numero + 1;
                    this.form.data = this.hoje;
                } else {
                    this.form.numero = (this.ultimoSorteio.numero) + 1;
                    this.form.data = this.hoje;
                }
            }).catch(error => {
                console.log(error);
            });
        },

        methods: {
            salvarSorteio() {
                if (
                    this.form.numero != null
                    && this.form.data != null
                    && (this.dezenas.length > 5 && this.dezenas.length < 16)
                ) {
                    let user = JSON.parse(localStorage.getItem('user'));
                    this.form.id_user = user.id;
                    this.form.dezenas = this.dezenas.join('-');

                    axios.post(api.aposta, this.form, this.header).then(response => {
                        if (response.data.status == 0) {
                            this.$toast.success(response.data.message);

                            this.form.descricao = null;
                            this.form.dezenas = null;
                            this.zeraCampos();

                            this.$emit('atualizarTabela');
                        } else {
                            this.$toast.warning(response.data.mensage);
                        }
                    }).catch(error => {
                        console.log(error.response.data.message);
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

            editDezenas(dezena) {
                let index = this.dezenas.indexOf(dezena);

                if (index > -1) {
                    this.dezenas.splice(index, 1);
                } else if (index < 0 && this.dezenas.length < 15) {
                    this.dezenas.push(dezena);
                }
            },

            limparDezenas() {
                this.dezenas = [];
            },
            
            preencheCampos(item) {
                this.form.id = item.id;
                this.form.descricao = item.descricao;
                this.form.numero = item.numero;

                let dt = item.data_aposta.split('/');
                this.form.data = dt[2] + '-' + dt[1] + '-' + dt[0];
                
                this.dezenas = [];
                let dezenas = item.apostado.split('-');
                for (let i = 0; i < dezenas.length; i++) {
                    this.dezenas.push(dezenas[i] * 1);
                }

                this.selectDezenas();
            },

            zeraCampos() {
                this.form.id = null;
                this.form.descricao = null;
                this.form.numero = this.ultimoSorteio.numero + 1;
                this.form.data = this.hoje;

                this.limparDezenas();
            },

            selectDezenas() {
                setTimeout(() => {
                    for (let i = 1; i <= this.tamanho; i++) {
                        if (this.dezenas.indexOf(numero) > -1) {
                            let elementz = 'dezena_' + i.toString();

                            this.$refs[elementz][0].classList.add('activeClass');
                        }
                    }
                }, 400);
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
</style>
