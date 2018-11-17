<template>
  <div class="titles">
    <h1>{{ $t('titles') }}</h1>
    <BookList :titles="titles" />
  </div>
</template>


<script>
  import BookList from '@/components/BookList.vue'
  import gql from 'graphql-tag'

  export default {
    name: 'Titles',
    components: {
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
    }

  }
</script>
