<template>
  <div class="logout">
    <h1>{{ $t('logout') }}</h1>
  </div>
</template>

<style>
  .Login {
    width: 400px;
    display: inline-block;
  }
</style>

<script>
    import {AUTH_LOGOUT} from '../store/actions/auth'

    export default {
        name: 'Logout',
        data() {
            return {
                form: {
                    username: '',
                    password: '',
                },
                show: true
            }
        },
        methods: {
            onEnter: function (evt) {
                evt.preventDefault();
                this.$store.dispatch(AUTH_LOGOUT, {}).then(() => {
                    this.$router.push('/')
                })
            }
        },
    }
</script>
