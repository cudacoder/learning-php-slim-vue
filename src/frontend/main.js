import Vue from 'vue'
import axios from 'axios';
import VueRouter from 'vue-router'

import params from './params'
import router from './router'
import App from './App.vue'

Vue.use(VueRouter);
Vue.prototype.$http = axios;

new Vue({
    router,
    el: '#app',
    render: h => h(App, {props: {params}}),
});
