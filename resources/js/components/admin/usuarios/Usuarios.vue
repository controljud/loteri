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

            <NovoUsuarioModal
                :idJogo="jogo"
                v-on:atualizarTabela="atualizarTabela"
                ref="novoUsuarioModal"
            ></NovoUsuarioModal>
        </div>
    </div>
</template>

<script>
import { throwStatement } from '@babel/types';
import {api} from '../../../config';
import Tabela from '../../shared/Tabela.vue';
import NovoUsuarioModal from './NovoUsuarioModal.vue';
import ConfirmModal from '../../shared/ConfirmModal.vue';

export default ({
    components: {
        Tabela,
        NovoUsuarioModal,
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
                { key: 'id', label: 'ID', class: 'text-center' },
                { key: 'name', label: 'Nome' },
                { key: 'email', label: 'E-mail' },
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
            isLoading: false
        }
    },

    created: function() {
        this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');

        this.getData(this.currentPage);
    },

    methods: {
        getData(page) {
            this.isBusy = true;

            let url = api.usuarios + '?page=' + page;

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
            this.$bvModal.show('novoSorteioModal');
        },

        doConfirm(item) {
            this.$refs.confirmaModal.preencheItem(item);
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
            let url = api.sorteio + '/' + item.id;
				
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