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
            user_per:''
        };
    },

    methods: {
        permission() {
            var data ={
                id_per:this.per,
                id_user:this.user_per
            }
            $.ajax({
                    url: 'permission',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'post',
                    data: data,
                    success: function (result) {
                        if (result.error === 0) {
                            alert('Thành công!');
                            window.location = 'permission';
                        }
                    },
            });
        }
	}
});
