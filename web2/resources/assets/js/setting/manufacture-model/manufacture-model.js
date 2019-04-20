'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#setting-manufacture-model',
    components: { Pagination},
    data() {
        return {
            id_manufacture: '',
            manufacture_model_results: {},
        };
    },
    created() {
        this.getModelManufacture();
    },
    methods: {
        getModelManufacture() {
            if (this.id_manufacture === 'null') {
                this.id_manufacture = null;
            }
            var data = {
                id_manufacture: this.id_manufacture,
            };
            common.loading.show('body');
            $.post('/config/manufacture/model', data)
                .done(response => {
                    this.manufacture_model_results = response;
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },

        deleteManuFacture(id) {
            var data = {
                id_category:id
            };
            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn muốn xóa hãng xe này?',
                buttons: {
                    confirm: {
                        label: 'Xóa',
                        className: 'btn-primary',
                    },
                    cancel: {
                        label: 'Hủy bỏ',
                        className: 'btn-default'
                    }
                },
            callback: (result) => {
                if (result) {
                    common.loading.show('body');
                    $.post('/config/save-manufacture',data)
                        .done(response=> {
                            if (response.error === 0) {
                                common.loading.hide('body');
                                bootbox.alert("Xóa thành công!!", function() {
                                    window.location.reload();
                                })
                            } else {
                                bootbox.alert('error!!!');
                            }
                        }).fail(error=> {
                            bootbox.alert('Error!!');
                        }).always(()=> {
                            common.loading.hide('body');
                        });
                    }
                }
            });
        },

        deleteModel(id) {
            var data = {
                model:id,
            };
            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn muốn xóa dòng xe này?',
                buttons: {
                    confirm: {
                        label: 'Xóa',
                        className: 'btn-primary',
                    },
                    cancel: {
                        label: 'Hủy bỏ',
                        className: 'btn-default'
                    }
                },
            callback: (result) => {
                if (result) {
                    common.loading.show('body');
                    $.post('/config/save-model',data)
                        .done(response=> {
                            if (response.error === 0) {
                                common.loading.hide('body');
                                bootbox.alert("Xóa thành công!!", function() {
                                    window.location.reload();
                                })
                            } else {
                                bootbox.alert('error!!!');
                            }
                        }).fail(error=> {
                            bootbox.alert('Error!!');
                        }).always(()=> {
                            common.loading.hide('body');
                        });
                    }
                }
            });            
        },
    }
});