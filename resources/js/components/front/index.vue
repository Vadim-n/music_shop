<template>
    <div id="indexPage">
        <div class="content-top">
            <div v-for="(category, index) in categories">
                <div class="col-md-4 grid-top">
                    <a :href="'/' + category.alias" class="b-link-stripe b-animate-go thickbox">
                        <img class="img-responsive" :src="category.image" alt="">
                        <div class="b-wrapper">
                            <h3 class="b-animate b-from-left    b-delay03 ">
                                <span>{{ category.name }}</span>
                            </h3>
                        </div>
                    </a>

                    <p><a :href="'/' + category.alias">{{category.name}}</a></p>
                </div>

                <div v-if="index % 3 === 2 || index === categories.length - 1" class="clearfix"></div>
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
            });
        },
        data() {
            return {
                categories: [],
            }
        },
    }
</script>
