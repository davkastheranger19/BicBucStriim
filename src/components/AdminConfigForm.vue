<template>
  <b-form
    id="configForm"
    @submit="onSubmit"
    @reset="onReset"
    v-if="show"
  >
    <b-form-group
      id="calibreDirGroup"
      :description="$t('calibre_dir_expl')"
      :label="$t('calibre_dir1')"
      label-for="calibreDir"
    >
      <b-form-input
        id="calibreDir"
        type="text"
        v-model="configForm.calibreDir"
        required
        :placeholder="$t('calibre_dir2')"
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
    name: "AdminConfigForm",
    data () {
      return {
        configForm: {
          calibreDir: ''
        },
        show: true
      }
    },
    methods: {
      onSubmit (evt) {
        evt.preventDefault();
        alert(JSON.stringify(this.configForm));
      },
      onReset (evt) {
        evt.preventDefault();
        /* Reset our form values */
        this.form.calibreDir = '';
        /* Trick to reset/clear native browser form validation state */
        this.show = false;
        this.$nextTick(() => { this.show = true });
      }
    }
  }
</script>

<style scoped>

</style>