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
  askConfirmation: (context, data) => {
    return new Promise((resolve, reject) => {
      context.commit('activateConfirm', {
        active: true,
        title: data.title,
        body: data.body,
        resolve,
        reject
      })
    })
  }
};

export default {
  state: state,
  mutations,
  actions
}
