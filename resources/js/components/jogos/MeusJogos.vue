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
				<b-button size="sm" class="btn-success" @click="$bvModal.show('novoJogoModal'); zeraCamposModal()">
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
					:busy="isBusy"
				>
					<template #cell(acertos)="item">
						<b-form-rating v-if="item.value == 0" id="rating-6" v-model="item.value" stars="6" disabled></b-form-rating>
						<b-form-rating v-if="item.value > 0 && item.value <= 3" id="rating-6" v-model="item.value" stars="6" variant="warning" readonly></b-form-rating>
						<b-form-rating v-if="item.value > 3 && item.value <= 5" id="rating-6" v-model="item.value" stars="6" variant="primary" readonly></b-form-rating>
						<b-form-rating v-if="item.value > 5" id="rating-6" v-model="item.value" stars="6" variant="success" readonly></b-form-rating>
					</template>

					<template #cell(actions)="row">
						<b-button-group>
							<b-button size="sm" class="btn btn-sm btn-primary" @click="$bvModal.show('novoJogoModal'); edit(row.item)">
								<font-awesome-icon icon="fa-solid fa-edit"/>
							</b-button>
							
							<b-button size="sm" class="btn btn-sm btn-danger" @click="$bvModal.show('confirmModal'); doDelete(row.item)">
								<font-awesome-icon icon="fa-solid fa-trash" />
							</b-button>
						</b-button-group>
					</template>

					<template #table-busy>
						<div class="text-center text-success my-2">
							<b-spinner class="align-middle"></b-spinner>
							<strong>Carregando...</strong>
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
			ref="novoJogoModal"
		></novo-jogo-modal>

		<confirm-modal
			v-on:deleteAposta="deleteAposta"
			ref="confirmModal"
		></confirm-modal>
	</div>
</template>

<script>
	import {api} from '../../config';
	import NovoJogoModal from './NovoJogoModal.vue';
	import ConfirmModal from './ConfirmModal.vue';
	import Paginate from 'vuejs-paginate';

	export default {
		components: {
			'novo-jogo-modal': NovoJogoModal,
			'confirm-modal': ConfirmModal,
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
				item: null,
				fields: [
					{ key: 'numero', label: 'Sorteio', class: 'text-center', isRowHeader: true },
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
				last_page: 1,

				isBusy: false
            }
        },

		created: function() {
			this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');

			this.getData();
		},

		methods: {
			getData(page) {
				this.isBusy = true;

				let filtro = this.filter == null || this.filter == '' ? 0 : this.filter;
				let url = '';

				url = api.apostas + '/' + filtro + '?page=' + page;

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
					this.isBusy = false;
				}).catch(error => {
					if (error.response.status == 401) {
						localStorage.removeItem('token');

						window.location.href = "/";
					}
					this.isBusy = false;
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
				this.getData(this.currentPage);
			},

			edit(item) {
				this.$refs.novoJogoModal.preencheCampos(item);
			},

			doDelete(item) {
				this.$refs.confirmModal.selectItem(item);
			},

			deleteAposta(item) {
				let url = api.aposta + '/' + item.id;
				
				axios.delete(url, this.header).then(response => {
					this.$toast.success("Aposta excluída com sucesso");
					this.getData(this.currentPage);
				});
			},

			zeraCamposModal() {
				this.$refs.novoJogoModal.zeraCampos();
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