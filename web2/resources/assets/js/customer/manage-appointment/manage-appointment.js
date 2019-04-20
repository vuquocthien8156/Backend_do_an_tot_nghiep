'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#manage-appointment',
    components: { Pagination},
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            results: {},
            result_infoExport:{},
            username_phone_number: '',
            type_appointment: '',
            branch: '',
            from_date: '',
            to_date : '',

            type_appointment_update: '',
            info_user_update: '',
            date_send_update: '',
            time_send_update: '',
            branch_update: '',
            note_update: '',
            reminder_update: '',
            time_config_update: '',
            id_appointment: '',
        };
    },
    created() {
        this.searchAppointment(); 
    },
    methods: {
        formatDate(date) {
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return  date.getFullYear() + "-" + (date.getMonth()+1) + "-" +  date.getDate() + " " + strTime;
        },
        searchAppointment(page) {
            var data = {
                username_phone_number: this.username_phone_number,
                type_appointment: this.type_appointment,
                branch: this.branch,
                from_date: this.from_date,
                to_date: this.to_date,
                _token: this.csrf,
            };
            if (page) {
                data.page = page;
            }
            common.loading.show('body');
            $.post('/customer/manage-appointment/search', data)
                .done(response => {
                    this.results = response.listAppointment;
                    this.result_infoExport = response.listAppointmentExport;
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },
        getInfoAppointment(user_name, user_phone, type_appointment, appointment_at, name_branch, reminder, note, id_appointment) {
            this.info_user_update = user_phone + ' - ' + user_name;
            $("#info_user_update").val(this.info_user_update);
            this.type_appointment_update = type_appointment;
            var time_ = appointment_at.toString().split(' ');
            this.date_send_update = time_[0];
            this.time_send_update = time_[1];
            this.branch_update = name_branch;
            this.reminder_update = reminder;
            this.note_update = note;
            this.id_appointment = id_appointment;

        },
        updateAppointment() {
            if (this.type_appointment_update == null || this.type_appointment_update == "" || this.date_send_update == "" || this.date_send_update == null ||
                this.time_send_update == "" || this.time_send_update == null || this.branch_update == "" || this.branch_update == null) {
                bootbox.alert("Vui lòng điền đầy đủ thông tin!");
                return false;
            }
            
            var date_time = new Date(this.date_send_update + ' ' + this.time_send_update);
            var time_ = date_time.toUTCString().replace('GMT', '');
            var format_time = new Date(time_);
            this.time_config_update = this.formatDate(format_time);

            // get Phone User
            var info_user = $("#info_user_update").val().replace(" ", "");
            var numberphone = info_user.split('-');
            this.numberphone_user = numberphone[0];
            var data = {
                _token: this.csrf,
                type_appointment: this.type_appointment_update,
                branch: this.branch_update,
                note: this.note_update,
                reminder: this.reminder_update,
                time_config : this.time_config_update,
                numberphone_user : this.numberphone_user,
                id_appointment: this.id_appointment,
            };
            common.loading.show('body');
            $.post('/customer/save-appointment', data)
                .done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !!", function() {
                            window.location = '/customer/manage-appointment';
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
        deleteAppointment(id_appointment) {
            var data = {
                _token: this.csrf,
                id_appointment: id_appointment,
            }
            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có chắc chắn muốn xoá lịch hẹn này không?',
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
                        $.post('/customer/delete-appointment', data)
                            .done(response => {
                                if (response.error === 0) {
                                    common.loading.hide('body');
                                    bootbox.alert("Xoá thành công !!", function() {
                                        window.location = '/customer/manage-appointment';
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
    }
});