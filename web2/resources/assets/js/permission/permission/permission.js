'use strict';
import * as Pagination from 'laravel-vue-pagination';
const app = new Vue({

    el: '#permission',
    components: {Pagination},
    data() {
        return {
            results:{},
            name:'',
            per:'',
        };
    },

    methods: {
        permission() {
            console.log(this.per);
        }
	}
});
