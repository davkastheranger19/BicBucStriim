import Vue from 'vue'
import {TITLES_DL30, TITLES_DL30_SUCCESS, TITLES_ERROR} from "../actions/titles";
import gql from "graphql-tag";

const state = { status: '', titles_dl30: {} }

const getters = {
    getDl30: state => state.titles_dl30,
}

const actions = {
    [TITLES_DL30]: ({commit, dispatch}) => {
        commit(TITLES_DL30)
        this.$apollo.provider.defaultClient.query({
            query: gql`
                {
                    titles(index: 0, lang: "de", length: 30, search: null) {
                        authorSort
                    }
                }
            `
        }).then(resp => {
            commit(TITLES_DL30_SUCCESS, resp)
        }).catch(resp => {
            commit(TITLES_ERROR, resp)
        })
    },
}

const mutations = {
    [TITLES_DL30]: (state) => {
        state.status = 'loading'
    },
    [TITLES_DL30_SUCCESS]: (state, resp) => {
        state.status = 'success'
        Vue.set(state, 'titles_dl30', resp)
    },
    [TITLES_ERROR]: (state) => {
        state.status = 'error'
    }
}

export default {
    state,
    getters,
    actions,
    mutations,
}
