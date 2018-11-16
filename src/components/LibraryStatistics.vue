<template>
  <div class="LibraryStatistics">
    <h2>{{ $t('intro') }}</h2>
    <b-table
      striped
      :items="items"
      :fields="fields"
    />
  </div>
</template>

<script>
    const fields = {
        count: {
            label: ''
        },
        item: {
            label: ''
        }
    }
    const items = [
        {count: 0, item: "$t('titles')" },
        {count: 0, item: "$t('authors')" },
        {count: 0, item: "$t('tags')" },
        {count: 0, item: "$t('series')" }
    ]
    export default {
        name: "LibraryStatistics",
        data () {
            return {
                fields: fields,
                items: items
            }
        }
    }
</script>

<style scoped>
    .LibraryStatistics {
        display: inline-block;
        margin: 40px 0 0;
    }
</style>