<template>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h3>Meus Jogos</h3>
				<p>Aqui estão seus jogos feitos. Tenha vários dados sobre seus jogos</p>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<b-form-group >
					<b-form-input type="text" autocomplete="off" placeholder="Busque seus jogos" v-model="filter" v-on:keyup="filtrar"></b-form-input>
				</b-form-group>
			</div>
			<div class="col-md-6 right">
				<b-button size="sm" class="btn-success" @click="$bvModal.show('novoJogoModal')">
					<font-awesome-icon icon="fa-solid fa-plus" />
				</b-button>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<b-table responsive striped hover
					id="meusjogos"
					:items="items"
					:fields="fields"
				>
					<template #cell(acertos)="item">
						<b-form-rating v-if="item.value == 0" id="rating-6" v-model="item.value" stars="6" disabled></b-form-rating>
						<b-form-rating v-if="item.value > 0 && item.value <= 3" id="rating-6" v-model="item.value" stars="6" variant="warning" readonly></b-form-rating>
						<b-form-rating v-if="item.value > 3 && item.value <= 5" id="rating-6" v-model="item.value" stars="6" variant="primary" readonly></b-form-rating>
						<b-form-rating v-if="item.value > 5" id="rating-6" v-model="item.value" stars="6" variant="success" readonly></b-form-rating>
					</template>

					<template #cell(actions)>
						<b-button size="sm" class="btn btn-sm btn-danger">
							<font-awesome-icon icon="fa-solid fa-trash" />
						</b-button>
					</template>

					<template #table-busy>
						<div class="text-center text-danger my-2">
							<b-spinner class="align-middle"></b-spinner>
							<strong>Loading...</strong>
						</div>
					</template>
				</b-table>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<paginate
					:page-count="last_page"
					:click-handler="linkGen"
					:prev-text="'<<'"
					:next-text="'>>'"
					:container-class="'pagination'"
				></paginate>
			</div>
		</div>

		<novo-jogo-modal
			v-on:atualizarTabela="atualizarTabela"
		></novo-jogo-modal>
	</div>
</template>

<script>
	import {api} from '../../config';
	import NovoJogoModal from '../jogos/NovoJogoModal.vue';
	import Paginate from 'vuejs-paginate';

	export default {
		components: {
			'novo-jogo-modal': NovoJogoModal,
			'paginate': Paginate
		},

		data() {
            return {
                header: {
					headers: {
						Authorization: null
					}
				},
				items: null,
				fields: [
					{ key: 'numero', label: 'Sorteio', class: 'text-center' },
					{ key: 'apostado', label: 'Apostado', class: 'text-center' },
					{ key: 'descricao', label: 'Descrição', class: 'text-left' },
					{ key: 'data', label: 'Data Sorteio', class: 'text-center' },
					{ key: 'sorteado', label: 'Sorteio', class: 'text-center' },
					{ key: 'acertos', label: 'Acertos', class: 'text-center' },
					{ key: 'actions', label: 'Ações', class: 'text-center' }
				],
				filter: null,
				currentPage: 1,
				rows: 1,
				baseUrl: null,
				perPage: 10,
				last_page: 1
            }
        },

		created: function() {
			this.getData();
		},

		methods: {
			getData(page) {
				this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');

				let filtro = this.filter == null || this.filter == '' ? 0 : this.filter;
				let url = '';

				if (page != this.currentPage) {
					url = api.apostas + '/' + filtro + '?page=' + page;
				} else {
					url = api.apostas + '/' + filtro;
				}

				axios.get(url, this.header).then(response => {
					let rows = [];

					this.currentPage = 1;
					this.perPage = 10;
					this.rows = 1;
					this.baseUrl = null;
					this.last_page = 1;

					if (response.data.data != null) {
						let retorno = response.data.data;

						this.currentPage = retorno.current_page;
						this.rows = retorno.total;
						this.perPage = retorno.per_page;
						this.baseUrl = retorno.path;
						this.last_page = retorno.last_page;

						let dados = retorno.data;
						dados.map(function(aposta) {
							let apostadoFormated = JSON.parse(aposta.apostado).join('-');
							let acertos = 0;

							if (aposta.sorteado != null) {
								let sorteado = JSON.parse(aposta.sorteado);
								let sorteadoFormated = sorteado.join('-');

								JSON.parse(aposta.apostado).map(function(dezena) {
									if (sorteado.find(function(elemento) { return elemento == dezena; }) == dezena) {
										acertos++;
									}
								});

								aposta.sorteado = sorteadoFormated;
							}
							aposta.apostado = apostadoFormated;
							aposta.acertos = acertos;

							rows.push(aposta);
						});
					}

					this.items = rows;
				}).catch(error => {
					if (error.response.status == 401) {
						localStorage.removeItem('token');

						window.location.href = "/";
					}
					console.log(error.data);
				});
			},

			filtrar() {
				if ((this.filter.length >= 2) || (this.filter == null || this.filter == '')) {
					this.getData(1);
				}
			},

			linkGen(page) {
				this.getData(page);
			},

			atualizarTabela() {
				this.filter = null;
				this.getData(1);
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
</style>