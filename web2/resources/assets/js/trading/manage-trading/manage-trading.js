'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#manage-trade',
    components: {Pagination},
    data() {
        return {
            results_search: {},
            results_detail: {},
            result_infoExport: {},
            customer_name_phone: '',
            vehicle_number: '',
            id_feedback: this.id_feedback,
            feedback: '',
            from_date: '',
            employees: '',
            to_date: '',
            branch_id: '',
            checkDelete: []
        };
    },

    created() {
        this.searchTrading();
    },

    filters: {
        feedbackSubstr:function(string) {
            return string.substring(0,80)
        },
    },

    methods: {
        searchTrading(page) {
            var data = {
                customer_name_phone: this.customer_name_phone,
                vehicle_number: this.vehicle_number,
                from_date: this.from_date,
                employees: this.employees,
                to_date: this.to_date,
                name_store: this.branch_id,
            };
            if (page) {
                data.page = page;
            }
            common.loading.show('body');
            $.post('manage/search', data)
                .done(response => {
                    this.results_search = response.listSearch;
                    this.result_infoExport = response.exportVehicleList;    
                })
                .fail(error => {
                    bootbox.alert('Error!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },

        DeleteTrade() {
            var data = {
                idDelete: this.checkDelete,
            }
            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn muốn Xóa không?',
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
                        $.post('manage/deleteTrade', data)
                        .done(response => {
                            if (response.error === 0) {
                                common.loading.hide('body');
                                bootbox.alert("Xóa thành công !!", function() {
                                    window.location = '/product/manage';
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

        getInfoFeedback(feedback, id) {
            console.log(id);
            $('#content_feedback').val(feedback);
            $('#id_feedback').val(id);
            $('#ModalUpdateFeedback').modal('show');   
        },

        addFeedback(feedback, id) {
            $('#content_feedback').val(feedback);
            $('#id_feedback').val(id);    
            $('#ModalUpdateFeedback').modal('show');
        },

        submitForm : function(event) {
            event.preventDefault();
            var content_feedback = $('#content_feedback').val().trim();
            if(content_feedback == "" || content_feedback == null) {
                bootbox.alert('Hãy nhập nội dung phản hồi!');
                return false;
            }
            common.loading.show('body');
            $.ajax({
                url: $('#form_feedback').attr("action"),
                method: 'POST',
                data: $('#form_feedback').serialize(),
                success: function(response) {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !", function() {
                            window.location = window.location.pathname;
                        })
                    } else {
                        common.loading.hide('body');
                        bootbox.alert('Lỗi !!');
                    }
                },
                error: function() {
                    common.loading.hide('body');
                    bootbox.alert('Lỗi !!');
                }
            });
        },

        seeMoreDetail(id) {
            var data = {
                id_order_detail: id,
            }
            common.loading.show('body');
            $.post('manage/detail', data)
                .done(response => {
                    this.results_detail = response.listOrderDetail;    
                })
                .fail(error => {
                    bootbox.alert('Error!');
                }).always(() => {
                    common.loading.hide('body');
                }); 
            $('#ModalDetailTrade').modal('show');
        },

        deleteFeedback(id_feedback) {
            var data = {
                id_delete_feedback: id_feedback
            }
            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có chắc chắn muốn xoá phản hồi này không ?',
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
                        $.post('manage/delete/feedback', data)
                        .done(response => {
                            if (response.error === 0) {
                                common.loading.hide('body');
                                bootbox.alert("Xoá thành công !!", function() {
                                    window.location = '/trade/manage';
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
        }
    }
});
