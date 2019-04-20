'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#manage-rescue',
    components: { Pagination},
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            results: {},
            latitude: '',
            longitude: '',
            name_user_rescue: '',
            phone_user_rescue: '',
            results_branch_staff: {branch_staff_rescue: [{name: "", phone: "", avatar_path: ""}], pathToResource: ""},
            info_staff_rescue: '',
            distance: '',
            price: '',
            note: '',
            id_rescue_request: '',
            branch_id_rescue: '',
            id_staff_rescue: '',
        };
    },
    created() { },
    mounted() {},
    methods: {
        getInfo(name_user, phone_user, id_rescue_request) {
            $("#name_user_rescue").text(name_user);
            $("#phone_user_rescue").text(phone_user);
            $("#name_phone_user_rescue").val(name_user + ' / ' + phone_user);
            this.name_user_rescue = name_user;
            this.phone_user_rescue = phone_user;
            this.id_rescue_request = id_rescue_request;
        },
        getInfoAssignedStaff(id, name_user, phone_user, distance, price, note, name_staff, phone_staff) {
            $("#name_phone_user_rescue_handling ").val(name_user + ' / ' + phone_user);
            $("#name_phone_staff_rescue_handling ").val(name_staff + ' / ' + phone_staff);
            $("#distance_rescue_handling ").val(distance);
            $("#price_rescue_handling ").val(price);
            $("#note_handling ").val(note);
            $("#id_handling").data('id_handling', id);
        },
        getInfoCompleted(id, name_user, phone_user, distance, price, note, name_staff, phone_staff) {
            $("#name_phone_user_rescue_completed").val(name_user + ' / ' + phone_user);
            $("#name_phone_staff_rescue_completed").val(name_staff + ' / ' + phone_staff);
            $("#distance_rescue_completed").val(distance);
            $("#price_rescue_completed").val(price);
            $("#note_completed").val(note);
        },
        getInfoDeleted(id, name_user, phone_user, distance, price, note, name_staff, phone_staff) { 
            $("#name_phone_user_rescue_deleted").val(name_user + ' / ' + phone_user);
            $("#name_phone_staff_rescue_deleted").val(name_staff + ' / ' + phone_staff);
            $("#distance_rescue_deleted").val(distance);
            $("#price_rescue_deleted").val(price);
            $("#note_deleted").val(note);
        },
        getStaffBranch(branch_id, id_staff_rescue, namestaff, phonestaff) {
            this.branch_id_rescue = branch_id;
            this.id_staff_rescue = id_staff_rescue; 
            this.info_staff_rescue = namestaff + ' / ' + phonestaff;
            var distance_fly_bird = $('#distance_fly_bird' + branch_id).text();
            this.distance = distance_fly_bird;
            $('#staff_rescue').val(this.info_staff_rescue);
            if (this.branch_id_rescue == null || this.branch_id_rescue == "" || this.id_staff_rescue == null || this.id_staff_rescue == "") {
                bootbox.alert("Hãy chọn nhân viên cứu hộ!");
                return false;
            }
            $('#ModalInfoPayment').modal('show');
            
            $('#ModalRescue').on('hidden.bs.modal', function (e) {
                $(".list-group-item-branch-rescue").removeClass("active");
                $(".card .collapse").removeClass("show");
            });
        },
        saveAssignStaffRescue() {
            var data = {
                _token: this.csrf,
                branch_id_rescue: this.branch_id_rescue,
                id_staff_rescue: this.id_staff_rescue,
                distance: this.distance,
                price: this.price,
                note: this.note,
                id_rescue_request: this.id_rescue_request,
            };
            common.loading.show('body');
            $.post('/rescue/save-assign-staff-rescue', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/rescue/manage';
                        })
                    } else {
                        common.loading.hide('body');
                        bootbox.alert('Error!!!');
                    }
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },
        completedRescue() {
            var id_rescue_request = $("#id_handling").data('id_handling');
            if (id_rescue_request == undefined || id_rescue_request == "" || id_rescue_request == null ) {
                return false;
            }
            var data = {
                _token: this.csrf,
                id_rescue_request: id_rescue_request,
            };
            common.loading.show('body');
            $.post('/rescue/complete-rescue', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/rescue/manage';
                        })
                    } else {
                        common.loading.hide('body');
                        bootbox.alert('Error!!!');
                    }
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },
        removeRescue(id_rescue_request) {
            var data = {
                _token: this.csrf,
                id_rescue_request: id_rescue_request,
            };

            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có chắc chắn muốn xoá TH này không?',
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
                        $.post('/rescue/delete-rescue', data)
                        .done(response => {
                            if (response.error === 0) {
                                common.loading.hide('body');
                                bootbox.alert("Xoá thành công !!", function() {
                                    window.location = '/rescue/manage';
                                })
                            } else {
                                common.loading.hide('body');
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
        dimissModal() {
            this.id_rescue_request = '';
            this.distance = '';
            this.price = '';
            this.note = '';
        }
    }
});