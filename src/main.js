import Vue from 'vue'
import VueI18n from 'vue-i18n'
import App from '@/App.vue'
import router from '@/router'
import store from '@/store/index'
import {AUTH_LOGOUT} from '@/store/actions/auth'

import en from '@/lang/en.json'
import de from '@/lang/de.json'

import {apollo} from '@/utils/apollo'
import VueApollo from "vue-apollo"
const apolloProvider = new VueApollo({
  defaultClient: apollo,
  errorHandler ({ graphQLErrors, networkError }) {
    if (graphQLErrors)
      graphQLErrors.map(({ message, locations, path }) =>
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
        console.log(`[Network error]: ${networkError.statusCode}, ${networkError}`);
      }
    }
  },
})
Vue.use(VueApollo)


Vue.config.productionTip = false
Vue.use(VueI18n)

export const i18n = new VueI18n({
  locale: 'de',
  fallbackLocale: 'en',
  messages: {
    de,
    en
  }
})

new Vue({
  router,
  store,
  i18n,
  apolloProvider,
  render: h => h(App),
}).$mount('#app')

