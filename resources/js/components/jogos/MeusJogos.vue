<template>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h3>Meus Jogos</h3>
				<p>Aqui estão seus jogos feitos. Tenha vários dados sobre seus jogos</p>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 right">
				<b-button size="sm" class="btn-success" @click="$bvModal.show('novoJogoModal')">
					<font-awesome-icon icon="fa-solid fa-plus" />
				</b-button>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<b-table responsive striped hover 
					:items="items"
					:filter="filter"
					:fields="fields"
				>
					<template #cell(actions)>
						<b-button size="sm" class="btn btn-sm btn-danger">
							<font-awesome-icon icon="fa-solid fa-trash" />
						</b-button>
					</template>
				</b-table>
			</div>
		</div>
		<novo-jogo-modal></novo-jogo-modal>
	</div>
</template>

<script>
	import {api} from '../../config';
	import NovoJogoModal from '../jogos/NovoJogoModal.vue';

	export default {
		components: {
			'novo-jogo-modal': NovoJogoModal
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
					{ key: 'data', label: 'Data Sorteio', class: 'text-center' },
					{ key: 'sorteado', label: 'Sorteio', class: 'text-center' },
					{ key: 'acertos', label: 'Acertos', class: 'text-center' },
					{ key: 'actions', label: 'Ações' }
				],
				filter: null
            }
        },

		created: function() {
			this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');


			axios.get(api.apostas + '/11', this.header).then(response => {
				let rows = [];
				response.data.data.map(function(aposta) {
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

				this.items = rows;
			}).catch(error => {
				if (error.response.status == 401) {
					localStorage.removeItem('token');

					window.location.href = "/";
				}
				console.log(error.data);
			});
		},

		methods: {
            showModal() {
				alert('ok');
			}
		}
	}
</script>

<style scoped>
.right {
	text-align: right;
}
</style>