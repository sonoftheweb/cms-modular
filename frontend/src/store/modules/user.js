import Cookies from 'js-cookie'
import router from '../../router/router'
import axios from 'axios'

const state = {
    token: Cookies.get('token') || null,
    user: {},
}

const mutations = {
    set_token(state, token) {
        state.token = token
    },
    set_user(state, user) {
        state.user = user
    },
    remove_user(state) {
        state.user = {}
    },
    remove_token(state) {
        state.token = null
    }
}

const actions = {
    logout(context, urlForRedirect = null) {
        axios.post('/api/v1/auth/logout').then(() => {
            context.dispatch('loggedOut').then(() => {
                router.push(urlForRedirect ? urlForRedirect : "/")
            })
        })
    },
    loggedOut(context) {
        // use is forced out. Cookie id removed and user data is cleared
        Cookies.remove('token', { path: '', domain: '.' + context.getters.domain })
        context.commit('remove_user')
        context.commit('remove_token')
    }
}

const getters = {
    authenticated: (state) => state.user !== {} && state.token,
}

export default {
    state: state,
    mutations,
    actions,
    getters
}