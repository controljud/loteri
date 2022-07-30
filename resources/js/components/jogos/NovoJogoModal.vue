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
                            <b-form-group label="Data do sorteio" label-for="data">
                                <b-form-input type="date" v-model="form.data"></b-form-input>
                            </b-form-group>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <b-form-group label="Dezenas" label-for="dezenas">
                                <b-form-input type="text" autocomplete="off" placeholder="Dezenas, separadas por vírgula (,)" v-model="form.dezenas"></b-form-input>
                            </b-form-group>
                        </div>
                    </div>
                </b-form>
                
                <hr />

                <div class="row">
                    <div class="col-md-12 right">
                        <b-button type="button" variant="success" block @click="salvarSorteio">
                            <font-awesome-icon icon="fa-solid fa-save" />
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
        data() {
            return {
                header: {
					headers: {
						Authorization: null
					}
				},

                form: {
                    id: null,
                    numero: null,
                    data: null,
                    dezenas: null
                },

                ultimoSorteio: null
            }
        },

        created: function() {
            this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');

            axios.get(api.sorteio_atual, this.header).then(response => {
                this.ultimoSorteio = response.data.data;
                
                if (response.data.status == 0 && this.ultimoSorteio.dezenas == null) {
                    this.form.numero = this.ultimoSorteio.numero;
                    this.form.data = this.ultimoSorteio.data;
                } else {
                    this.form.numero = (this.ultimoSorteio.numero + 1);
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
                    && (this.form.dezenas != null && this.validaDezenas(this.form.dezenas))
                ) {
                    this.form.dezenas = (this.form.dezenas.split(',')).join('-');
                    let user = JSON.parse(localStorage.getItem('user'));
                    this.form.id_user = user.id;
                    
                    axios.post(api.aposta, this.form, this.header).then(response => {
                        if (response.data.status == 0) {
                            this.$toast.success("Aposta cadastrada com sucesso");
                            $bvModal.hide('novoJogoModal');
                        } else {
                            this.$toast.warning(response.data.mensagem);
                        }
                    }).catch(error => {
                        console.log(error);
                        this.$toast.danger("Erro reportado. Tente novamente mais tarde");
                    });
                } else {
                    this.$toast.warning("Preencha todos os campos");
                }
            },

            validaDezenas(dezenas) {
                let dezenasArray = dezenas.split(',');
                if (dezenasArray.length < 6) {
                    return false;
                }

                return true;
            }
        }
    }
</script>

<style scoped>
.right {
    text-align: right;
}
</style>
