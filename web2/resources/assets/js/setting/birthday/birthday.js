'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#birthday_config',
    components: { Pagination},
    data() {
        return { };
    },
    methods: {
        saveContentBirthday() {
            var data = {
                content_birthday: $("#content_notification").val(),
            }
            common.loading.show('body');
            $.post('/config/birthday/update-content', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/config/birthday';
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