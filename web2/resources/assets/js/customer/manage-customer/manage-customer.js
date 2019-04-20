'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#manage-customer',
    components: { Pagination},
    data() {
        return {
            results: {},
            result_infoExport:{},
            from_date: '',
            to_date: '',
            username_phone: '',
            status: '',
            avatar_path: '',
            partner: '',
            result_partner: [],
            partner_field: '',
            partnerfield_edit:'',
            partner_edit: '',
            result_partner_edit: {},
            avatar_path_edit: '',
            imageUrl: null,
            selectedFile: null,

        };
    },

    created() {
        this.searchCustomer();
    },

    methods: {
        searchCustomer(page) {
            var partner_id = "";
            if(this.partner_field != "") {
                partner_id = this.partner_field
            } else {
                partner_id = this.partner
            }
            var data = {
                username_phone: this.username_phone,
                from_date: this.from_date,
                to_date: this.to_date,
                status: this.status,
                partner_field: partner_id,
            };
            if (page) {
                data.page = page;
            }
            common.loading.show('body');
            $.post('/customer/search', data)
                .done(response => {
                    this.results = response.listSearch;
                    this.result_infoExport=response.exportCustomerList;
                }).fail(error => {
                    bootbox.alert('Error!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },
        
        deleteCustomer(id) {
            var data = {
                id_customer: id,
            }
            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có chắc chắn muốn xoá khách hàng này không?',
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
                        $.post('/customer/delete', data)
                        .done(response => {
                            if (response.error === 0) {
                                common.loading.hide('body');
                                bootbox.alert("Xoá thành công !!", function() {
                                    window.location = '/customer/manage';
                                })
                            } else {
                                bootbox.alert('error!!!');
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
        getInfoUser(name, phone, email, avatar_path, id_user) {
            this.avatar_path = avatar_path;
            $('#name_employees').val(name);
            $('#phone_employees').val(phone);
            $('#email_employees').val(email);
            $('#avatar_path').val(avatar_path);
            $('#id_user').val(id_user);
            this.imageUrl = null;
            $('#ModalUpgradeUser').modal('show');
        },

        getEditCustomer(id,name, phone, address, birthday, partner, avatar_path) {
            this.avatar_path = avatar_path;
            $('#name_employees_edit').val(name);
            $('#phone_employees_edit').val(phone);
            $('#id_user_edit').val(id);
            $('#address_edit').val(address);
            $('#birthday_edit').val(birthday);
            $('#partnerfield_edit').val(partner);
            $('#partner_edit').val(partner);
            $('#avatar_path_edit').val(avatar_path);
            this.getPartnerEdit(partner);
            this.imageUrl = null;
            $('#ModalUpgradeCustomer').modal('show');
        },
        
        submitForm : function(event) {
            event.preventDefault();
            var name_employees = $('#name_employees').val();
            var phone_employees = $('#phone_employees').val();
            var email_employees = $('#email_employees').val();
            var type_employees = $('#type_employees').val();
            var branch = $('#branch').val();
            if(name_employees == null || name_employees == '') {
                $('#name_employees').focus();
                bootbox.alert("Vui lòng nhập tên!");
                return false;
            } else if (phone_employees == null || phone_employees == '') {
                $('#phone_employees').focus();
                bootbox.alert("Vui lòng nhập số điện thoại!");
                return false;
            } else if (email_employees == null || email_employees == '') {
                $('#email_employees').focus();
                bootbox.alert("Vui lòng nhập email!");
                return false;
            } else if (branch == null || branch == '') {
                $('#branch').focus();
                bootbox.alert("Hãy chọn chi nhánh!");
                return false;
            } else if (type_employees == null || type_employees == '') {
                $('#type_employees').focus();
                bootbox.alert("Hãy chọn loại nhân viên!");
                return false;
            }
            var data = new FormData();
            
            data.append('name_employees', name_employees);
            data.append('phone_employees', phone_employees);
            data.append('email_employees', email_employees);
            data.append('type_employees', type_employees);
            data.append('branch', branch);
            data.append('files', this.selectedFile);
            data.append('id_user', $('#id_user').val());

            let url = $('#form_upgrade_user').attr("action");
            let options = {
                    method: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                };
            common.loading.show('body');
            $('#ModalUpgradeUser').modal('hide');
            $.ajax(url, options).done(response => {
                if (response.error === 0) {
                    common.loading.hide('body');
                    bootbox.alert("Cập nhật thành công !!", function() {
                        window.location.reload();
                    })
                } else {
                    bootbox.alert('Error!!!');
                }
            })
            .always(() => {
                common.loading.hide('body');
            });
        },

        submitEditCustomer : function(event) {
            event.preventDefault();
            var name_employees_edit = $('#name_employees_edit').val();
            var phone_employees_edit = $('#phone_employees_edit').val();
            var address_edit = $('#address_edit').val();
            var birthday_edit = $('#birthday_edit').val();
            var partnerfield_edit = $('#partnerfield_edit').val();
            var partner_edit = $('#partner_edit').val();
            if(name_employees == null || name_employees == '') {
                $('#name_employees').focus();
                bootbox.alert("Vui lòng nhập tên!");
                return false;
            } else if (phone_employees == null || phone_employees == '') {
                $('#phone_employees').focus();
                bootbox.alert("Vui lòng nhập số điện thoại!");
                return false;
            } else if (address_edit == null || address_edit == '') {
                $('#address_edit').focus();
                bootbox.alert("Vui lòng nhập địa chỉ!");
                return false;
            } else if ((partnerfield_edit == null || partnerfield_edit == '') && (partner_edit == null || partner_edit == '')) {
                $('#partnerfield_edit').focus();
                bootbox.alert("Hãy chọn đối tác!");
                return false;
            } else if (birthday_edit == null || birthday_edit == '') {
                $('#birthday_edit').focus();
                bootbox.alert("Vui lòng nhập ngày sinh!");
                return false;
            }
            common.loading.show('body');
            var data = new FormData();
            
            data.append('name_employees_edit', name_employees_edit);
            data.append('phone_employees_edit', phone_employees_edit);
            data.append('address_edit', address_edit);
            data.append('birthday_edit', birthday_edit);
            
            if(partnerfield_edit != null && partnerfield_edit != '') {
                data.append('partnerfield_edit', partnerfield_edit);
            }
            if(partner_edit != null && partner_edit != '') {
                data.append('partner_edit', partner_edit);
            }
            data.append('avatar_path_edit', $('#avatar_path_edit').val());
            data.append('files_edit', this.selectedFile);
            data.append('id_user_edit', $('#id_user_edit').val());

            let url = $('#form_edit_password').attr("action");
            let options = {
                    method: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                };
            common.loading.show('body');
            $('#ModalUpgradeCustomer').modal('hide');
            $.ajax(url, options).done(response => {
                if (response.error === 0) {
                    common.loading.hide('body');
                    bootbox.alert("Cập nhật thành công !!", function() {
                        window.location.reload();
                    })
                } else {
                    bootbox.alert('Error!!!');
                    common.loading.hide('body');
                }
            })
            .always(() => {
                common.loading.hide('body');
            });
        },

        getPartner () {
            var data = {
                partnerField: this.partner,
            };
            common.loading.show('body');
            $.post('get-partner',data)
            .done(response => {
                if (response.error === 1) {
                    bootbox.alert('Error!!!');
                } else {
                    this.result_partner = response;
                    this.partner_field = "";
                }
            }).fail(error => {
                bootbox.alert('Error!!!');
            }).always(() => {
                common.loading.hide('body');
            });
        },

        getPartnerEdit (partner) {
            if(partner) {
                var data = {
                    partner: partner
                };
            } else {
                data = {
                    partnerField: $('#partnerfield_edit').val(),
                };
            }
            common.loading.show('body');
            $.post('get-partner',data)
            .done(response => {
                if (response.error === 1) {
                    bootbox.alert('Error!!!');
                } else {
                    this.result_partner_edit = response;
                }
            }).fail(error => {
                bootbox.alert('Error!!!');
            }).always(() => {
                common.loading.hide('body');
            });
        },

        onSelectImageHandler(e) {
            this.avatar_path = null;
            let files = e.target.files;
            let done = (url) => {
                this.$refs.fileInputEl.value = '';
                //this.$refs.bannerImgEl.src = url;
                this.imageUrl = url;
            };
            let reader;
            let file;
            let url;

            if (files && files.length > 0) {
                file = files[0];
                this.selectedFile = file;

                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = e => {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        },
    }
});