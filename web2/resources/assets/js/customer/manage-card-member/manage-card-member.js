'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#manage-card-member',
    components: { Pagination},
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            results: {},
            result_infoExport:{},
            username_phone_number_vehicle: '',
            manufacture: '',
            model: '',
            code: '',
            id_manufacture: '',
            status : '',
            manufacture_model_results: {},
            code_update: '',
            name_update: '',
            id_card_member_update: "",
        };
    },
    created() { 
        this.searchCardMember();
    },
    methods: {
        searchCardMember(page) {
            if (this.model === 'null') {
                this.model = null;
            }
            if (this.manufacture === 'null') {
                this.manufacture = null;
            }
            var data = {
                username_phone_number_vehicle: this.username_phone_number_vehicle,
                manufacture: this.id_manufacture,
                model: this.model,
                code: this.code,
                status: this.status,
            };
            if (page) {
                data.page = page;
            }
            common.loading.show('body');
            $.post('/customer/card-member/search', data)
                .done(response => {
                    this.results = response.listCard;
                    this.result_infoExport = response.listCardExport;
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },

        getModelManufacture() {
            if (this.id_manufacture === 'null') {
                this.id_manufacture = null;
            }
            var data = {
                id_manufacture: this.id_manufacture,
            };
            common.loading.show('body');
            $.post('/customer/manufacture/model', data)
                .done(response => {
                    this.manufacture_model_results = response;
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },
        
        deleteCardMember(id_card_member) {
            var data = {
                id_card_member: id_card_member,
            };
            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có chắc chắn muốn xoá thẻ thành viên này không?',
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
                        $.post('/customer/delete-card-member', data)
                        .done(response => {
                            if (response.error === 0) {
                                common.loading.hide('body');
                                bootbox.alert("Xoá thành công !!", function() {
                                    window.location = '/customer/card-member';
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

        saveCodeCardMember(id_card_member, id_user) {
            var code = $('#' + id_card_member).val();
            var name_card = $('#name_card_' + id_card_member).val();
            if (code == "" || code == null || name_card == "" || name_card == null) {
                bootbox.alert("Điền đầy đủ thông tin để duyệt!");
                return false;
            }
            var data = {
                id_card_member: id_card_member,
                code: code,
                name_card : name_card,
                id_user: id_user,
            };
            common.loading.show('body');
            $.post('/customer/save/code-card-member', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/customer/card-member';
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

        getInfo(id_card_member_update, code_card_member_update, name_card_member_update) {
            this.name_update = name_card_member_update;
            this.code_update = code_card_member_update;
            this.id_card_member_update = id_card_member_update;
        },

        updateNameCodeCardMember() {
            var data = {
                id_card_member: this.id_card_member_update,
                code_card_member: this.code_update,
                name_card_member: this.name_update,
            };
            common.loading.show('body');
            $.post('/customer/update/code-name-card-member', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/customer/card-member';
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

        syncDB411() {
            var data = {
                _token: this.csrf,
            };
            common.loading.show('body');
            $.post('/customer/sync-membership-card', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Đồng bộ hoàn tất!!", function() {
                            window.location = '/customer/card-member';
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

        seeMoreDetail(name_user, phone, name_card, vehicle_number, vehicle_manufacture_id, 
            vehicle_model_id, vehicle_color, bank_transfer_info, status, code, created_at, approved_at, expired_at, approved, vehicle_card_status) {
            $("#name").text(name_user);
            $("#phonenNumber").text(phone);
            $("#nameOnCard").text(name_card);
            $("#vehicleNumber").text(vehicle_number);
            $("#Manufacture").text(vehicle_manufacture_id);
            $("#Model").text(vehicle_model_id);
            $("#Color").text(vehicle_color);
            $("#bankTransferInfo").text(bank_transfer_info);
            if(status == -1) {
                $("#Status").text('Đã xoá');
            } else if (approved == true) {
                $("#Status").text('Đã kích hoạt');
            } else if (approved= false && vehicle_card_status == 1) {
                $("#Status").text('Đã đăng ký');
            } else {
                $("#Status").text('Chưa đăng ký');
            }
            $("#codeOfCard").text(code);
            $("#createdAt").text(created_at);
            $("#approvedAt").text(approved_at);
            $("#expiredAt").text(expired_at);
        },
    }
});