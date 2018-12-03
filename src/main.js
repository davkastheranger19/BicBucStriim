/* eslint-disable no-console */
import Vue from 'vue'
import App from '@/App.vue'
import router from '@/router'
import store from '@/store/index'
import {AUTH_LOGOUT} from '@/store/actions/auth'
import { i18n } from '@/utils/i18n'

import {apollo} from '@/utils/apollo'
import VueApollo from "vue-apollo"
const apolloProvider = new VueApollo({
  defaultClient: apollo,
  errorHandler ({ graphQLErrors, networkError }) {
    if (graphQLErrors)
      graphQLErrors.map(({ message, locations, path }) =>
          // TODO better error handling
          console.log(
              `[GraphQL error]: Message: ${message}, Location: ${locations}, Path: ${path}`
          )
      );
    if (networkError) {
      if (networkError.statusCode === 401) {
        this.$store.dispatch(AUTH_LOGOUT, {}).then(() => {
          this.$router.push('/')
        })
      } else {
        // TODO better error handling
        console.log(`[Network error]: ${networkError.statusCode}, ${networkError}`);
      }
    }
  },
})
Vue.use(VueApollo)


Vue.config.productionTip = false

new Vue({
  router,
  store,
  i18n,
  apolloProvider,
  render: h => h(App),
}).$mount('#app')

