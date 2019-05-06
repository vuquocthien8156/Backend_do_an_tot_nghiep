'use strict';

const app = new Vue({

    el: '#login',
    components:'',
    data() {
        return {
           username:'',
           password:'',
        };
    },

    created() {
        $("#btn-tk").hide();
        $("#dn2").hide();
    },
	
    methods: {
        hide() {
            $("#password1").hide();
            $("#btn-sdt").hide();
            $("#btn-tk").show();
            $("#username").attr('placeholder', 'Nhập sdt');
            $("#dn2").show();
            $("#dn1").hide();
        },
        hide1() {
            $("#dn1").show();
            $("#dn2").hide();
            $("#password1").show();
            $("#btn-sdt").show();
            $("#btn-tk").hide();
            $("#username").attr('placeholder', 'Nhập email');
        },
        login() {
        	if (this.username == '' || this.password == '' || this.username == null || this.password == null) {
        		alert('vui lòng nhập đầy đủ thông tin!');
        	}
        	else
        	{
        		$('#error').css('display','none');
                var data = {
                username: this.username,
                password:this.password,
            };
            $.ajax({
                    url: 'dangnhap',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'post',
                    data: data,
                    success: function (result) {
                        if (result.error === 0) {
                            alert('Thành công!');
                            window.location = 'home';
                        } else {
                            if (result.error === 0) {
                                alert('Sai tên hoặc mật khẩu!');
                            }            
                            
                        }
                    },
            });
        	}
        	$('#username').focus(function () {
        		$('#error').css('display','none');
        	});
        	$('#password').focus(function () {
        		$('#error').css('display','none');
        	});
        },
        login_sdt() {
            if (this.username == '' && this.username == null) {
                alert('vui lòng nhập đầy đủ thông tin!');
            }
            else
            {
                var data = {
                username: this.username,
            };
            $.ajax({
                    url: 'dangnhapsdt',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'post',
                    data: data,
                    success: function (result) {
                        if (result.error === 0) {
                            alert('Thành công!');
                            window.location = 'home';
                        } else {            
                            $('#error').css('display','block');
                        }
                    },
            });
            }
            $('#username').focus(function () {
                $('#error').css('display','none');
            });
            $('#password').focus(function () {
                $('#error').css('display','none');
            });
        }
	}
});
