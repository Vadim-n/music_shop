<template>
    <div id="categoryPage">
        <preloader v-if="loading"></preloader>
        <div v-else>
            <div class="content-top" v-if="category.children && category.children.length">
                <h1>{{category.name}}</h1>

                <div v-for="(child, index) in category.children">
                    <item-block :item="child" :reference="child.alias"></item-block>
                </div>
            </div>

            <div class="content-top" v-if="category.products && category.products.length">
                <h1>Товары категории "{{category.name}}"</h1>

                <div class="bottom-product">
                    <div v-for="(product, index) in category.products">
                        <item-block :item="product" :reference="category.alias + '/' + product.alias"></item-block>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "categoryPage",
        props: {
            categoryAlias: {
                type: String,
                required: true,
            }
        },
        created() {
            this.$http.get('/api/client/category/' + this.categoryAlias).then(response => {
                this.category = response.data.category;
                this.loading = false;
            });
        },
        data() {
            return {
                loading: true,
                category: {},
            }
        },
    }
</script>
