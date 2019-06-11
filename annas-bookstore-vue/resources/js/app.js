require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router';
Vue.use(VueRouter);

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);

import routes from './routes';

const router = new VueRouter({
    routes
});

const app = new Vue({
    router,
    el: '#app'
});
