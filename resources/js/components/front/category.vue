<template>
    <div id="categoryPage">
        <div class="content-top" v-if="category.children && category.children.length">
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

        <div class="content-top" v-if="category.products && category.products.length">
            <h1>Товары категории "{{category.name}}"</h1>

            <div class="bottom-product">
                <div v-for="(product, index) in category.products">
                    <div class="col-md-4 bottom-cd simpleCart_shelfItem">
                        <div class="product-at ">
                            <a :href="'/' + product.alias"><img class="img-responsive" :src="product.image"
                                                                :alt="product.name">
                                <div class="pro-grid">
                                    <span class="buy-in">Подробнее</span>
                                </div>
                            </a>
                        </div>
                        <p class="tun">{{product.name}}</p>
                        <div class="item_add" v-if="product.price">
                            <p class="number item_price">{{product.price}} руб.</p>
                        </div>
                    </div>
                    <div v-if="index % 3 === 2 || index === category.products.length - 1" class="clearfix"></div>
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
            });
        },
        data() {
            return {
                category: {},
            }
        },
    }
</script>
