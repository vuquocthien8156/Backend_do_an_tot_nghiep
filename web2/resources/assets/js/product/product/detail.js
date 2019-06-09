'use strict';
import * as Pagination from 'laravel-vue-pagination';
const app = new Vue({

    el: '#detail',
    components: {Pagination},
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            ten:'',
            ma:'',
            gia_goc:'',
            gia_size_vua:'',
            gia_size_lon:'',
            ngay_ra_mat:'',
            loaisp:'',
            mo_ta:'',
            selectedFile: null,
        };
    },

	created() {
       
    },
    methods: {
        search() {
            
        }
	}
});
