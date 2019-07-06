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
                    common.loading.hide('body');
                })
                .fail(error => {
                    bootbox.alert('Error!');
                })
        },
        saveBranch() {
            if (($("#address").val().indexOf('Hồ Chí Minh') == -1 && $("#place").val() != 1) || ($("#address").val().indexOf('Hà Nội') == -1 && $("#place").val() != 2)) {
                bootbox.alert("Vui lọng chọn đúng khu vực");
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
             if (($("#address1").val().indexOf('Hồ Chí Minh') == -1 && $("#place_update").val() == 1) || ($("#address1").val().indexOf('Hà Nội') == -1 && $("#place_update").val() == 2)) {
                bootbox.alert("Vui lọng chọn đúng khu vực");
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