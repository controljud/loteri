<template>
    <div class="container">
        <div class="card card-content">
            <div class="row">
                <div class="col-md-12">
                    <h3>Usuários</h3>
                    <p>Abaixo os usuários cadastrados no Lotery Map</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6"></div>

                <div class="col-md-6 right">
                    <b-button size="sm" variant="success" @click="zeraCampos(); $bvModal.show('novoUsuarioModal');">
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
                        :tipo="tipo"
                    ></Tabela>
                </div>
            </div>

            <NovoUsuarioModal
                :idJogo="jogo"
                v-on:atualizarTabela="atualizarTabela"
                ref="novoUsuarioModal"
            ></NovoUsuarioModal>

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
                { key: 'imagem', label: '', class: 'text-center' },
                { key: 'name', label: 'Nome' },
                { key: 'email', label: 'E-mail' },
                { key: 'tipo', label: 'Tipo', class: 'text-center' },
                { key: 'status', label: 'Status', class: 'text-center' },
                { key: 'actions', label: 'Ações', class: 'text-center' }
            ],
            jogo: 1,
            last_page: 1,
            currentPage: 1,
            isBusy: false,
            isLoading: false,
            tipo: 'usuario',

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

            let url = api.usuarios + '?page=' + page;

            axios.get(url, this.header).then(response => {
                let rows = [];
                let retorno = response.data.data;

                if (response.data.status == 0) {
                    this.currentPage = retorno.current_page ? retorno.current_page : 1;
                    this.last_page = retorno.last_page ? retorno.last_page : 1;

                    this.items = retorno.data;
                }

                this.isBusy = false;
            }).catch(error => {
                this.items = null;
                
                this.isBusy = false;
            });
        },

        linkGen(page) {
            this.getData(page);
        },

        edit(item) {
            this.$bvModal.show('novoUsuarioModal');
            this.$refs.novoUsuarioModal.preencheCampos(item);
            this.$refs.novoUsuarioModal.preencheTipo(item);
        },

        zeraCampos() {
            this.$refs.novoUsuarioModal.zeraCampos();
        },

        doConfirm(item) {
            this.$refs.confirmModal.preencheItem(item);
            this.$bvModal.show('confirmaModal');
        },

        doDelete(item) {
            if (item != null) {
                this.deleteUsuario(item);
            } else {
                this.$toast.warning("Não há usuário selecionado");
            }
        },

        deleteUsuario(item) {
            let url = api.usuario + '/' + item.id;

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
            this.$bvModal.hide('novoUsuarioModal');
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