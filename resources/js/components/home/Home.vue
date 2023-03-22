<template>
	<div class="container">
		<div class="card card-content">
			<div class="row">
				<div class="col-md-12">
					<h3>Mapa de dezenas sorteadas</h3>
					<p>Acompanhe aqui a quantidade de vezes que cada dezena foi sorteada na Mega Sena</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="dv_sorteados" v-if="!carregando">
						<div v-for="(total, index) in totais" :key="index" class="pointSorteio" v-bind:style="{background: total[1]}">
							<span class="dezena"><span v-if="index < 10">0</span>{{index}}</span><br />
							<span class="total" style="font-weight: bold;">{{total[0]}}</span>
						</div>
					</div>
					<div class="dv_sorteados" v-if="carregando">
						<div class="text-center text-success my-2">
							<b-spinner class="align-middle"></b-spinner>
							<strong>Carregando...</strong>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<p><b>Último sorteio: </b>{{ultimo_sorteio}}<br />
					{{ultima_data}}</p>
				</div>
			</div>
			<!-- Inserir a tabela com os números sorteados -->
		</div>
	</div>
</template>

<script>
	import {api} from './../../config';

	export default {
		data() {
            return {
                header: {
					headers: {
						Authorization: null
					}
				},

				totais: null,
				coluna: 0,

				ultimo_sorteio: null,
				ultima_data: null,

				carregando: false
            }
        },

		mounted: function() {
			this.getTotais();
		},

		created: function() {
			this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');

			this.getTotais();
			this.getUltimoJogo();
		},

		methods: {
            getTotais() {
				this.carregando = true;
				axios.get(api.totais + '/1', this.header).then(response => {
					let retorno = response.data;

					if (retorno.status == 0) {
						let totais = retorno.data[0].totais;
						this.totais = JSON.parse(totais);
						
						this.getMinTotal();
					}

					this.carregando = false;
				}).catch(error => {
					if (error.response.status == 401) {
						localStorage.removeItem('token');

						window.location.href = "/";
					}

					this.carregando = true;
				});
			},

			getUltimoJogo() {
				axios.get(api.ultimo_jogo + '/1', this.header).then(response => {
					let data = response.data.data;

					this.ultimo_sorteio = JSON.parse(data.dezenas).join('-');
					this.ultima_data = data.data;
				}).catch(error => {
					if (error.response.status == 401) {
						localStorage.removeItem('token');

						window.location.href = "/";
					}
				});
			},

			getMinTotal() {
				let min = 1000;
				// for (let i = 0; i < this.totais.length; i++) {
					// if (total < min) {
					// 	min = total;
					// }
				// };

				// console.log(min);
				// console.log(this.totais);
			}
		}
	}
</script>

<style scoped>
.pointSorteio {
	float: left;
	border: 1px solid #cdcdcd;
	border-radius: 5px;
	width: 55px;
	height: 55px;
	margin: 3px;
	text-align: center;
	background: #cdcdcd;
	display: block;
}

.dezena {
	border: 1px solid #696969;
	border-radius: 50%;
	background: white;
	padding: 3px;
	margin-top: 3px;
	width: 25px;
	height: 14px;
}

.dv_sorteados {
	margin: 0 auto;
	max-width: 620px;
}

.card-content {
	margin-top: 10px;
	padding: 10px;
}
</style>