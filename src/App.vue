<template>
  <div id="app">
    <div id="nav">
      <b-navbar 
        toggleable="md" 
        type="dark"
        variant="dark"
      >
        <b-navbar-toggle target="nav_collapse" />

        <b-navbar-brand href="#">
          <!--img src="./assets/logo.png" class="d-inline-block align-top" alt="BV"-->
          BicBucStriim
        </b-navbar-brand>
        <b-collapse 
          is-nav 
          id="nav_collapse"
        >
          <b-navbar-nav>
            <b-nav-item to="/">{{ $t('home') }}</b-nav-item>
            <b-nav-item to="/titles">{{ $t('titles') }}</b-nav-item>
            <b-nav-item to="/authors">{{ $t('authors') }}</b-nav-item>
            <b-nav-item to="/tags">{{ $t('tags') }}</b-nav-item>
            <b-nav-item to="/series">{{ $t('series') }}</b-nav-item>
          </b-navbar-nav>
          <!-- Right aligned nav items -->
          <b-navbar-nav class="ml-auto">

            <b-nav-form>
              <b-form-input
                size="sm"
                class="mr-sm-2"
                type="text"
                :placeholder="$t('pagination_search_ph')"
              />
              <b-button 
                size="sm" 
                class="my-2 my-sm-0" 
                type="submit"
              >{{ $t('pagination_search_lbl') }}</b-button>
            </b-nav-form>

            <b-nav-item
              v-if="isAuthenticated"
              :title="$t('admin_short')"
              to="/admin"
            ><font-awesome-icon icon="cog" /></b-nav-item>
            <b-nav-item
              v-if="isAuthenticated"
              :title="$t('logout')"
              @click="logout"
              to="/"
            ><font-awesome-icon icon="sign-out-alt" /></b-nav-item>
          </b-navbar-nav>
        </b-collapse>
      </b-navbar>
    </div>
    <router-view />
  </div>
</template>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>

<script>
    import { mapGetters } from 'vuex'
    import { AUTH_LOGOUT } from '@/store/actions/auth'

    export default {
        name: 'App',
        methods: {
            logout: function () {
                this.$store.dispatch(AUTH_LOGOUT).then(() => this.$router.push('/login'))
            }
        },
        computed: {
            ...mapGetters(['isAuthenticated']),
        },
    }
</script>