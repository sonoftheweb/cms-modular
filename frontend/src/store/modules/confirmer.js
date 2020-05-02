const initialState = {
    active: false,
    title: '',
    body: '',
    resolve: null,
    reject: null,
};

const state = Object.assign({}, initialState);

const mutations = {
    activateConfirm: (state, payload) => {
        Object.assign(state, payload)
    },
    deactivateConfirm: (state) => {
        Object.assign(state, initialState)
    }
};

const actions = {
    askConfirmation: ({ commit }, { title, body }) => {
        return new Promise((resolve, reject) => {
            commit('activateConfirm', {
                active: true,
                title,
                body,
                resolve,
                reject
            })
        })
    }
};

export default {
    state,
    mutations,
    actions
}
