'use strict';
import * as Pagination from 'laravel-vue-pagination';
const app = new Vue({

    el: '#manage-account',
    components: {Pagination},
    data() {
        return {
            results:{},
            name:'',
        };
    },

	created() {
        this.search();
    },
    methods: {
        search(page) {
            var data = {
                name: this.name,
            };
            if (page) {
                data.page = page;
            }
             
            $.get('search', data)
                .done(response => {
                    this.results = response.listSearch;
                })
                .fail(error => {
                    alert('Error!');
                })
        },
        deleteHealthRecord(id) {
            var data = {
                id:id
            }
            var r = confirm('bạn muốn xóa');
            if (r == true) {
                $.post('delete', data)
                .done(response => {
                    if (response.error == 0) {
                        alert("xóa thành công !!");
                    }
                })
            }
        },
        seeMoreDetail(id) {
            $("#edit").css('display','block');
            $("#id_user").val(id);
            $("#body").css('display','none');
        },
        exit() {
            $("#edit").css('display','none');
            $("#body").css('display','block');
        },
        edit() {
           var a = $("#imag1").val();
           var b = $("#id_user").val();
           console.log(b);
           var data = {
            img: a,
            id: b,
           }
            $.post('edit', data)
                .done(response => {
                    
            })
        },
	}
});
