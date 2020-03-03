<template>
    <div id="categoryPage">
        <div class="content-top" v-if="category.children.length">
            <h1>{{category.name}}</h1>

            <div v-for="(child, index) in category.children">
                <div class="col-md-4 grid-top">
                    <a :href="'/' + child.alias" class="b-link-stripe b-animate-go thickbox">
                        <img class="img-responsive" :src="child.image" alt="">
                        <div class="b-wrapper">
                            <h3 class="b-animate b-from-left    b-delay03 ">
                                <span>{{ child.name }}</span>
                            </h3>
                        </div>
                    </a>

                    <p><a :href="'/' + child.alias">{{child.name}}</a></p>
                </div>

                <div v-if="index % 3 === 2 || index === category.children.length - 1" class="clearfix"></div>
            </div>
        </div>

        <div class="content-top" v-if="category.products.length">
            <h1>Товары категории "{{category.name}}"</h1>

            <div v-for="(product, index) in category.products">
                <div class="col-md-4 grid-top">
                    <a :href="'/' + category.alias + '/' + product.alias" class="b-link-stripe b-animate-go thickbox">
                        <img class="img-responsive" :src="product.image" alt="">
                        <div class="b-wrapper">
                            <h3 class="b-animate b-from-left    b-delay03 ">
                                <span>{{ product.name }}</span>
                            </h3>
                        </div>
                    </a>

                    <p><a :href="'/' + product.alias">{{product.name}}</a></p>
                </div>

                <div v-if="index % 3 === 2 || index === category.products.length - 1" class="clearfix"></div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "productPage",
        props: {
            categoryAlias: {
                type: String,
                required: true,
            }
        },
        created() {
            console.log(this.categoryAlias)
            this.$http.get('/api/client/category/' + this.categoryAlias).then(response => {
                this.category = response.data.category;
            });
        },
        data() {
            return {
                category: {},
            }
        },
    }
</script>
