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
            sidebar: false
        }
    },
    mutations: {
        setStart(state, start) {
            state.start = start
            localStorage.setItem('role', role)
        }
    },
    actions: {
        setStart({commit}, start) {
            commit('setStart', start)
        }
    },
    modules: {
        auth
    }
})
