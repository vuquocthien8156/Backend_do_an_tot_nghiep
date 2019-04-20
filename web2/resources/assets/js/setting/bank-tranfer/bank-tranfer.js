'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#bank-tranfer',
    components: { Pagination},
    data() {
        return { };
    },
    methods: {
        saveContentBankTranfer() {
            var data = {
                content_tranfer: $("#content_tranfer").val(),
            }
            common.loading.show('body');
            $.post('/config/bank-tranfer/update-content', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/config/bank-tranfer';
                        })
                    } else {
                        bootbox.alert('Error!!!');
                    }
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },
        
    }
});