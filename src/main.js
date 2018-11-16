import Vue from 'vue'
import VueI18n from 'vue-i18n'
import App from './App.vue'
import router from './router'
import store from './store/index'
import en from '@/lang/en.json'
import de from '@/lang/de.json'

import ApolloClient from "apollo-client"
import { createHttpLink } from 'apollo-link-http';
import { setContext } from 'apollo-link-context';
import { InMemoryCache } from 'apollo-cache-inmemory';

const httpLink = createHttpLink({
    uri: 'api/graphql',
});

const authLink = setContext((_, { headers }) => {
    // get the authentication token from local storage if it exists
    const token = localStorage.getItem('token');
    // return the headers to the context so httpLink can read them
    return {
        headers: {
            ...headers,
            authorization: token ? `Bearer ${token}` : "",
        }
    }
});
import VueApollo from "vue-apollo"
const apolloProvider = new VueApollo({
    defaultClient: new ApolloClient({
        link: authLink.concat(httpLink),
        cache: new InMemoryCache(),
        connectToDevTools: true
    })
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

