/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


import Vue from 'vue';

window.Vue = require('vue');


import vSelect from 'vue-select';
import { VueEditor } from "vue2-editor";

Vue.use(require('vue-resource'));

require('./bootstrap');
require('./functions');
require('./validator');


Vue.http.interceptors.push((request, next) => {
    let token = document.head.querySelector('meta[name="csrf-token"]');
    request.headers.set('X-CSRF-TOKEN', token.content);

    next();
});

// Categories
Vue.component('category-add', require('./components/category/add.vue').default);
Vue.component('category-index', require('./components/category/index.vue').default);
Vue.component('category-order', require('./components/category/order.vue').default);

// Products
Vue.component('product-add', require('./components/product/add.vue').default);
Vue.component('product-index', require('./components/product/index.vue').default);
Vue.component('product-order', require('./components/product/order.vue').default);

// Common tools
Vue.component('v-table', require('./components/common/table.vue').default);
Vue.component( 'v-date-picker', require('./components/common/bootstrap_datetimepicker.vue').default);
Vue.component( 'v-select', vSelect);
Vue.component( 'custom-select', require('./components/common/customSelect.vue').default);
Vue.component('vue-editor', VueEditor);
Vue.component( 'v-number', require('./components/common/numeric.vue').default);

const app = new Vue({
    el: '#home',
    data: {
    },
});
