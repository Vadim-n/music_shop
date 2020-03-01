<template>
    <div id="indexProduct">
        <div class="card-body">
            <div v-if="message" class="alert alert-success" role="alert">
                {{message}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <v-table id="products_table"
                     :columns="columns"
                     :data_src="dataUrl"
                     data_prop_name="products"
                     :filters="filters"
                     :dicts="dicts"
                     :settings="settings"
                     @loaded="setData"
                     :reload="refresh"
                     @activate="switchActivated"
                     @deactivate="switchActivated"
            ></v-table>

            <div class="d-flex flex-row-reverse">
                <a class="btn btn-primary" href="/admin/products/add"><i class="fa fa-plus" aria-hidden="true"></i> Добавить</a>
                <a v-if="categoryId" class="btn btn-primary" :href="'/admin/categories/'+categoryId+'/products/order'"><i class="fa fa-sort" aria-hidden="true"></i> Упорядочить </a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "productsIndex",
        props: {
            message: {
                type: String,
                default: ''
            },
            dataUrl: {
                type: String,
                default: '/api/products'
            },
            categoryId: {
                default: null,
            },
        },
        data() {
            return {
                columns: [
                    {
                        name: 'id',
                        title: 'ID',
                        format: 'text',
                        align: 'right',
                        sortable: true,
                        searchable: true,
                        width: '2%',
                        sortDir: 'asc'
                    },
                    {
                        name: 'name',
                        title: 'Наименование',
                        href: "/admin/products/{id}",
                        target: '_self',
                        format: 'text',
                        sortable: true,
                        searchable: true,
                        width: '10%',
                        sortDir: 'asc'
                    },
                    {
                        name: 'short_name',
                        title: 'Короткое наименование',
                        format: 'text',
                        sortable: true,
                        searchable: true,
                        width: '7%',
                        sortDir: 'asc'
                    },
                    {
                        name: 'alias',
                        title: 'Алиас',
                        format: 'text',
                        sortable: true,
                        searchable: true,
                        width: '10%',
                        sortDir: 'asc'
                    },
                    {
                        name: 'creator_name',
                        title: 'Создатель',
                        format: 'text',
                        sortable: true,
                        width: '7%',
                        sortDir: 'asc',
                        searchable: true,
                    },
                    {
                        name: 'price',
                        title: 'Цена',
                        format: 'currency',
                        sortable: true,
                        width: '5%',
                        sortDir: 'asc',
                        searchable: true,
                    },
                    {
                        name: 'is_active',

                        type: 'buttons', items: [
                            {
                                type: 'button',
                                class: 'btn-danger btn-sm',
                                id: 'activate',
                                title: '',
                                icon: 'fa-times',
                                width: '1%',
                                show: 'is_active = 0'
                            },
                            {
                                type: 'button',
                                class: 'btn-success btn-sm',
                                id: 'activate',
                                title: '',
                                icon: 'fa-check',
                                width: '1%',
                                show: 'is_active = 1'
                            }],

                        title: 'Активна',
                        align: 'right',
                        sortable: true,
                        searchable: true,
                        width: '2%',
                        sortDir: 'asc',
                        search_dict: 'is_active',
                    },
                ],
                filters: {},
                dicts: {
                    is_active: [
                        {value: 1, label: 'Да'},
                        {value: 0, label: 'Нет'}
                    ],
                },
                settings: {
                    refresh_default: false,
                    global_search: false,
                    sort_on_init: {
                        col: 0,
                        dir: 'desc'
                    },
                    activate_callback: function (col, item) {}
                },
                refresh: false,
            }
        },
        watch: {
            refresh: _.debounce(function (newVal) {
                if (newVal) {
                    this.refresh = false;
                }
            }, 300)
        },
        methods: {
            setData: function () {

            },
            switchActivated: function (item) {
                this.$http.put('/api/product/' + item.id + '/activate').then(function (response) {
                    this.refresh = true;
                });
                return false;
            },
        }
    }
</script>
