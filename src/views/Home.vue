<template>
  <div class="home">
    <h1>{{ $t('home') }} </h1>
    <LibraryStatistics />
    <h2>{{ $t('dl30') }} </h2>
    <BookList :titles="titles" />
  </div>
</template>


<script>
  // @ is an alias to /src
  import LibraryStatistics from '@/components/LibraryStatistics.vue'
  import BookList from '@/components/BookList.vue'
  import gql from 'graphql-tag'

  export default {
    name: 'Home',
    components: {
      LibraryStatistics,
      BookList
    },
    apollo: {
      titles: gql`{ titles(index: 0, lang: "de", length: 30, search: null) {id, sort, authorSort, addInfo} }`,
    },
    data() {
      return {
        // Initialize your apollo data
        titles: [],
      }
    }

  }
</script>
