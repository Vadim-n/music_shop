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
            <div class="row">
                <div class="col-sm-10">
                    <div class="form-group">
                        <label for="name">Наименование<sup class="text-danger">*</sup></label>
                        <input v-validate="'required'" data-vv-as=" " type="text" class="form-control" id="name" name="name"
                               v-model="product.name">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="name">Цена</label>
                        <v-number
                            :precision="2"
                            class="form-control"
                            name="price"
                            id="price"
                            v-model="product.price"
                            separator="space"
                        ></v-number>
                    </div>
                </div>
            </div>
            <div class="form-check">
                <input v-model="product.is_active" class="form-check-input" type="checkbox" value="" id="is_active">
                <label class="form-check-label" for="is_active">
                    Активна
                </label>
            </div>
            <div class="form-group">
                <label for="alias">Категории</label>
                <custom-select
                    :multiple="true"
                    v-model="product.categories"
                    :options="categories">
                </custom-select>
            </div>
            <div class="form-group">
                <label for="alias">Алиас</label>
                <input v-model="product.alias" type="text" class="form-control"
                       placeholder="Генерировать автоматически" id="alias" name="alias">
            </div>
            <div class="form-group">
                <label for="shortName">Короткое наименование</label>
                <input type="text" class="form-control" id="shortName" name="shortName" v-model="product.short_name">
            </div>
            <div class="form-group">
                <label for="description">Описание</label>
                <vue-editor v-model="product.description"
                            :editorToolbar="toolbarOptions"
                            id="description"
                            name="description"></vue-editor>
            </div>
            <div class="form-group">
                <label for="images">
                    <div type="button" class="btn btn-primary">&nbsp; <i class="fa fa-download pd-r-5"
                                                                         aria-hidden="true"></i> Загрузить изображения
                    </div>
                </label>
                <input class="form-control" type="file" style="height: 100%; display:none;" name="images"
                       id="images" multiple accept="image/*" @change="upload"/>
            </div>
            <div class="form-group">
                <div class="row">
                    <div v-for="(image, index) in product.images" class="col-md-3">
                        <label class="btn btn-primary">
                            <input type="radio" 
                                   name="options" 
                                   id="option1" 
                                   autocomplete="off" 
                                   :value="index"
                                   v-model="product.main_image_index">
                            Главное изображение
                        </label>
                        <div class="border m-3 ">
                            <p style="position: relative; margin-left: 86%; color: white; font-size: small; background-color: #9c2631; cursor: pointer;"
                               class="badge text-md-right" @click="deleteImage(index)"><i class="fa fa-times"
                                                                                          aria-hidden="true"></i></p>
                            <img :src="image.source" style="width:100%; margin-top: -37px;"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-row-reverse">
                <button class="btn btn-primary" @click="save">Сохранить</button>
                <a class="btn btn-light" href="/admin/products">Отмена</a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            productId: {
                default: null
            }
        },
        created() {
            this.$http.get('/api/categories/list').then(response => {
                this.categories = response.data.categories;
            });

            if (this.productId) {
                this.loading = true;
                this.$http.get('/api/product/' + this.productId).then(response => {
                    if (response.status === 200) {
                        this.product = _.clone(response.data.product);

                        if (this.product.price === null || this.product.price === undefined) {
                            this.product.price = 0;
                        }

                        this.$set(this.product, 'images', []);
                        this.$set(this.product, 'main_image_index', 0);
                        this.product.images = [];
                        _.forEach(response.data.files, (file, index) => {
                            if (file.is_main === 1) {
                                this.product.main_image_index = index;
                            }
                            this.product.images.push({
                                file: file.document_id,
                                source: file.source,
                                is_main: file.is_main,
                            });
                        });

                        this.product.categories = [];
                        _.forEach(response.data.product.categories, category => {
                            this.product.categories.push({
                                value: category.id,
                                label: category.name + ' - #' + category.id,
                            });
                        });

                        this.loading = false;
                    } else {
                        window.location = '/admin/products/add';
                        this.loading = false;
                    }
                }).catch(response => {
                    window.location = '/admin/products/add';
                    this.loading = false;
                });
            }
        },
        data() {
            return {
                product: {
                    name: "",
                    price: 0,
                    short_name: "",
                    description: "",
                    alias: "",
                    is_active: true,
                    images: [],
                    sources: [],
                    main_image_index: 0,
                    categories: []
                },
                categories: [],
                saving: false,
                loading: false,
                toolbarOptions: [
                    [{font: []}],

                    [{header: [false, 1, 2, 3, 4, 5, 6]}],

                    [{size: ["small", false, "large", "huge"]}],

                    ["bold", "italic", "underline", "strike"],

                    [
                        {align: ""},
                        {align: "center"},
                        {align: "right"},
                        {align: "justify"}
                    ],

                    [{header: 1}, {header: 2}],

                    ["blockquote", "code-block"],

                    [{list: "ordered"}, {list: "bullet"}, {list: "check"}],

                    [{script: "sub"}, {script: "super"}],

                    [{indent: "-1"}, {indent: "+1"}],

                    [{color: []}, {background: []}],

                    ["link"],

                    [{direction: "rtl"}],
                    ["clean"]
                ],
            }
        },
        methods: {
            save() {
                this.saving = true;
                this.$validator.validateAll().then((result) => {
                    if (musicShop.validate(this) && result) {

                        let form_data = new FormData();

                        for (let key in this.product ) {
                            if (this.product[key] === null || this.product[key] === undefined) {
                                continue;
                            }

                            if (key === 'images') {
                                let old_images = [];

                                _.forEach(this.product[key], (val, index) => {
                                    if (val.file instanceof Blob) {
                                        form_data.append(index, val.file);
                                    } else {
                                        old_images.push(val.file);
                                    }
                                });

                                form_data.append('old_images', old_images);
                                continue;
                            }

                            if (key === 'categories') {
                                let categories = [];

                                _.forEach(this.product[key], (cat) => {
                                    categories.push(cat.value);
                                });

                                form_data.append('categories', categories);
                                continue;
                            }

                            form_data.append(key, this.product[key]);
                        }

                        let savedPromise = this.productId ?
                            this.$http.post('/api/product/' + this.productId, form_data) :
                            this.$http.post('/api/product', form_data);
                        savedPromise.then((response) => {
                            window.location = '/admin';
                        }).catch(response => {
                            this.saving = false;
                        });
                    } else {
                        this.saving = false;
                    }
                }).catch(() => {
                    this.saving = false;
                })
            },
            upload(event) {
                this.incorrect_file = false;
                _.forEach(event.target.files, (file, index) => {
                    if (!file.type.match('image.*')) {
                        console.log('This is not in image. Try again.');
                        this.incorrect_file = true;
                        return;
                    }

                    let fileObject = {
                        file: file,
                        source: null,
                    };

                    this.product.images.push(fileObject);
                });
                _.forEach(this.product.images, (file) =>{
                    if (file.file instanceof Blob) {
                        let reader = new FileReader();
                        reader.onload = (event) => {
                            file.source = event.target.result;
                        };
                        reader.readAsDataURL(file.file);
                    }
                });
            },
            deleteImage: function(index) {
                if (this.product.main_image_index === index) {
                    this.product.main_image_index = 0;
                }

                if (index < this.product.main_image_index) {
                    this.product.main_image_index--;
                }

                this.product.images.splice(index, 1);
            },
        },
    }
</script>
