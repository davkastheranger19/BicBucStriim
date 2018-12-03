import { shallowMount } from '@vue/test-utils'
import AdminEmailForm from '../AdminEmailForm.vue'
import { i18n } from '@/utils/i18n'

// mock for i18n, see https://stackoverflow.com/questions/48790032/vuejs-unit-testing-with-vue-test-utils-gives-error-typeerror-vm-t-is-not
// const $t = () => {}

describe('AdminEmailForm.vue', () => {

  test('sanity test', () => {
    //const wrapper = mount(AdminEmailForm, { mocks: { $t }})
    const wrapper = shallowMount(AdminEmailForm, { i18n })
    expect(wrapper.text()).toContain('Speichern')
  })
})
