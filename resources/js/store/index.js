import {createStore, createLogger} from 'vuex'
import auth from './modules/auth.module'

const plugins = []

if (process.env.NODE_ENV === 'development') {
    plugins.push(createLogger())
}

export default createStore({
    plugins,
    state() {
        return {
            message: null,
            currentEditUser: null
        }
    },
    mutations: {
        setMessage(state, message) {
            state.message = message
        },
        async getEditUser(state, user) {
            state.currentEditUser = user
        },
        clearEditUser(state) {
            state.currentEditUser = null
        },
        clearMessage(state) {
            state.message = null
        },
        setStart(state, start) {
            state.start = start
            localStorage.setItem('role', role)
        }
    },
    actions: {
        getUser({commit}, id) {
            commit('getEditUser', id)
        },
        clearEditUser({commit}) {
            commit('clearEditUser')
        },
        setMessage({commit}, message) {
            commit('setMessage', message)
            setTimeout(() => {
                commit('clearMessage')
            }, 5000)
        },
        setStart({commit}, start) {
            commit('setStart', start)
        }
    },
    getters: {
        getEditUser(state) {
            return state.currentEditUser
        }
    },
    modules: {
        auth
    }
})
