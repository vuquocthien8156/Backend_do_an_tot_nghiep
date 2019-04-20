'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#authorization-user-web',
    components: { Pagination},
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            results: {},
            name_user: '',
            phone_user: '',
            email_user: '',
            password_user: '',

            user_id_update: '',
            name_user_update: '',
            phone_user_update: '',
            email_user_update: '',
            password_user_update: '',
            list_access_id : []
        };
    },
    created() { 
        this.listAuthorizationUserWeb();
    },
    mounted() {
       
    },
    methods: {
        listAuthorizationUserWeb(page) {
            var data = {
                _token: this.csrf,
            };
            if (page) {
                data.page = page;
            }
            common.loading.show('body');
            $.post('/config/authorization/list-authorization-user-web', data)
                .done(response => {
                    this.results = response;
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },
        formatDate(date) {
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return  date.getFullYear() + "-" + (date.getMonth()+1) + "-" +  date.getDate() + " " + strTime;
        },
        saveUserWeb() {
            if(this.name_user == null || this.name_user == '') {
                $('#name_user').focus();
                return false;
            } else if (this.email_user == null || this.email_user == '') {
                $('#email_user').focus();
                return false;
            } else if (this.password_user == null || this.password_user == '') {
                $('#password_user').focus();
                return false;
            }
            var arrayCheck = [];
            $('input[name="chk_permission_group[]"]:checked').each(function() {
                arrayCheck.push(this.value);
            });
            if (arrayCheck === undefined || arrayCheck.length == 0) {
                bootbox.alert("Tạo User cần ít nhất 1 quyền truy cập.");
                return false;
            }
            var data = {
                _token: this.csrf,
                name: this.name_user,
                phone: this.phone_user,
                email: this.email_user,
                password: this.password_user,
                permission_group: arrayCheck,
            };
            common.loading.show('body');
            $.post('/config/authorization/save', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/config/authorization';
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
        deleteUserWeb(user_id) {
            var data = {
                _token: this.csrf,
                user_id: user_id,
            };
            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có chắc chắn muốn xoá tài khoản này không?',
                buttons: {
                    confirm: {
                        label: 'Xác nhận',
                        className: 'btn-primary',
                    },
                    cancel: {
                        label: 'Bỏ qua',
                        className: 'btn-default'
                    }
                },
                callback: (result) => {
                    if (result) {
                        common.loading.show('body');
                        $.post('/config/authorization/delete', data)
                        .done(response => {
                            if (response.error === 0) {
                                common.loading.hide('body');
                                bootbox.alert("Xoá thành công !!", function() {
                                    window.location = '/config/authorization';
                                })
                            } else {
                                bootbox.alert('Error!!!');
                            }
                        }).fail(error => {
                            bootbox.alert('Error!!!');
                        }).always(() => {
                            common.loading.hide('body');
                        });
                    }
                }
            });
        },

        getInfo(id, name, phone, email, list_access_id) {
            this.user_id_update = id;
            this.name_user_update = name;
            this.email_user_update = email;
            this.phone_user_update = phone;
            $(".input_type_check").prop("checked", false);
            list_access_id.forEach(element => {
                $("#permission_update_"+element).prop( "checked", true);
            });
            $('#ModalUpdateUserAuthorization').modal('show');
        },

        updateUserWeb() {
            if(this.name_user_update == null || this.name_user_update == '') {
                $('#name_user').focus();
                return false;
            } else if (this.email_user_update == null || this.email_user_update == '') {
                $('#email_user').focus();
                return false;
            }
            var arrayCheck = [];
            $('input[name="chk_permission_group_update[]"]:checked').each(function() {
                arrayCheck.push(this.value);
            });
            if (arrayCheck === undefined || arrayCheck.length == 0) {
                bootbox.alert("Tạo User cần ít nhất 1 quyền truy cập.");
                return false;
            }
            var data = {
                _token: this.csrf,
                user_id: this.user_id_update,
                name: this.name_user_update,
                phone: this.phone_user_update,
                email: this.email_user_update,
                permission_group: arrayCheck,
            };
            common.loading.show('body');
            $.post('/config/authorization/update', data)
                .done(response => {

                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("sửa thành công !!", function() {
                            window.location = '/config/authorization';
                        })
                    } else {
                        bootbox.alert('Error!!!');
                    }
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        }
    }
});
