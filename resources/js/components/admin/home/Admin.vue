<template>
    <div class="container">
        <div class="row dv_top">
            <div class="col-md-3">
                <b-card bg-variant="danger" text-variant="white">
                    <span class="number">{{ qtdJogos }}</span><br />
                    <hr />
                    <b-icon icon="caret-right-fill"></b-icon>
                    Jogo
                </b-card>
            </div>
            <div class="col-md-3">
                <b-card bg-variant="success" text-variant="white" >
                    <span class="number">{{  qtdSorteios }}</span><br />
                    <hr />
                    <b-icon icon="star"></b-icon>
                    Sorteios
                </b-card>
            </div>
            <div class="col-md-3">
                <b-card bg-variant="info" text-variant="white" >
                    <span class="number">{{ qtdApostas }}</span><br />
                    <hr />
                    <b-icon icon="star-fill"></b-icon>
                    Apostas
                </b-card>
            </div>
            <div class="col-md-3">
                <b-card bg-variant="primary" text-variant="white" >
                    <span class="number">{{ qtdUsuarios }}</span><br />
                    <hr />
                    <b-icon icon="person"></b-icon>
                    Usuários
                </b-card>
            </div>
        </div>
    </div>
</template>

<script>
import {api} from '../../../config';

export default {
    components: {
        
    },

    data() {
        return {
            header: {
                headers: {
                    Authorization: null
                }
            },
            qtdJogos: 0,
            qtdSorteios: 0,
            qtdApostas: 0,
            qtdUsuarios: 0
        }
    },

    created: function() {
        this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');

        this.getQuantidadeJogos();
        this.getQuantidadeSorteios();
        this.getQuantidadeApostas();
        this.getQuantidadeUsuarios();
    },

    methods: {
        getQuantidadeJogos() {
            axios.get(api.jogo_quantidade, this.header).then(response => {
                if (response.status == 200) {
                    this.qtdJogos = response.data.data.quantidade;
                }
            }).catch(error => {
                if (error.response.status == 401) {
                    localStorage.removeItem('token');

                    window.location.href = "/";
                }
            });
        }, 

        getQuantidadeSorteios() {
            axios.get(api.sorteios_quantidade, this.header).then(response => {
                if (response.status == 200) {
                    this.qtdSorteios = response.data.data.quantidade;
                }
            }).catch(error => {
                if (error.response.status == 401) {
                    localStorage.removeItem('token');

                    window.location.href = "/";
                }
            });
        },

        getQuantidadeApostas() {
            axios.get(api.apostas_quantidade, this.header).then(response => {
                if (response.status == 200) {
                    this.qtdApostas = response.data.data.quantidade;
                }
            }).catch(error => {
                if (error.response.status == 401) {
                    localStorage.removeItem('token');

                    window.location.href = "/";
                }
            });
        },

        getQuantidadeUsuarios() {
            axios.get(api.usuario_quantidade, this.header).then(response => {
                if (response.status == 200) {
                    this.qtdUsuarios = response.data.data.quantidade;
                }
            }).catch(error => {
                if (error.response.status == 401) {
                    localStorage.removeItem('token');

                    window.location.href = "/";
                }
            });
        },
    }
}
</script>

<style scoped>
.dv_top {
    margin-top: 35px;
}

.card {
    margin-bottom: 15px;
}

.number {
    font-size: 36px;
}
</style>
