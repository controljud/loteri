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

        <div class="row">
            <div class="col-md-8">
                <div class="card card-content">
                    <h3>Quantidade de apostas por mês</h3>
                    <Bar
                        id="my-chart-id"
                        :options="qtdApostasMensaisOptions"
                        :data="qtdApostasMensaisData"
                    />
                </div>
            </div>
            <div class="col-md-4">
                <b-card bg-variant="warning" text-variant="white" >
                    <span class="number">28</span><br />
                    <hr />
                    <b-icon icon="star"></b-icon>
                    Dezembro - Mês com mais apostas
                </b-card>
            </div>
        </div>
    </div>
</template>

<script>
import {api} from '../../../config';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

export default {
    components: {
        Bar
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
            qtdUsuarios: 0,
            qtdApostasMensaisOptions: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            },
            qtdApostasMensaisData: {
                labels: [],
                datasets: [ { data: [] } ]
            },
        }
    },

    created: function() {
        this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');

        this.getQuantidadeJogos();
        this.getQuantidadeSorteios();
        this.getQuantidadeApostas();
        this.getQuantidadeUsuarios();
        this.getQuantidadeApostasMensais();
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

        getQuantidadeApostasMensais() {
            axios.get(api.apostas_quantidade_mensal, this.header).then(response => {
                if (response.status == 200) {
                    let quantidades = response.data.data.apostas;
                    let qtdApostasMensaisLabels = [];
                    let qtdApostasMensaisDatasets = [];
                    
                    quantidades.map(response => {
                        qtdApostasMensaisLabels.push(response.data_formatada);
                        qtdApostasMensaisDatasets.push(response.quantidade);
                    });

                    this.qtdApostasMensaisData = {
                        labels: qtdApostasMensaisLabels,
                        datasets: [ { 
                            label: false,
                            data: qtdApostasMensaisDatasets,
                            backgroundColor: [
                                'rgba(255, 0, 0, 0.7)',
                                'rgba(255, 255, 0, 0.7)',
                                'rgba(75, 0, 130, 0.7)',
                                'rgba(107, 142, 35, 0.7)',
                                'rgba(0, 128, 0, 0.7)',
                                'rgba(70, 130, 180, 0.7)',
                                'rgba(79, 79, 79, 0.7)',
                                'rgba(250, 128, 114, 0.7)',
                                'rgba(255, 105, 180, 0.7)',
                                'rgba(222, 184, 135, 0.7)',
                                'rgba(189, 83, 107, 0.7)',
                                'rgba(60, 179, 113, 0.7)',
                            ],
                        } ]
                    };
                    this.qtdApostasMensaisOptions = {
                        responsive: true
                    };
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

.row-chart {
    max-height: 200px;
}
</style>
