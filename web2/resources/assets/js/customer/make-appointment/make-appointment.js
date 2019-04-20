'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#setting-make-appointment',
    components: { Pagination},
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            results: {},
            type_appointment: '',
            info_user: '',
            date_send: '',
            time_send: '',
            branch: '',
            note: '',
            reminder: true,
            time_config: '',
        };
    },
    created() {},
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
        saveAppointment() {
            var type_appointment = $('#type_appointment').val();
            var info_customer = $('#info_customer').val();
            var date_send = $('#date_send').val();
            var time_send = $('#time_send').val();
            var branch = $('#branch').val();
            if(type_appointment == null || type_appointment == '') {
                $('#type_appointment').focus();
                return false;
            } else if (info_customer == null || info_customer == '') {
                $('#info_customer').focus();
                return false;
            } else if (date_send == null || date_send == '') {
                $('#date_send').focus();
                return false;
            } else if (time_send == null || time_send == '') {
                $('#time_send').focus();
                return false;
            } else if (branch == null || branch == '') {
                $('#branch').focus();
                return false;
            }
            if (this.type_appointment == null || this.type_appointment == "" || this.date_send == "" || this.date_send == null ||
                this.time_send == "" || this.time_send == null || this.branch == "" || this.branch == null) {
                bootbox.alert("Vui lòng điền đầy đủ thông tin!");
                return false;
            }
            
            var date_time = new Date(this.date_send + ' ' + this.time_send);
            var time_ = date_time.toUTCString().replace('GMT', '');
            var format_time = new Date(time_);
            this.time_config = this.formatDate(format_time);

            // get Phone User
            var info_user = $('#info_customer').val().replace(" ", "");
            var numberphone = info_user.split('-');
            this.numberphone_user = numberphone[0];
            var data = {
                _token: this.csrf,
                type_appointment: this.type_appointment,
                info_user: $('#info_customer').val(),
                date_send: this.date_send,
                time_send: this.time_send,
                branch: this.branch,
                note: this.note,
                reminder: this.reminder,
                time_config : this.time_config,
                numberphone_user : this.numberphone_user,
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
    }
});