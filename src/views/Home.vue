<template>
  <div class="home">
    <h1>{{ $t('home') }} </h1>
    <div v-if="isLibraryAvailable">
      <LibraryStatistics />
      <h2>{{ $t('dl30') }} </h2>
      <BookList :titles="titles" />
    </div>
    <div v-else>
      <b-alert show>{{ $t('mdb_error') }} <router-link to="Admin">{{ $t('admin') }}</router-link></b-alert>
    </div>
  </div>
</template>


<script>
  // @ is an alias to /src
  import LibraryStatistics from '@/components/LibraryStatistics.vue'
  import BookList from '@/components/BookList.vue'
  import gql from 'graphql-tag'
  import { mapGetters } from 'vuex'

  export default {
    name: 'Home',
    components: {
      LibraryStatistics,
      BookList
    },
    apollo: {
      titles: gql`{ titles(index: 0, lang: "de", length: 30, search: null) {id, sort, authorSort, addInfo, thumbnail} }`,
    },
    data() {
      return {
        // Initialize your apollo data
        titles: [],
      }
    },
    computed: {
      ...mapGetters(['isLibraryAvailable']),
    }
  }
</script>
