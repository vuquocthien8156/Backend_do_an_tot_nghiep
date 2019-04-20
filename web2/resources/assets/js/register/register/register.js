'use strict';

const app = new Vue({

    el: '#register',
    components:'',
    data() {
        return {
           username:'',
           password:'',
           re_password:'',
           name:'',
           phone:'',
           gender:'',
           birthday:'',
           address:'',
        };
    },

	
    methods: {
        register() {
            $('#username').focus(function () {
                $('#error1').css('display','none');
                $('#error2').css('display','none');
                $('#error6').css('display','none');
                $('#error7').css('display','none');
            });
            $('#password').focus(function () {
                $('#error1').css('display','none');
                $('#error2').css('display','none');
                $('#error6').css('display','none');
                $('#error7').css('display','none');
            });
            $('#re_password').focus(function () {
                $('#error1').css('display','none');
                $('#error2').css('display','none');
                $('#error6').css('display','none');
                $('#error7').css('display','none');
            });
            $('#name').focus(function () {
                $('#error3').css('display','none');
                $('#error4').css('display','none');
                $('#error5').css('display','none');
            });
            $('#phone').focus(function () {
                $('#error3').css('display','none');
                $('#error4').css('display','none');
                $('#error5').css('display','none');
            });
            $('#gender').focus(function () {
                $('#error3').css('display','none');
                $('#error4').css('display','none');
                $('#error5').css('display','none');
            });
            $('#birthday').focus(function () {
                $('#error3').css('display','none');
                $('#error4').css('display','none');
                $('#error5').css('display','none');
            });
            $('#address').focus(function () {
                $('#error3').css('display','none');
                $('#error4').css('display','none');
                $('#error5').css('display','none');
            });
            var b = false;
            var c = false;
            var d = false;
            var e = false;
            if (this.phone.length < 10 || this.phone.length > 11) {
                $('#error5').css('display','block');
            }
            else
            {
                b=true;
            }
            if (this.username == "" || this.password == "" || this.re_password == "" || 
                this.username == null || this.password == null || this.re_password == null) {
                $('#error1').css('display','block');
            }
            else
            {
                c=true;
            }
            if (this.name == "" || this.phone == "" || this.gender == ""
                || this.birthday == "" || this.address == "" || this.name == null || this.phone == null || this.gender == null
                || this.birthday == null || this.address == null) {
                $('#error4').css('display','block');
            }
            else
            {
                d=true;
            }
            if (this.re_password == "" || this.re_password == null || this.password != this.re_password) {
                $('#error6').css('display','block');
            }
            else
            {
                e=true;
            }
            if (b==false || c == false || d==false || e==false) {
                $('#error6').css('display','block');   
            }
            else
            {
                var data = {
                username:this.username,
                password:this.password,
                re_password:this.re_password,
                name:this.name,
                phone:this.phone,
                gender:this.gender,
                birthday:this.birthday,
                address:this.address,
            };
            $.post('dangky', data)
                .done(response => {
                    if (response.status == "already") {
                        $('#error2').css('display','block');
                    }
                    if (response.error == 0) {
                        alert('Đăng ký thành công!');
                        window.location = 'login2';
                    }
                    else
                    {
                        $('#error').css('display','block');
                    } 
                })
            }
        }
	}
});
