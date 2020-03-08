<template>
    <div class="col-md-4 grid-top" style="width: 350px; margin: 0 auto;">
        <a :href="'/' + reference" class="b-link-stripe b-animate-go thickbox">
            <img class="img-responsive" style="width: 320px; height: 320px;" :src="item.image" :alt="item.name" :title="item.name">
            <div class="b-wrapper">
                <h3 class="b-animate b-from-left b-delay03 ">
                    <span>{{ item.name }}</span>
                </h3>
            </div>
        </a>

        <p><a :href="'/' + reference">{{item.name}}</a></p>
    </div>
</template>

<script>
    export default {
        name: "itemBlock",
        props: {
            item: {
                type: Object,
                required: true,
            },
            reference: {
                type: String,
                required: true,
            }
        }
    }
</script>

<style scoped>

</style>
