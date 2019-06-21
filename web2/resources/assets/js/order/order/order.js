'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#order',
    components: { Pagination},
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            code:'',
            results:{},
        };
    },
    created() { 
        this.search();
    },
    mounted() {
       
    },
    methods: {
        search(page) {
            common.loading.show('body');
            var data = {
                code:this.code
            }
            if (page) {
                 data.page = page;
            }
            $.get('search', data)
                .done(response => {
                    this.results = response.listSearch;
                    //this.exportproduct = response.infoExportExcel;
                    common.loading.hide('body');
                })
                .fail(error => {
                    bootbox.alert('Error!');
                    common.loading.hide('body');
                })
        },
        deleteOrder(id) {
            var data = {
                id:id
            }
            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có xoá sản phẩm này không?',
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
                    if (response.error == 0) {
                        bootbox.alert("xóa thành công !!", function() {
                             window.location.reload();
                        });
                        common.loading.hide('body');
                    }
                })
                    }
                }
            });
        
            
            
        },
        duyet(ma_don_hang, trang_thai, tong_tien, ma_khach_hang, phuong_thuc){
            console.log(ma_don_hang, trang_thai);
            var data = {
                id:ma_don_hang,
                status:trang_thai,
                tong_tien:tong_tien,
                ma_khach_hang:ma_khach_hang,
                phuong_thuc:phuong_thuc
            }
            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có duyệt đơn hàng này không?',
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
                $.post('accept', data)
                .done(response => {
                    if (response.error == 0) {
                        bootbox.alert("Duyệt thành công !!", function() {
                             window.location.reload();
                        });
                        common.loading.hide('body');
                    }
                })
                    }
                }
            });
        
            
            
        }
    }
});
