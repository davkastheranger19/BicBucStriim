<template>
  <div class="login">
    <b-form
      @submit="onSubmit"
      v-if="show"
    >
      <h1>{{ $t('login') }}</h1>
      <b-form-group
        id="username"
        label-for="usernameInput"
      >
        <b-form-input
          id="usernameInput"
          type="text"
          v-model="form.username"
          autocomplete="username"
          required
          :placeholder="$t('admin_username')"
        />
      </b-form-group>
      <b-form-group
        id="password"
        label-for="passwordInput"
      >
        <b-form-input
          id="passwordInput"
          type="password"
          v-model="form.password"
          autocomplete="current-password"
          required
          :placeholder="$t('admin_userpw')"
        />
      </b-form-group>
      <b-button
        type="submit"
        variant="primary"
      >{{ $t('login') }}
      </b-button>
    </b-form>
  </div>
</template>

<style>
  .Login {
    width: 400px;
    display: inline-block;
  }
</style>

<script>
  import {AUTH_REQUEST} from '../store/actions/auth'

  export default {
    name: 'Login',
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
      onSubmit: function (evt) {
        evt.preventDefault();
        const {username, password} = this.form
        this.$store.dispatch(AUTH_REQUEST, {username, password}).then(() => {
          this.$router.push('/')
        })
      }
    },
  }
</script>
