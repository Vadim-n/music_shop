<template>
    <div id="addCategory">
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
            <div class="form-group">
                <label for="name">Наименование<sup class="text-danger">*</sup></label>
                <input v-validate="'required'" data-vv-as=" " type="text" class="form-control" id="name" name="name"
                       v-model="category.name">
            </div>
            <div class="form-check">
                <input v-model="category.is_active" class="form-check-input" type="checkbox" value="" id="is_active">
                <label class="form-check-label" for="is_active">
                    Активна
                </label>
            </div>
            <div class="form-group">
                <label for="alias">Алиас</label>
                <input v-model="category.alias" type="text" class="form-control"
                       placeholder="Генерировать автоматически" id="alias" name="alias">
            </div>
            <div class="form-group">
                <label for="alias">Родительская категория</label>
                <custom-select
                    v-model="category.parent"
                    :size="categories.length"
                    :clear-choice="true"
                    :options="categories">
                </custom-select>
            </div>
            <div class="form-group">
                <label for="description">Описание</label>
                <vue-editor v-model="category.description"
                            :editorToolbar="toolbarOptions"
                            id="description"
                            name="description"></vue-editor>
            </div>

            <div class="form-group">
                <label for="images">
                    <div type="button" class="btn btn-primary">&nbsp; <i class="fa fa-download pd-r-5"
                                                                         aria-hidden="true"></i> Загрузить изображение
                    </div>
                </label>
                <input class="form-control" type="file" style="height: 100%; display:none;" name="images"
                       id="images" accept="image/*" @change="upload"/>
            </div>
            <div class="form-group" v-if="category.image">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="border m-3 ">
                            <p style="position: relative; margin-left: 86%; color: white; font-size: small; background-color: #9c2631; cursor: pointer;"
                               class="badge text-md-right" @click="deleteImage"><i class="fa fa-times"
                                                                                   aria-hidden="true"></i></p>
                            <img :src="category.image.source" style="width:100%; margin-top: -37px;"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-row-reverse">
                <button class="btn btn-primary" @click="save">Сохранить</button>
                <a class="btn btn-light" href="/admin/categories">Отмена</a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            categoryId: {
                default: null
            }
        },
        created() {
            this.$http.get('/api/categories/list').then(response => {
                this.flattenOptions(response.data.categories, 0);
                if (this.categoryId) {
                    _.remove(this.categories, category => {
                        return category.value === this.categoryId;
                    });
                }
            });

            if (this.categoryId) {
                this.loading = true;
                this.$http.get('/api/category/' + this.categoryId).then(response => {
                    if (response.status === 200) {
                        this.category = response.data.category;
                        this.category.image = response.data.image;
                        this.category.parent = response.data.parent;
                        this.loading = false;
                    } else {
                        window.location = '/admin/categories';
                        this.loading = false;
                    }
                }).catch(response => {
                    window.location = '/admin/categories';
                    this.loading = false;
                });
            }
        },
        data() {
            return {
                category: {
                    name: "",
                    short_name: "",
                    description: "",
                    alias: "",
                    is_active: true,
                    image: null,
                    parent: null,
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

                        for (let key in this.category ) {
                            if (this.category[key] === null || this.category[key] === undefined) {
                                form_data.append(key, '');
                                continue;
                            }

                            if (key === 'image') {
                                form_data.append(key, this.category[key].file);
                                continue;
                            }
                            
                            if (key === 'parent' && typeof(this.category[key]) === 'object') {
                                form_data.append(key, this.category[key].value);
                                continue;
                            }

                            form_data.append(key, this.category[key]);
                        }

                        let savedPromise = this.categoryId ?
                            this.$http.post('/api/category/' + this.categoryId, form_data) :
                            this.$http.post('/api/category', form_data);
                        savedPromise.then((response) => {
                            window.location = '/admin/categories';
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

                let file = event.target.files[0];

                if (!file.type.match('image.*')) {
                    console.log('This is not in image. Try again.');
                    this.incorrect_file = true;
                    return;
                }

                this.category.image = {
                    file: file,
                    source: null,
                };

                if (file instanceof Blob) {
                    let reader = new FileReader();
                    reader.onload = (event) => {
                        this.category.image.source = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },
            deleteImage() {
                this.category.image = null;
            },
            flattenOptions(options, level) {
                _.forEach(options, option => {
                    this.categories.push({
                        value: option.value,
                        label: option.label,
                        level: level,
                    });

                    this.flattenOptions(option.children, level + 1);
                });
            },
        },
    }
</script>
