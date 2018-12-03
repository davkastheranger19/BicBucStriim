import Vue from 'vue'
import VueI18n from 'vue-i18n'
import en from '@/lang/en.json'
import de from '@/lang/de.json'

Vue.use(VueI18n)
export const i18n = new VueI18n({
  locale: 'de',
  fallbackLocale: 'en',
  messages: {
    de,
    en
  }
})
