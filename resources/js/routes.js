import Home from './components/home/Home.vue';
import MeusJogos from './components/jogos/MeusJogos.vue';

export default [
	{
		path: '/home',
		name: 'home',
		component: Home,
		meta: {}
	},
	{
		path: '/meus-jogos',
		name: 'meus-jogos',
		component: MeusJogos,
		meta: {}
	}
];