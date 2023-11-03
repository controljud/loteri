<template>
    <div class="container">
        <div class="card card-content">
            <div class="container mt-4 mb-4 p-3 d-flex justify-content-center"> 
                <div class="row">
                    <div class="col-md-12">
                        <div class=" image d-flex flex-column justify-content-center align-items-center">
                            <img :src="image" class="img-perfil"/>

                            <h3>{{ usuario.name }}</h3>

                            <span class="idd">{{ usuario.email }}</span>

                            <div class="d-flex flex-row justify-content-center align-items-center mt-3"> 
                                <p v-for="aposta in apostas" v-bind:key="aposta.jogo">{{ aposta.nome }}: {{ aposta.quantidade }} <span class="follow">Apostas</span></p>
                            </div> 

                            <div class=" px-2 rounded mt-4 date "> 
                                <span class="join">Inscrito desde {{ usuario.created_at }}</span><br />
                                <span class="join">Última atualização: {{ usuario.updated_at }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 right">
                        <a class="btn btn-sm btn-primary" href="javascript:void(0)">Editar Perfil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { throwStatement } from '@babel/types';
import {api} from '../../config';
import PerfilModal from './PerfilModal.vue';

export default ({
    components: {
        PerfilModal
    },

    data() {
        return {
            header: {
                headers: {
                    Authorization: null
                }
            },
            usuario: null,
            data_inscricao: null,
            data_atualizacao: null,
            apostas: [],
            image: '/images/user-admin.png'
        }
    },

    created: function() {
        this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');
        this.usuario = JSON.parse(localStorage.getItem('user'));

        if (this.usuario.imagem != null) {
            this.image = this.usuario.imagem;
        }

        this.getApostas();
    },

    methods: {
        getApostas() {
            let url = api.jogos;

            axios.get(api.jogos_todos, this.header).then(response => {
                this.jogos = response.data.data;
                
                this.jogos.forEach(jogo => {
                    axios.get(api.jogo_quantidade_usuario + '/' + jogo.id + '/' + this.usuario.id, this.header).then(resposta => {
                        let quantidade = resposta.data.data.quantidade;

                        if (quantidade > 0) {
                            let jg = {
                                nome: jogo.jogo,
                                quantidade: quantidade
                            }

                            this.apostas.push(jg);
                        }
                    });
                });
            }).catch(error => {
                
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

.img-perfil {
    width: 200px; 
    height: 200px; 
    border-radius: 100%; 
    box-shadow: 0px 3px 5px 0px rgba(184,175,184,1); 
    margin-bottom: 50px;
}
</style>