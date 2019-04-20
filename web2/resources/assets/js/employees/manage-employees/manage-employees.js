'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#manage-employees',
    components: { Pagination},
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            results: {},
            result_infoExport:{},
            branch_id: '',
            name_phone_email: '',
            status: '',
            type_employees: '',
            branch_name_edit: '',
            category_name_edit: '',
            avatar_path: '',
            imageUrl: '',
            selectedFile: null,
        };
    },
    created() {
        this.searchEmployees();
    },
    mounted() {
        
    },
    methods: {
        searchEmployees(page) {
            var data = {
                name_phone_email: this.name_phone_email,
                branch_id: this.branch_id,
                status: this.status,
                type_employees: this.type_employees,
            };
            if (page) {
                data.page = page;
            }
            common.loading.show('body');
            $.post('/employees/search', data)
                .done(response => {
                    this.results = response.listSearchEmployees;
                    this.result_infoExport = response.listEmployeesExport;
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },
        deleteEmployees(id) {
            var data = {
                id_staff: id
            }
            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có chắc chắn muốn xoá khách hàng này không?',
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
                        $.post('/employees/delete', data)
                        .done(response => {
                            if (response.error === 0) {
                                common.loading.hide('body');
                                bootbox.alert("Xoá thành công !!", function() {
                                    window.location = '/employees/manage';
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
        modalImage(path_source, img_src) {
            var modal = document.getElementById('myModal_chat');
            var modalImg = document.getElementById("img01");
            var span = document.getElementsByClassName("close_chat")[0];
            modal.style.display = "block";
            modalImg.src = path_source + '/' + img_src;
            span.onclick = function() { 
                modal.style.display = "none";
            };
        },

        getmodelEmployess(id, name, email, branch_id, category_id, avatar_path, birthday) {
            this.avatar_path = avatar_path;
            $("#id_user_edit").val(id);
            $("#name_employees_edit").val(name);
            $("#email_edit").val(email);
            $("#branch_name_edit").val(branch_id);
            $("#category_name_edit").val(category_id);
            $('#avatar_path').val(avatar_path);
            $('#birthday_edit').val(birthday);
            this.imageUrl =null;
            $('#ModalUpgradeEmployees').modal('show');
        },

        submitEditEmployees : function(event) {
            try {

                var name_employees_edit = $('#name_employees_edit').val();
                var email_edit = $('#email_edit').val();
                var branch_name_edit = $('#branch_name_edit').val();
                var category_name_edit = $('#category_name_edit').val();
                var birthday_edit = $('#birthday_edit').val();
                if(name_employees_edit == null || name_employees_edit == '') {
                    $('#name_employees_edit').focus();
                    bootbox.alert("Vui lòng nhập tên!");
                    return false;
                } else if (email_edit == null || email_edit == '') {
                    $('#email_edit').focus();
                    bootbox.alert("Vui lòng nhập Email!");
                    return false;
                } else if (birthday_edit == null || birthday_edit == '') {
                    $('#birthday_edit').focus();
                    bootbox.alert("Hãy nhập ngày sinh!");
                    return false;
                } else if (branch_name_edit == null || branch_name_edit == '') {
                    $('#branch_name_edit').focus();
                    bootbox.alert("Vui lòng chọn chi nhánh!");
                    return false;
                } else if (category_name_edit == null || category_name_edit == '') {
                    $('#category_name_edit').focus();
                    bootbox.alert("Hãy chọn loại nhân viên!");
                    return false;
                }

                var data = new FormData();
                    data.append( 'files', this.selectedFile );
                    data.append('id_user_edit', $('#id_user_edit').val());
                    data.append('name_employees_edit', name_employees_edit);
                    data.append('email_edit', email_edit);
                    data.append('branch_name_edit', branch_name_edit);
                    data.append('category_name_edit', category_name_edit);
                    data.append('birthday_edit', birthday_edit);
                    data.append('avatar_path', $('#avatar_path').val());

                common.loading.show('body');
                $('#ModalUpgradeEmployees').modal('hide');
                let url = $('#from_upgrade_employess').attr("action");
                let options = {
                            method: 'POST',
                            data: data,
                            processData: false,
                            contentType: false,
                        };
                $.ajax(url, options).done(response => {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Cập nhật thành công !!", function() {
                            window.location.reload();
                        })
                    } else {
                        bootbox.alert('Error!!!');
                    }
                })
                .always(() => {
                    common.loading.hide('body');
                });
            } catch (e) {
                bootbox.alert('Error');
                common.loading.hide('body');
            }
            event.preventDefault();
        },

        onSelectImageHandler(e) {
            this.avatar_path = null;
            let files = e.target.files;
            let done = (url) => {
                this.$refs.fileInputEl.value = '';
                //this.$refs.bannerImgEl.src = url;
                this.imageUrl = url;
            };
            let reader;
            let file;
            let url;

            if (files && files.length > 0) {
                file = files[0];
                this.selectedFile = file;

                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = e => {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        },
    }
});