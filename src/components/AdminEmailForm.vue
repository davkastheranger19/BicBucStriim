<template>
  <b-form
    id="mailForm"
    @submit="onSubmit"
    @reset="onReset"
    v-if="show"
  >
    <b-form-group
      id="mailBooksGroup"
      :description="$t('kindle_expl')"
      :label="$t('kindle_enable')"
      label-for="mailBooks"
    >
      <b-form-checkbox-group
        :options="mailForm.mailBooksOptions"
        buttons
        button-variant="outline-primary"
        name="mailBooks"
        v-model="mailForm.mailBooks"
      />
    </b-form-group>
    <b-form-group
      id="mailFromGroup"
      :description="$t('kindle_expl2')"
      :label="$t('kindle_from_email1')"
      label-for="mailFrom"
    >
      <b-form-input
        id="mailFrom"
        type="email"
        v-model="mailForm.mailFrom"
        required
        :placeholder="$t('kindle_from_email1')"
      />
    </b-form-group>
    <b-form-group
      id="username"
      :description="$t('admin_smtpusername_expl')"
      :label="$t('admin_username')"
      label-for="usernameInput"
    >
      <b-form-input
        id="usernameInput"
        type="text"
        v-model="mailForm.username"
        autocomplete="username"
        :placeholder="$t('admin_username')"
      />
    </b-form-group>
    <b-form-group
      id="password"
      :description="$t('admin_smtpuserpw_expl')"
      :label="$t('admin_userpw')"
      label-for="passwordInput"
    >
      <b-form-input
        id="passwordInput"
        type="password"
        v-model="mailForm.password"
        autocomplete="current-password"
        :placeholder="$t('admin_userpw')"
      />
    </b-form-group>
    <b-form-group
      id="smtServerGroup"
      :description="$t('admin_smtpserver_expl')"
      :label="$t('admin_smtpserver')"
      label-for="mailFrom"
    >
      <b-form-input
        id="smtpServer"
        type="text"
        v-model="mailForm.smtpServer"
        :placeholder="$t('admin_smtpserver')"
      />
    </b-form-group>
    <b-form-group
      id="smtpPortGroup"
      :description="$t('admin_smtpport_expl')"
      :label="$t('admin_smtpport')"
      label-for="mailFrom"
    >
      <b-form-input
        id="smtpPort"
        type="number"
        v-model="mailForm.smtpPort"
        :placeholder="$t('admin_smtpport')"
      />
    </b-form-group>
    <b-form-group
      id="smtpEncGroup"
      :description="$t('admin_smtpenc_expl')"
      :label="$t('admin_smtpenc')"
      label-for="smtpEnc"
    >
      <b-form-checkbox-group
        :options="mailForm.smtpEncOptions"
        buttons
        button-variant="outline-primary"
        name="smtpEnc"
        v-model="mailForm.smtpEnc"
      />
    </b-form-group>
    <b-button
      type="submit"
      variant="primary"
    >{{ $t('save') }}</b-button>
    <b-button
      type="reset"
      variant="danger"
    >{{ $t('cancel') }}</b-button>
  </b-form>
</template>

<script>
  export default {
    name: "AdminEmailForm",
    data() {
      return {
        mailForm: {
          mailBooks: [false],
          mailBooksOptions: [
            {text: this.$i18n.t('switch_yes'), value: true},
            {text: this.$i18n.t('switch_no'), value: false},
          ],
          mailFrom: '',
          username: '',
          password: '',
          smtpServer: '',
          smtpPort: '',
          smtpEnc: [0],
          smtpEncOptions: [
            {text: this.$i18n.t('admin_smtpenc_none'), value: 0},
            {text: this.$i18n.t('admin_smtpenc_ssl'), value: 1},
            {text: this.$i18n.t('admin_smtpenc_tls'), value: 2},
          ],
        },
        show: true
      }
    },
    methods: {
      onSubmit (evt) {
        evt.preventDefault();
        alert(JSON.stringify(this.mailForm));
      },
      onReset (evt) {
        evt.preventDefault()
        /* Reset our form values */
        this.mailForm.mailBooks = false
        this.mailForm.mailFrom = ''
        this.mailForm.username = ''
        this.mailForm.password = ''
        this.mailForm.smtpServer = ''
        this.mailForm.smtpPort = ''
        this.mailForm.smtpEnc = ''
        /* Trick to reset/clear native browser form validation state */
        this.show = false;
        this.$nextTick(() => { this.show = true });
      }
    }
  }
</script>

<style scoped>

</style>