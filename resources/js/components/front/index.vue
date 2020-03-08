<template>
    <div id="indexPage">
        <preloader v-if="loading"></preloader>
        <div v-else class="content-top">
            <div v-for="category in categories">
                <item-block :item="category" :reference="category.alias"></item-block>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "indexPage",
        props: [],
        mounted() {
            this.$http.get('/api/client/categories').then(response => {
                this.categories = response.data.categories;
                this.loading = false;
            });
        },
        data() {
            return {
                loading: true,
                categories: [],
            }
        },
    }
</script>
