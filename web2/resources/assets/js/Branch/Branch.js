'use strict';
import * as Pagination from 'laravel-vue-pagination';
const app = new Vue({

    el: '#manage-branch',
    components: {Pagination},
    data() {
        return {
           name: '',
           place: '',
           results: {},
        };
    },

	created() {
        this.search();
    },
    methods: {
        search(page) {
             var data = {
                name: this.name,
                place: this.place,
            };
            $.post('search', data)
                .done(response => {
                    console.log(response.listBranch);
                    this.results = response.listBranch;
                    common.loading.hide('body');
                })
                .fail(error => {
                    alert('Error!');
                })
        },
	}
});
