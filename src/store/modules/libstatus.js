import Vue from 'vue'
import gql from "graphql-tag";
import {STATUS_REQUEST, STATUS_ERROR, STATUS_SUCCESS} from '../actions/libstatus'
import {AUTH_LOGOUT} from '../actions/auth'
import { apollo } from '@/utils/apollo'

const state = {status: '', libStatus: {userName: '', userId: "-1", userRole: 'NOUSER', libraryDefined: false, libraryVersion: ''}}

const getters = {
  getLibStatus: state => state.libStatus,
  isAdmin: state => state.libStatus.userRole === 'ADMIN',
  isLibraryAvailable: state => state.libStatus.libraryDefined,
  isLibStatusLoaded: state => !!state.libStatus.userName,
}

const actions = {
  [STATUS_REQUEST]: ({commit, dispatch}) => {
    commit(STATUS_REQUEST)
    apollo.query({
      query: gql`
                {
                    status {
                        userName userId userRole libraryDefined libraryVersion
                    }
                }
            `
    }).then(resp => {
      commit(STATUS_SUCCESS, resp)
    }).catch(resp => {
      commit(STATUS_ERROR)
      // if resp is unauthorized, logout, to
      dispatch(AUTH_LOGOUT)
    })
  },
}

const mutations = {
  [STATUS_REQUEST]: (state) => {
    state.status = 'loading'
  },
  [STATUS_SUCCESS]: (state, resp) => {
    state.status = 'success'
    Vue.set(state, 'libStatus', resp.data.status)
  },
  [STATUS_ERROR]: (state) => {
    state.status = 'error'
  },
  [AUTH_LOGOUT]: (state) => {
    state.libStatus = {userName: '', userId: "-1", userRole: 'NOUSER', libraryDefined: false, libraryVersion: ''}
    state.status = ''
  }
}

export default {
  state,
  getters,
  actions,
  mutations,
}
