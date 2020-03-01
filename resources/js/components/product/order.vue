<template>
    <div id="addProduct">
        <div v-show="saving">
            <div class="card-body" style=" text-align: center;padding: 100px 0;">
                Сохранение ...
            </div>
        </div>
        <div v-show="loading">
            <div class="card-body" style=" text-align: center;padding: 100px 0;">
                Загрузка ...
            </div>
        </div>
        <div class="card-body" v-show="!saving && !loading">

            <draggable
                class="list-group"
                tag="ul"
                v-model="products"
            >
                <transition-group type="transition" name="flip-list">
                    <li
                        class="list-group-item"
                        v-for="element in products"
                        :key="element.id"
                    >
                        <i
                            :class="
                element.fixed ? 'fa fa-anchor' : 'glyphicon glyphicon-pushpin'
              "
                            @click="element.fixed = !element.fixed"
                            aria-hidden="true"
                        ></i>
                        {{ element.name }}
                    </li>
                </transition-group>
            </draggable>

            <div class="d-flex flex-row-reverse">
                <button class="btn btn-primary" @click="save">Сохранить</button>
                <a class="btn btn-light" :href="'/admin/categories/'+categoryId+'/products'">Отмена</a>
            </div>
        </div>
    </div>
</template>

<script>
    import draggable from 'vuedraggable';

    export default {
        components: {
            draggable,
        },
        props: {
            categoryId: {
                type: Number,
            }
        },
        created() {
            this.urlPrefix = '/categories/' + this.categoryId + '/products';
            this.loading = true;
            this.$http.get('/api' + this.urlPrefix + '/order').then(response => {
                if (response.status === 200) {
                    this.products = response.data.products;
                    this.loading = false;
                } else {
                    window.location = '/admin' + this.urlPrefix;
                    this.loading = false;
                }
            }).catch(response => {
                window.location = '/admin' + this.urlPrefix;
                this.loading = false;
            });
        },
        data() {
            return {
                products: [],
                saving: false,
                loading: false,
                urlPrefix: '',
            }
        },
        methods: {
            save() {
                this.saving = true;
                this.$validator.validateAll().then((result) => {
                    if (musicShop.validate(this) && result) {
                        this.$http.put('/api' + this.urlPrefix + '/order', {products: this.products}).then((response) => {
                            window.location = '/admin' + this.urlPrefix;
                        }).catch(response => {
                            this.saving = false;
                        });
                    } else {
                        this.saving = false;
                    }
                }).catch(() => {
                    this.saving = false;
                })
            }
        },
    }
</script>
