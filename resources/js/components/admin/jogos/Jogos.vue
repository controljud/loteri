<template>
    <div class="container">
        <div class="card card-content">
            <div class="row">
                <div class="col-md-12">
                    <h3>Jogos</h3>
                    <p>Aqui estão os jogos disponívei</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    
                </div>

                <div class="col-md-6 right">
                    <b-button size="sm" variant="success" @click="zeraCampos(); $bvModal.show('novoJogoModal');">
                        <font-awesome-icon icon="fa-solid fa-plus" />
                    </b-button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <Tabela
                        :idTabela="idTabela"
                        :items="items"
                        :fields="fields"
                        :last_page="last_page"
                        :isBusy="isBusy"
                        v-on:linkGen="linkGen"
                        v-on:edit="edit"
                        v-on:doConfirm="doConfirm"
                    ></Tabela>
                </div>
            </div>

            <NovoJogoModal
                :idJogo="jogo"
                v-on:atualizarTabela="atualizarTabela"
                ref="novoJogoModal"
            ></NovoJogoModal>

            <ConfirmModal
                :texto="texto"
                v-on:doDelete="doDelete"
                ref="confirmModal"
            ></ConfirmModal>
        </div>
    </div>
</template>

<script>
import { throwStatement } from '@babel/types';
import {api} from '../../../config';
import Tabela from '../../shared/Tabela.vue';
import NovoJogoModal from './NovoJogoModal.vue';
import ConfirmModal from '../../shared/ConfirmModal.vue';

export default ({
    components: {
        Tabela,
        NovoJogoModal,
        ConfirmModal
    },

    data() {
        return {
            header: {
                headers: {
                    Authorization: null
                }
            },
            idTabela: 1,
            items: null,
            item: null,
            fields: [
                { key: 'imagem', label: '', class: 'text-center' },
                { key: 'jogo', label: 'Jogo'},
                { key: 'quantidade_dezenas', label: 'Quantidade de Dezenas', class: 'text-center' },
                { key: 'quantidade_acertos', label: 'Quantidade de Acertos', class: 'text-center' },
                { key: 'status', label: 'Status', class: 'text-center' },
                { key: 'actions', label: 'Ações', class: 'text-center' }
            ],
            last_page: 1,
            currentPage: 1,
            isBusy: false,
            jogo: 1,
            jogos: [
                { value: null, text: '--- Selecione um jogo ---' },
                { value: 1, text: 'Mega Sena' },
            ],
            isLoading: false,
            texto: ''
        }
    },

    created: function() {
        this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');

        this.getData(this.currentPage);
    },

    methods: {
        getData(page) {
            this.isBusy = true;

            let url = api.jogos + '?page=' + page;

            axios.get(url, this.header).then(response => {
                let rows = [];

                if (response.data.data != null) {
                    let retorno = response.data.data;

                    this.currentPage = retorno.current_page ? retorno.current_page : 1;
                    this.last_page = retorno.last_page ? retorno.last_page : 1;

                    this.items = retorno;
                }

                this.isBusy = false;
            }).catch(error => {
                this.items = null;
                
                if (error.response.status == 401) {
                    localStorage.removeItem('token');

                    window.location.href = "/";
                }

                this.isBusy = false;
            });
        },

        updateData() {
            this.isLoading = true;
            let body = {
                'id_jogo': this.jogo
            }

            axios.put(api.sorteios, body, this.header).then(response => {
                if (response.status == 200) {
                    if (response.data.data.adicionados > 0) {
                        this.$toast.success(response.data.message);

                        this.updateTotaisJogos(api);
                    } else {
                        this.$toast.success("Não há sorteios novos");
                    }
                }
                
                this.isLoading = false;
            }).catch(error => {
                this.items = null;
                
                if (error.response.status == 401) {
                    localStorage.removeItem('token');

                    window.location.href = "/";
                }

                this.isLoading = false;
            });
        },

        updateTotaisJogos(api) {
            let body = {
                'id_jogo': this.jogo
            }

            axios.put(api.totais, body, this.header).then(retorno => {
                this.getData(1);
            });
        },

        linkGen(page) {
            this.getData(page);
        },

        edit(item) {
            this.$bvModal.show('novoJogoModal');
            this.$refs.novoJogoModal.preencheCampos(item);
        },

        zeraCampos() {
            this.$refs.novoJogoModal.zeraCampos();
        },

        doConfirm(item) {
            this.$refs.confirmModal.preencheItem(item);
            this.$bvModal.show('confirmaModal');
        },

        doDelete(item) {
            if (item != null) {
                this.deleteJogo(item);
            } else {
                this.$toast.warning("Não há jogo selecionado");
            }
        },

        deleteJogo(item) {
            let url = api.jogo + '/' + item.id;

            axios.delete(url, this.header).then(response => {
                this.$toast.success(response.data.message);
                this.getData(this.currentPage);
            }).catch(error => {
                if (error.response.status == 400) {
                    this.$toast.warning(error.response.data.message);
                } else {
                    this.$toast.error(error.response.data.message);
                }
            });
        },

        atualizarTabela() {
            this.$bvModal.hide('novoSorteioModal');
            this.getData(this.currentPage);
        }
    }
})
</script>

<style>
.right {
    text-align: right;
}

.card-content {
	margin-top: 10px;
	padding: 10px;
}
</style>