import Home from './components/home/Home.vue';
import Apostas from './components/apostas/Apostas.vue';

import Admin from './components/admin/home/Admin.vue';
import Sorteios from './components/admin/sorteios/Sorteios.vue';
import Jogos from './components/admin/jogos/Jogos.vue';
import Usuarios from './components/admin/usuarios/Usuarios.vue';

export default [
	{
		path: '/home',
		name: 'home',
		component: Home,
		meta: {}
	},
	{
		path: '/apostas',
		name: 'apostas',
		component: Apostas,
		meta: {}
	},
	
	{
		path: '/admin',
		name: 'admin',
		component: Admin,
		meta: {}
	},
	{
		path: '/admin/sorteios',
		name: 'admin.sorteios',
		component: Sorteios,
		meta: {}
	},
	{
		path: '/admin/jogos',
		name: 'admin.jogos',
		component: Jogos,
		meta: {}
	},
	{
		path: '/admin/usuarios',
		name: 'admin.usuarios',
		component: Usuarios,
		meta: {}
	}
];