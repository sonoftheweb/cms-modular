import Cookies from 'js-cookie'
import axios from 'axios'
import router from "../../router/router";

const state = {
	token: Cookies.get('token') || '',
	authenticated: Cookies.get('token') !== undefined,
	dataset: false,
	subscribed: true
}

const mutations = {
	set_token(state, token) {
		state.token = token
	},
	set_authenticated(state, truthy) {
		state.authenticated = truthy
	},
	set_data_set(state, truthy) {
		state.dataset = truthy
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
		context.commit('set_token', token)
		context.commit('set_authenticated', true)
		context.commit('set_data_set', true)
	},
	async fetchUserData(context) {
		await axios.get('/api/me').then((response) => {
			context.commit('putSettingsInStore', { user: response.data })
			context.commit('putSettingsInStore', { instance: response.data.instance })
			context.commit('set_data_set', true)
			context.commit('updateState')
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
		context.commit('deleteUser')
		router.push('/')
	}
}

const getters = {
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