<template>
    <div id="productPage">
        <preloader v-if="loading"></preloader>
        <div v-else class="col-md-12 product-price1" style="margin-top: 15px;">
            <div class="col-md-5 single-top">
                <div style="margin-bottom: 20px; height: 380px; display: table-cell; vertical-align: middle;">
                    <img :src="currentDocument.image" class="gallery-current"/>
                    <a href="#" @click="prevImage" v-if="documents.length > 0" class="callbacks_nav callbacks1_nav prev">Previous</a>
                    <a href="#" @click="nextImage" v-if="documents.length > 0" class="callbacks_nav callbacks1_nav next">Next</a>
                </div>
                <div v-if="documents.length > 0" style="width: 100%; overflow: hidden;">
                    <div v-for="(document, index) in documents" class="col-sm-3" style="margin: 0; padding: 0;">
                        <img :src="document.image"
                             @click="selectImage(index)"
                             :class="index === currentDocumentIndex ? 'current-thumb' : ''"
                             class="gallery-thumb">
                    </div>
                </div>
            </div>
            <div class="col-md-7 single-top-in simpleCart_shelfItem">
                <div class="single-para ">
                    <h1>{{product.name}}</h1>

                    <h5 v-if="product.price" class="item_price">{{product.price}} руб.</h5>
                    <div v-html="product.description"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "productPage",
        props: {
            productAlias: {
                type: String,
                required: true,
            }
        },
        created() {
            this.$http.get('/api/client/product/' + this.productAlias).then(response => {
                this.product = response.data.product;
                this.documents = response.data.documents;

                this.currentDocument = _.find(this.documents, (document, index) => {
                    this.currentDocumentIndex = index;
                    return document.is_main;
                });

                this.loading = false;
            });
        },
        data() {
            return {
                loading: true,
                product: {},
                documents: [],
                currentDocument: {},
                currentDocumentIndex: 0,
            }
        },
        methods: {
            selectImage(index) {
                this.currentDocumentIndex = index;
                this.currentDocument = this.documents[index];
            },
            prevImage() {
                if (this.currentDocumentIndex === 0) {
                    this.currentDocumentIndex = this.documents.length - 1;
                } else {
                    this.currentDocumentIndex--;
                }
                this.currentDocument = this.documents[this.currentDocumentIndex];
            },
            nextImage() {
                if (this.currentDocumentIndex === this.documents.length - 1) {
                    this.currentDocumentIndex = 0;
                } else {
                    this.currentDocumentIndex++;
                }
                this.currentDocument = this.documents[this.currentDocumentIndex];
            },
        },
    }
</script>

<style scoped>
    .current-thumb {
        border: 1px solid #ef5f21;
    }

    .gallery-current {
        max-width: 100%;
        transition: 5s;
    }

    .gallery-thumb {
        height: 70px;
        margin: 0 10px 10px 0;
        cursor: pointer;
        transition: .3s;
    }

    .gallery-thumb:hover {
        opacity: .8;
        transition: .3s;
    }
    .callbacks_nav.prev{
        left: 20px !important;
        opacity: 0.9;
    }
    .callbacks_nav.next{
        right: 20px !important;
        opacity: 0.9;
    }
</style>
