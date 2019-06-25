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
            detail:{},
            checkApprove:[],
            trang_thai: '',
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
                    common.loading.hide('body');
                })
                .fail(error => {
                    bootbox.alert('Error!');
                    common.loading.hide('body');
                })
        },
        showDetail(id) {
            common.loading.show('body');
            var data = {
                id:id,
            }
            $.get('detail', data)
                .done(response => {
                    this.detail = response.listDetail;
                    common.loading.hide('body');
                })
                .fail(error => {
                    bootbox.alert('Error!');
                    common.loading.hide('body');
                })
            $('#ModalShowDetail').modal('show');
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
        seeMoreDetail(thong_tin_giao_hang,ten_khuyen_mai,phi_ship,tong_tien2,ghi_chu,
            phuong_thuc_thanh_toan,ngay_lap,id) {
            var day = ngay_lap.split('-');
            var date = day[2]+'-'+day[1]+'-'+day[0];
            $('#TTGH').val(thong_tin_giao_hang);
            $('#KM').val(ten_khuyen_mai);
            $('#PS').val(phi_ship);
            $('#TT').val(tong_tien2);
            $('#GC').val(ghi_chu);
            $('#PTTT').val(phuong_thuc_thanh_toan);
            $('#NL').val(date);
            $('#id').val(id);
            $('#update').modal('show');
        },
        seeMoreStatus(trang_thai, ma) {
            this.trang_thai = trang_thai;
            $('#ma').text('Đơn hàng '+ma);
            $('#ModalStatus').modal('show');
        },
        editOrder() {
            common.loading.show('body');
            var data = {
                thong_tin_giao_hang:$('#TTGH').val(),
                ten_khuyen_mai:$('#KM').val(),
                phi_ship:$('#PS').val(),
                tong_tien:$('#TT').val(),
                ghi_chu:$('#GC').val(),
                phuong_thuc_thanh_toan:$('#PTTT').val(),
                ngay_lap:$('#NL').val(),
                id:$('#id').val(),
            };
            $.post('edit', data)
                 .done(response => {
                    if (response.error === 0) {
                        bootbox.alert("Sửa thành công !!", function() {
                             window.location.reload();
                        });
                        common.loading.hide('body');
                    } else {
                        bootbox.alert('Thất bại!!!');
                        common.loading.hide('body');
                    }
                })
        },
        duyet(){
            var data = {
                id:this.checkApprove,
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
                    }else {
                        bootbox.alert("Duyệt thất bại !!");
                        common.loading.hide('body');
                    }
                })
                    }
                }
            });
        }
    }
});
