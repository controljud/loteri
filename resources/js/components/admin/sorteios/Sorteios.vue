<template>
    <div class="container">
        <div class="card card-content">
            <div class="row">
                <div class="col-md-12">
                    <h3>Sorteios</h3>
                    <p>Aqui estão os sorteios realizados</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <b-form-select v-model="jogo" :options="jogos" size="sm" class="form-control" v-on:change="getData(1)"></b-form-select>
                </div>

                <div class="col-md-6 right">
                    <b-spinner variant="primary" small label="Spinning" v-if="isLoading"></b-spinner>

                    <b-button size="sm" variant="success" @click="$bvModal.show('novoSorteioModal');">
                        <font-awesome-icon icon="fa-solid fa-plus" />
                    </b-button>

                    <b-button size="sm" variant="primary">
                        <b-icon icon="arrow-down-circle-fill" @click="updateData"></b-icon>
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

            <NovoSorteioModal
                :idJogo="jogo"
                v-on:atualizarTabela="atualizarTabela"
                ref="novoSorteioModal"
            ></NovoSorteioModal>

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
import NovoSorteioModal from './NovoSorteioModal.vue';
import ConfirmModal from '../../shared/ConfirmModal.vue';

export default ({
    components: {
        Tabela,
        NovoSorteioModal,
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
                { key: 'numero', label: 'Sorteio', class: 'text-center', isRowHeader: true },
                { key: 'dezenas', label: 'Dezenas', class: 'text-center' },
                { key: 'data', label: 'Data do Sorteio', class: 'text-center' },
                { key: 'premio', label: 'Prêmio', class: 'text-left' },
                { key: 'actions', label: 'Ações', class: 'text-center' }
            ],
            last_page: 1,
            currentPage: 1,
            isBusy: false,
            jogo: 1,
            jogos: null,
            isLoading: false,
            texto: ''
        }
    },

    created: function() {
        this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');

        this.getJogos();
        this.getData(this.currentPage);
    },

    methods: {
        getData(page) {
            this.isBusy = true;

            let url = api.sorteios + '/' + this.jogo + '?page=' + page;

            axios.get(url, this.header).then(response => {
                let rows = [];

                if (response.data.data != null) {
                    let retorno = response.data.data;

                    this.currentPage = retorno.current_page ? retorno.current_page : 1;
                    this.last_page = retorno.last_page ? retorno.last_page : 1;

                    let dados = retorno.data;
                    dados.map(function(sorteio) {
                        let dezenas = JSON.parse(sorteio.dezenas).join('-');

                        sorteio.dezenas = dezenas;
                        
                        rows.push(sorteio);
                    })

                    this.items = rows;
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

                        this.getData(1);

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
                console.log('Totals generated');
            });
        },

        linkGen(page) {
            this.getData(page);
        },

        edit(item) {
            this.$bvModal.show('novoSorteioModal');
            this.$refs.novoSorteioModal.preencheCampos(item);
        },

        doConfirm(item) {
            this.$refs.confirmModal.preencheItem(item);
            this.$bvModal.show('confirmaModal');
        },

        doDelete(item) {
            if (item != null) {
                this.deleteSorteio(item);
            } else {
                this.$toast.warning("Não há sorteio selecionado");
            }
        },

        deleteSorteio(item) {
            let url = api.sorteio + '/' + this.jogo + '/' + item.numero;
				
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
        },

        getJogos() {
            let url = api.jogos;
            axios.get(url, this.header).then(response => {
                if (response.data.status == 0) {
                    let jogos = response.data.data;

                    this.jogos = [
                        { value: null, text: '--- Selecione um jogo ---' }
                    ];

                    jogos.map(jogo => {
                        this.jogos.push({ value: jogo.id, text: jogo.jogo });
                    });
                }
            }).catch(error => {
                if (error.response.status == 400) {
                    this.$toast.warning(error.response.data.message);
                } else {
                    this.$toast.error(error.response.data.message);
                }
            });
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