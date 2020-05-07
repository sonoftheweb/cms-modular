import Cookies from 'js-cookie'
import axios from 'axios'

const state = {
	token: Cookies.get('token') || '',
	authenticated: Cookies.get('token') !== undefined,
	subscribed: true,
	user: {},
}

const mutations = {
	set_token(state, token) {
		state.token = token
	},
	set_authenticated(state, truthy) {
		state.authenticated = truthy
	},
	set_user(state, user) {
		state.user = user
	},
	remove_user(state) {
		state.user = {}
	},
	remove_token(state) {
		state.token = null
	},
	subscribed(state, truthy) {
		state.subscribed = truthy
	}
}

const actions = {
	loggedIn(context, token) {
		Cookies.set('token', token, {
			expires: context.getters.sessionLifetime,
			//domain: '.' + context.getters.domain
		})
		context.commit('set_authenticated', true)
	},
	async fetchUserData(context) {
		await axios.get('/api/me').then((response) => {
			context.commit('set_user', response.data)
		})
	},
	async logout(context) {
		if (context.getters.authenticated) {
			try {
				await axios.post('/api/auth/logout').then(() => {
					context.dispatch('loggedOut')
				});
			} catch(e) {
				await context.dispatch('loggedOut')
			}
		}
	},
	loggedOut(context) {
		// use is forced out. Cookie id removed and user data is cleared
		Cookies.remove('token')
		context.commit('remove_user')
		context.commit('remove_token')
		context.commit('set_authenticated', false)
	}
}

const getters = {
	authenticated: state => state.authenticated,
	isLoggedIn: state => state.authenticated && Object.keys(state.user).length !== 0,
	user: state => state.user,
	needsUserData: state => Object.keys(state.user).length === 0,
	subscribed: state => state.subscribed,

	sessionLifetime: () => 10,
	domain: () => {
		return window.location.hostname;
	},
}

export default {
	state: state,
	mutations,
	actions,
	getters
}