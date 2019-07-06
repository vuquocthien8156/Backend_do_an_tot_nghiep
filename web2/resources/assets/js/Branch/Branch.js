'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#manage-branch',
    components: { Pagination},
    data() {
        return {
            results: {},
            latitude: '',
            longitude: '',
            name_branch: '',
            phone_branch: '',
            other_infomation: '',
            name_branch_update: '',
            listPlace: '',
            phone_branch_update: '',
            other_infomation_update: '',
            id_branch_update: '',
        };
    },
    created() {
        this.search();
    },
    methods: {
        search(page) {
            var data = {
                page:page
            };
            common.loading.show('body');
            $.get('search', data)
                .done(response => {
                    this.results = response.listBranch;
                    this.listPlace = response.listPlace;
                    common.loading.hide('body');
                })
                .fail(error => {
                    bootbox.alert('Error!');
                })
        },
        saveBranch() {
            var a = $("#place").val();
            var b = 'randombranch';
            for (var i = 0; i < this.listPlace.length; i++) {
                if (a == this.listPlace[i].ma_khu_vuc) {
                    b = this.listPlace[i].ten_khu_vuc;
                }
            }
            if (($("#address").val().indexOf(b) == -1)) {
                bootbox.alert("Vui lòng chọn đúng khu vực");
                return false;
            }
            if (this.name_branch == null && this.name_branch == '') {
                bootbox.alert("Vui lòng điền tên chi nhánh");
                return false;
            }
            if (this.phone_branch == null && this.phone_branch == '') {
                bootbox.alert("Vui lòng điền số điện thoại");
                return false;
            }
            if ($("#address").val() == null && $("#address").val() == '') {
                bootbox.alert("Vui lòng chọn địa chỉ");
                return false;
            }
            var data = {
                latitude: $("#lat").val(),
                longitude: $("#long").val(),
                name_branch: this.name_branch,
                phone_branch: this.phone_branch,
                id_kv:$("#place").val(),
                address: $("#address").val(),
            }
            common.loading.show('body');
            $.post('save', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/Branch/manage';
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
        deleteBranch(id_branch,status) {
            var data = {
                id_branch: id_branch,
                status: status
            }
            if (status == 1) {
                bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có muốn phục hồi chi nhánh này không?',
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
                        $.post('delete', data)
                        .done(response => {
                            if (response.error === 0) {
                                common.loading.hide('body');
                                bootbox.alert("Phục hồi thành công !!", function() {
                                    window.location = '/Branch/manage';
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
            }else {
                bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có chắc chắn muốn xoá chi nhánh này không?',
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
                        $.post('delete', data)
                        .done(response => {
                            if (response.error === 0) {
                                common.loading.hide('body');
                                bootbox.alert("Xoá thành công !!", function() {
                                    window.location = '/Branch/manage';
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
            } 
        },
        getInfoBranch(name, phone, address, latitude, longitude, id, id_kv) {
            this.name_branch_update = name;
            this.phone_branch_update = phone;
            $("#address1").val(address);
            $("#place_update").val(id_kv);
            $("#long_update").val(longitude);
            $("#lat_update").val(latitude);
            this.id_branch_update = id;
            $(".update-branch-form").removeClass('d-none');
            $(".update-branch-form").addClass('d-block');
            $('#collapseTableBranch').collapse('hide');
        },
        updateBranch() {
           var b = 'randombranch';
            for (var i = 0; i < this.listPlace.length; i++) {
                if (a == this.listPlace[i].ma_khu_vuc) {
                    b = this.listPlace[i].ten_khu_vuc;
                }
            }
            if (($("#address").val().indexOf(b) == -1)) {
                bootbox.alert("Vui lòng chọn đúng khu vực");
                return false;
            }
            if (this.name_branch == null && this.name_branch == '') {
                bootbox.alert("Vui lòng điền tên chi nhánh");
                return false;
            }
            if (this.phone_branch == null && this.phone_branch == '') {
                bootbox.alert("Vui lòng điền số điện thoại");
                return false;
            }
            if ($("#address").val() == null && $("#address").val() == '') {
                bootbox.alert("Vui lòng chọn địa chỉ");
                return false;
            }
            var data = {
                latitude_update: $("#lat_update").val(),
                longitude_update: $("#long_update").val(),
                name_branch_update: this.name_branch_update,
                id_kv: $("#place_update").val(),
                phone_branch_update: this.phone_branch_update,
                address_update: $("#address1").val(),
                id_branch_update: this.id_branch_update,
            }
            common.loading.show('body');
            $.post('update', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/Branch/manage';
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