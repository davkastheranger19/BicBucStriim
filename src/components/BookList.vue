<template>
  <div class="BookList">
    <b-container
            fluid
      class="bv-example-row"
    >
      <b-row>
        <b-col
          sm="12"
          md="6"
          lg="4"
          xl="3"
          class="w-75 my-1 py-1"
          v-for="titleItem in titles"
        ><BookListItem
          :key="titleItem.id"
          :bid="titleItem.id"
          :title="titleItem.sort"
          :author="titleItem.authorSort"
          :info="titleItem.addInfo"
        /></b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
    import BookListItem from '@/components/BookListItem.vue'

    export default {
        name: "BookList",
        components: {
            BookListItem
        },
        props: {
            titles: {
                default: () => { return new Array(0)},
                type: Array
            }
        },
    }
</script>

<style scoped>

</style>