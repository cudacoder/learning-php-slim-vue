import axios from 'axios';

import VueRouter from 'vue-router'

import Home from './routes/Home.vue'
import Tasks from './routes/Tasks.vue'
import Images from './routes/Images.vue'
import Deploy from './routes/Deploy.vue'
import Welcome from './routes/Welcome.vue'

export default new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'Home',
            component: Home,
            beforeEnter: function (to, from, next) {
                axios.get('/api/stat').then(function (res) {
                    if (!res.data.init) {
                        next('/welcome');
                    } else {
                        next();
                    }
                });
            }
        },
        {
            name: 'welcome',
            path: '/welcome',
            component: Welcome,
        },
        {
            name: 'images',
            path: '/images',
            component: Images,
        },
        {
            name: 'tasks',
            path: '/tasks',
            component: Tasks,
        },
        {
            name: 'deploy',
            path: '/deploy',
            component: Deploy,
        },
    ]
});
