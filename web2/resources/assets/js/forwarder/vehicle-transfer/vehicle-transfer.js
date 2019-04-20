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
            name_user_forwarder: '',
            phone_user_forwarder: '',
            info_staff_assign: '',

            distance: '',
            price: '',
            note: '',
            id_vehicle_transfer: '',
            branch_id: '',
            id_staff_assign: '',
        };
    },
    created() { },
    mounted() {
        this.number_not_process_yet = $('#number_not_process_yet').val();
        this.number_completed = $('#number_completed').val();
        this.number_handling = $('#number_handling').val();
    },
    methods: {
        getInfo(name_user, phone_user, id_vehicle_transfer) {
            $("#name_user_forwarder").text(name_user);
            $("#phone_user_forwarder").text(phone_user);
            $("#name_phone_user_new_forwarder").val(name_user + ' / ' + phone_user);
            this.name_user_forwarder = name_user;
            this.phone_user_forwarder = phone_user;
            this.id_vehicle_transfer = id_vehicle_transfer;
        },
        getInfoAssignedStaff(id, name_user, phone_user, name_staff, phone_staff, distance, price, note) {
            $("#name_phone_user_handling").val(name_user + ' / ' + phone_user);
            $("#name_phone_staff_handling").val(name_staff + ' / ' + phone_staff);
            $("#distance_handling").val(distance);
            $("#price_handling").val(price);
            $("#note_handling").val(note);
            $("#id_handling").data('id_handling', id);
        },
        getInfoCompleted(id, name_user, phone_user, name_staff, phone_staff, distance, price, note) {
            $("#name_phone_user_completed").val(name_user + ' / ' + phone_user);
            $("#name_phone_staff_completed").val(name_staff + ' / ' + phone_staff);
            $("#distance_completed").val(distance);
            $("#price_completed").val(price);
            $("#note_completed").val(note);
        },
        getInfoDeleted(id, name_user, phone_user, name_staff, phone_staff, distance, price, note) {
            $("#name_phone_user_deleted").val(name_user + ' / ' + phone_user);
            $("#name_phone_staff_deleted").val(name_staff + ' / ' + phone_staff);
            $("#distance_deleted").val(distance);
            $("#price_deleted").val(price);
            $("#note_deleted").val(note);
        },
        getStaffBranch(branch_id, id_staff_assign, namestaff, phonestaff) {
            this.branch_id = branch_id;
            this.id_staff_assign = id_staff_assign; 
            this.info_staff_assign = namestaff + ' / ' + phonestaff;
            $('#staff_assign').val(this.info_staff_assign);
            var distance_fly_bird = $('#distance_fly_bird'+branch_id).text();
            this.distance = distance_fly_bird;
            if (this.branch_id == null || this.branch_id == "" || this.id_staff_assign == null || this.id_staff_assign == "") {
                bootbox.alert("Hãy chọn nhân viên cứu hộ!");
                return false;
            }
            $('#ModalInfoPayment').modal('show');
            
            $('#ModalTransfer').on('hidden.bs.modal', function (e) {
                $(".list-group-item-branch-transfer").removeClass("active");
                $(".card .collapse").removeClass("show");
            });
        },
        assignStaffTransfer() {
            var data = {
                _token: this.csrf,
                branch_id: this.branch_id,
                assign_staff_id: this.id_staff_assign,
                distance: this.distance,
                price: this.price,
                note: this.note,
                id_vehicle_transfer: this.id_vehicle_transfer,
            };
            common.loading.show('body');
            $.post('/vehicle-forwarder/assign-staff-transfer', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/vehicle-forwarder';
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
        completedTransfer() {
            var id_vehicle_transfer = $("#id_handling").data('id_handling');
            if (id_vehicle_transfer == undefined || id_vehicle_transfer == "" || id_vehicle_transfer == null ) {
                return false;
            }
            var data = {
                _token: this.csrf,
                id_vehicle_transfer: id_vehicle_transfer,
            };
            common.loading.show('body');
            $.post('/vehicle-forwarder/complete-transfer', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/vehicle-forwarder';
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
        removeVehicleTransfer(id_vehicle_transfer) {
            var data = {
                _token: this.csrf,
                id_vehicle_transfer: id_vehicle_transfer,
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
                        $.post('/vehicle-forwarder/delete', data)
                        .done(response => {
                            if (response.error === 0) {
                                common.loading.hide('body');
                                bootbox.alert("Xoá thành công !!", function() {
                                    window.location = '/vehicle-forwarder';
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
            this.id_vehicle_transfer = '';
            this.distance = '';
            this.price = '';
            this.note = '';
        }
    }
});