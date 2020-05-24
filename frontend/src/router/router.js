import Vue from 'vue'
import VueRouter from 'vue-router'
import routes from './routes'
import store from '../store/index'

Vue.use(VueRouter)
const router = new VueRouter({
	routes: routes,
	mode: 'history'
})

router.initialize = async () => {
	router.beforeEach(async (to, from, next) => {
		if (to.name === 'logout' || from.name === 'logout') {
			next()
		}
		
		if ((to.name === 'login' || to.name === 'registration') && store.getters.authenticated) {
			return router.push('/home')
		}
		
		//console.log(store.getters.needsUserData)

		if (store.getters.authenticated && store.getters.needsUserData && to.name !== 'subscription') {
			await store.dispatch('fetchUserData')
			return next()
		}

		if (store.getters.authenticated && !store.getters.subscribed && to.name !== 'subscription') {
			return;
		}
		
		if (Object.prototype.hasOwnProperty.call(to.meta, 'requiresAuth') && to.meta.requiresAuth && !store.getters.authenticated) {
			return router.push('/')
		}

		/*if (store.getters.loggedIn) {
			if (to.name === 'logout') {
				return router.push('/home')
			}
		}

		if (to.meta.requiresAuth && !store.getters.isLoggedIn) {
			if (from.path === '/') {
				return
			}

			return router.push('/')
		}*/

		// Prevent access to role-protected routes for users that do not have the proper role
		/*if (to.meta.guard) { }*/

		return next();
	})
}

export default router