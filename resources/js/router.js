import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import routes from './routes';

const router = new VueRouter({
	mode: 'history',
	routes
});

router.beforeEach(async (to, from, next) => {
	const publicPages = ['/'];
	const authRequired = !publicPages.includes(to.path);
	const loggedIn = localStorage.getItem('user');

	if (authRequired && !loggedIn) {
		return next('/');
	}

	next();
});

export default router;
