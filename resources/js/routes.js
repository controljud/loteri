import Home from './components/home/Home.vue';
import Login from './components/login/Login.vue';
import Cadastro from './components/login/Cadastro.vue';

export default [
	{
		path: '/home',
		name: 'home',
		component: Home,
		meta: {}
	},
	{
		path: '/login',
		name: 'login',
		component: Login,
		meta: {}
	},
	{
		path: '/cadastro',
		name: 'cadastro',
		component: Cadastro,
		meta: {}
	}
];