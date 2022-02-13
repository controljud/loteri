<template>
	<div class="container">
        <h3>Esta é a escala de números sorteados na Mega Sena</h3>
		<!-- Inserir a tabela com os números sorteados -->
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

				totais: null
            }
        },

		mounted: function() {
			this.getTotais();
		},

		created: function() {
			// this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');
			
			// axios.get(api.mega_sorteados, this.header).then(response => {
			// 	console.log(response);
			// }).catch(error => {
			// 	if (error.response.status == 401) {
			// 		localStorage.removeItem('token');

			// 		window.location.href = "/";
			// 	}
			// });

			// this.getTotais();
		},

		methods: {
            getTotais() {
				this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');
				let url = api.totais + '/1';
				
				axios.get(api.totais + '/1', this.header).then(response => {
					let data = response.data.data[0];
					let totais = JSON.parse(data.totais);
					
					console.log(totais);
				}).catch(error => {

				});
			}
		}
	}
</script>