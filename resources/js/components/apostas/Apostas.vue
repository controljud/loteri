<template>
	<div class="container">
		<div class="card card-content">
			<div class="row">
				<div class="col-md-12">
					<h3>Minhas Apostas</h3>
					<p>Aqui estão suas apostas realizadas. Tenha vários dados sobre todos os seus jogos preferidos</p>
				</div>
			</div>

			<div class="row">
				<div class="col-md-4">
					<b-form-select v-model="jogo" :options="jogos" size="sm" class="form-control" v-on:change="getData(1)"></b-form-select>
				</div>

				<div class="col-md-4">
					<b-form-group >
						<b-form-input type="text" autocomplete="off" placeholder="Busque seus jogos" v-model="filter" v-on:keyup="filtrar"></b-form-input>
					</b-form-group>
				</div>
				<div class="col-md-4 right">
					<b-button size="sm" class="btn-success" @click="edit(null, jogo); $bvModal.show('novaApostaModal'); zeraCamposModal()">
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
							<b-form-rating v-if="item.value == 0" id="rating-6" v-model="item.value" stars="6" disabled  style="max-width: 175px; margin: 0 auto;"></b-form-rating>
							<b-form-rating v-if="item.value > 0 && item.value <= 3" id="rating-6" v-model="item.value" stars="6" variant="warning" readonly style="max-width: 175px; margin: 0 auto;"></b-form-rating>
							<b-form-rating v-if="item.value > 3 && item.value <= 5" id="rating-6" v-model="item.value" stars="6" variant="primary" readonly style="max-width: 175px; margin: 0 auto;"></b-form-rating>
							<b-form-rating v-if="item.value > 5" id="rating-6" v-model="item.value" stars="6" variant="success" readonly style="max-width: 175px; margin: 0 auto;"></b-form-rating>
						</template>

						<template #cell(actions)="row">
							<b-button-group>
								<b-button size="sm" class="btn btn-sm btn-primary" @click="$bvModal.show('novaApostaModal'); edit(row.item)">
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

			<nova-aposta-modal
				v-on:atualizarTabela="atualizarTabela"
				ref="novaApostaModal"
			></nova-aposta-modal>

			<confirm-modal
				v-on:deleteAposta="deleteAposta"
				ref="confirmModal"
			></confirm-modal>
		</div>
	</div>
</template>

<script>
	import {api} from '../../config';
	import NovaApostaModal from './NovaApostaModal.vue';
	import ConfirmModal from './ConfirmModal.vue';
	import Paginate from 'vuejs-paginate';

	export default {
		components: {
			'nova-aposta-modal': NovaApostaModal,
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
					{ key: 'acertos', label: 'Acertos', class: 'text-center' },
					{ key: 'actions', label: 'Ações', class: 'text-center' }
				],
				filter: null,
				currentPage: 1,
				rows: 1,
				baseUrl: null,
				perPage: 10,
				last_page: 1,

				jogos: null,
				jogo: 1,

				isBusy: false
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

				let filtro = this.filter == null || this.filter == '' ? 0 : this.filter;
				let url = '';

				url = api.apostas + '/' + this.jogo + '/' + filtro + '?page=' + page;

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
					this.items = null;
					
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
				this.$refs.novaApostaModal.preencheCampos(item, this.jogo);
			},

			doDelete(item) {
				this.$refs.confirmModal.selectItem(item);
			},

			deleteAposta(item) {
				let url = api.aposta + '/' + item.id;
				
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

			zeraCamposModal() {
				this.$refs.novaApostaModal.zeraCampos();
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