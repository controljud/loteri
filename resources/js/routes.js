import Home from './components/home/Home.vue';
import Apostas from './components/apostas/Apostas.vue';

import Admin from './components/admin/home/Admin.vue';
import Sorteios from './components/admin/sorteios/Sorteios.vue';

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
];