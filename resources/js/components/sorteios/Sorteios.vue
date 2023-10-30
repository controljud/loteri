<template>
	<div class="container">
		<div class="card card-content">
			<div class="row">
				<div class="col-md-12">
					<h3>Sorteios realizados</h3>
					<p>Aqui estão todos os sorteios já realizados nos jogos disponíveis no nosso app</p>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<b-form-select v-model="jogo" :options="jogos" size="sm" class="form-control" v-on:change="getData(1)"></b-form-select>
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
                    ></Tabela>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import {api} from '../../config';
	import Paginate from 'vuejs-paginate';
	import Tabela from '../shared/Tabela.vue';

	export default {
		components: {
			'paginate': Paginate,
			Tabela
		},

		data() {
            return {
                header: {
					headers: {
						Authorization: null
					}
				},
				items: null,
				item: null,
				fields: [
					{ key: 'numero', label: 'Sorteio', class: 'text-center', isRowHeader: true },
					{ key: 'dezenas', label: 'Dezenas', class: 'text-center' },
					{ key: 'data', label: 'Data do Sorteio', class: 'text-center' },
					{ key: 'premio', label: 'Prêmio', class: 'text-left' }
				],
				currentPage: 1,
				rows: 1,
				perPage: 10,
				last_page: 1,

				jogos: null,
				jogo: 1,

				isBusy: false,
				isLoading: false,
				
				idTabela: 1,
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
				}).catch(error => {
					this.items = null;
					
					if (error.response.status == 401) {
						localStorage.removeItem('token');

						window.location.href = "/";
					}

					this.isBusy = false;
				});
			},

			linkGen(page) {
				this.getData(page);
			},

			getJogos() {
				let url = api.jogosCombo;
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
	}
</script>

<style>
.right {
	text-align: right;
}

.pagination {
	list-style: none;
	display: flex;
}

.pagination li {
	width: 30px;
	height: 30px;
	display: flex;
	justify-content: center;
	align-items: center;
	border: 1px solid #D4E6F1;
	cursor: pointer;
}

.pagination li:hover {
	background-color: #D4E6F1;
}

.pagination li:active {
	background-color: #2980B9;
}

.active {
	background-color: #2980B9;
}

.pagination li a {
	text-decoration: none;
	display: block;
	color: #2980B9;
}

.pagination li.active a {
	color: white;
}

.card-content {
	margin-top: 10px;
	padding: 10px;
}
</style>