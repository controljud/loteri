<template>
	<div>
        <span>Sorvete</span>
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
				}
            }
        },

		created: function() {
			this.header.headers.Authorization = 'Bearer ' + localStorage.getItem('token');
			
			axios.get(api.mega_sorteados, this.header).then(response => {
				console.log(response);
			}).catch(error => {
				if (error.response.status == 401) {
					localStorage.removeItem('token');

					window.location.href = "/";
				}
			});
		}
	}
</script>