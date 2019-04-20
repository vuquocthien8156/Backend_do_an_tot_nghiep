'use strict';
window.bus = new Vue();
Vue.component('example-component', require('../components/ExampleComponent.vue'));
Vue.component('train-component', require('../components/NewComponent.vue'));

const app = new Vue({
    el: '#app',
});
const train = new Vue({
    el: '#train'
});


