<template>
  <b-link :to="{ name: 'title', params: { id: bid }}">
    <b-card
      :title="title"
      :img-src="thumbnail"
      img-alt="Image"
      img-top
      tag="article"
    >
      <p class="card-text">
        {{ `${author} ${info}` }}
      </p>
    </b-card>
  </b-link>
</template>

<script>
  export default {
    name: "BookListItem",
    props: {
      bid: {
        type: String,
        default: ''
      },
      title: {
        type: String,
        default: ''
      },
      author: {
        type: String,
        default: ''
      },
      info: {
        type: String,
        default: ''
      },
      thumbnail: {
        type: String,
        default: ''
      }
    },
  }
</script>

<style scoped>

</style>