'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#manage-branch',
    components: { Pagination},
    data() {
        return {
            manufacture_model_results: {},
            latitude: '',
            longitude: '',
            name_branch: '',
            address: '',
            phone_branch: '',
            other_infomation: '',

            name_branch_update: '',
            address_update: '',
            phone_branch_update: '',
            other_infomation_update: '',
            id_branch_update: '',
        };
    },
    created() {
        
    },
    methods: {
        saveBranch() {
            var data = {
                latitude: $("#lat").val(),
                longitude: $("#long").val(),
                name_branch: this.name_branch,
                phone_branch: this.phone_branch,
                address: this.address,
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
        deleteBranch(id_branch) {
            var data = {
                id_branch: id_branch,
            }
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
        },
        getInfoBranch(name, phone, address, latitude, longitude, id) {
            this.name_branch_update = name;
            this.phone_branch_update = phone;
            this.address_update = address;
            $("#long_update").val(longitude);
            $("#lat_update").val(latitude);
            this.id_branch_update = id;
        },
        updateBranch() {
            var data = {
                latitude_update: $("#lat_update").val(),
                longitude_update: $("#long_update").val(),
                name_branch_update: this.name_branch_update,
                phone_branch_update: this.phone_branch_update,
                address_update: this.address_update,
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