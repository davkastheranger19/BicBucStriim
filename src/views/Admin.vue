<template>
  <div class="admin">
    <h1>{{ $t('admin_short') }} </h1>
    <b-tabs>
      <b-tab
        :title="$t('admin')"
        active
      >
        <AdminConfigForm />
      </b-tab>
      <b-tab :title="$t('admin_users')" />
      <b-tab :title="$t('admin_idtemplates')" />
      <b-tab :title="$t('admin_mail')">
        <AdminEmailForm />
      </b-tab>
      <b-tab :title="$t('admin_check_version')" />
    </b-tabs>
  </div>
</template>

<script>
  import AdminConfigForm from '../components/AdminConfigForm'
  import AdminEmailForm from "../components/AdminEmailForm";
  export default {
    name: "Admin",
    components: {AdminEmailForm, AdminConfigForm},

  }
</script>

<style scoped>

</style>