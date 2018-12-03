import Vue from 'vue'
import Router from 'vue-router'
import '@/utils/bootstrap'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import Home from './views/Home.vue'
import Admin from './views/Admin.vue'
import Login from './views/Login.vue'
import Logout from './views/Logout.vue'
import Titles from './views/Titles.vue'
import Title from './views/Title.vue'
import Authors from './views/Authors.vue'
import Author from './views/Author.vue'
import Tags from './views/Tags.vue'
import Tag from './views/Tag.vue'
import Series from './views/Series.vue'
import SeriesDetails from './views/SeriesDetails.vue'
import store from './store/index'

Vue.use(Router)

import {library} from '@fortawesome/fontawesome-svg-core'
import {faCog, faSignOutAlt} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'

library.add(faCog)
library.add(faSignOutAlt)
Vue.component('font-awesome-icon', FontAwesomeIcon)

const ifNotAuthenticated = (to, from, next) => {
  if (!store.getters.isAuthenticated) {
    next()
    return
  }
  next('/')
}

const ifAuthenticated = (to, from, next) => {
  if (store.getters.isAuthenticated) {
    next()
    return
  }
  next('/login')
}
const ifAdmin = (to, from, next) => {
  if (store.getters.isAuthenticated && store.getters.isAdmin) {
    next()
    return
  }
  next('/login')
}

export default new Router({
  history,
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/admin',
      name: 'admin',
      component: Admin,
      beforeEnter: ifAdmin,
    },
    {
      path: 'titles/:id',
      name: 'title',
      component: Title,
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/titles',
      name: 'titles',
      component: Titles,
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/authors/:id',
      name: 'author',
      component: Author,
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/authors',
      name: 'authors',
      component: Authors,
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/tags/:id',
      name: 'tag',
      component: Tag,
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/tags',
      name: 'tags',
      component: Tags,
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/series/:id',
      name: 'seriesDetails',
      component: SeriesDetails,
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/series',
      name: 'series',
      component: Series,
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (about.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import(/* webpackChunkName: "about" */ './views/About.vue'),
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/login',
      name: 'Login',
      component: Login,
      beforeEnter: ifNotAuthenticated,
    },
    {
      path: '/logout',
      name: 'Logout',
      component: Logout,
      beforeEnter: ifAuthenticated,
    },
  ]
})
