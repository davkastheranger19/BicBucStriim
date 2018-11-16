<template>
  <b-link to="/">
    <b-card
      :title="title"
      :img-src="imgUrl"
      img-alt="Image"
      img-top
      tag="article"
    >
      <p class="card-text">
        {{ `${author}  ${info}` }}
      </p>
    </b-card>
  </b-link>
</template>

<script>
    export default {
        name: "BookListItem",
        props: {
          bid: Number,
          title: String,
          author: String,
          info: String
        },
      computed: {
          imgUrl: function() {
            return `/api/titles/${this.bid}/thumbnail/`
          }
      }
    }
</script>

<style scoped>

</style>