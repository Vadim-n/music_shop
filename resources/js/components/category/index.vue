<template>
    <div id="indexCategory">
        <div class="card-body">
            <div v-if="message" class="alert alert-success" role="alert">
                {{message}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <v-table id="categories_table"
                     :columns="columns"
                     data_src="/api/categories"
                     data_prop_name="categories"
                     :filters="filters"
                     :dicts="dicts"
                     :settings="settings"
                     @loaded="setData"
                     :reload="refresh"
                     @activate="switchActivated"
                     @deactivate="switchActivated"
                     @products="products"
                     @order="order"
            ></v-table>

            <div class="d-flex flex-row-reverse">
                <a class="btn btn-primary" href="/admin/categories/add"><i class="fa fa-plus" aria-hidden="true"></i> Добавить</a>
                <a class="btn btn-primary" href="/admin/categories/order"><i class="fa fa-sort" aria-hidden="true"></i> Упорядочить </a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "categoriesIndex",
        props: [
            'message',
        ],
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
                        href: "/admin/categories/{id}",
                        target: '_self',
                        format: 'text',
                        sortable: true,
                        searchable: true,
                        width: '10%',
                        sortDir: 'asc'
                    },
                    {
                        name: 'parent_name',
                        title: 'Родитель',
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
                        name: 'actions',
                        type: 'buttons', items: [
                            {
                                type: 'button',
                                class: 'btn-primary btn-sm',
                                id: 'products',
                                title: '',
                                icon: 'fa-product-hunt',
                                width: '1%',
                                tooltip: 'Товары',
                            },
                            {
                                type: 'button',
                                class: 'btn-primary btn-sm',
                                id: 'order',
                                title: '',
                                icon: 'fa-sort',
                                width: '1%',
                                tooltip: 'Сортировка',
                            },
                        ],
                        title: 'Действия',
                        align: 'right',
                        sortable: false,
                        searchable: false,
                        width: '1%',
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
                            }
                        ],
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
                    activate_callback: function (col, item) {},
                    products_callback: function (col, item) {},
                    order_callback: function (col, item) {},
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
                this.$http.put('/api/category/' + item.id + '/activate').then(function (response) {
                    this.refresh = true;
                });
            },
            products(item) {
                window.location.href = '/admin/categories/' + item.id + '/products';
            },
            order(item) {
                window.location.href = '/admin/categories/' + item.id + '/products/order';
            },
        }
    }
</script>
