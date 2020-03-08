/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


import Vue from 'vue';

window.Vue = require('vue');

Vue.use(require('vue-resource'));

window._ = require('lodash');

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// require('./bootstrap');
require('./functions');
require('./validator');


Vue.http.interceptors.push((request, next) => {
    let token = document.head.querySelector('meta[name="csrf-token"]');
    request.headers.set('X-CSRF-TOKEN', token.content);

    next();
});

// Front
Vue.component('index-page', require('./components/front/index.vue').default);
Vue.component('category-page', require('./components/front/category.vue').default);
Vue.component('product-page', require('./components/front/product.vue').default);

// Common
Vue.component('item-block', require('./components/front/itemBlock.vue').default);
Vue.component('preloader', require('./components/front/preloader.vue').default);

const app = new Vue({
    el: '#home',
    data: {
    },
});
