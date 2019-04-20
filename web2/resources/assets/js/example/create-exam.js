'use strict';
window.bus = new Vue();
Vue.component('examcreate-component', require('../components/CreateExamComponent.vue'));

const app = new Vue({
    el: '#exam-create',
});


