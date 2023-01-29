import Home from './components/home/Home.vue';
import Apostas from './components/apostas/Apostas.vue';

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
	}
];