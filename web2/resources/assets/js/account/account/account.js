'use strict';
import * as Pagination from 'laravel-vue-pagination';
const app = new Vue({

    el: '#manage-account',
    components: {Pagination},
    data() {
        return {
            results:{},
            exportaccount: {},
            name:'',
            phone:'',
            selectedFile: null,
            imageUrl: null,
            gender: '',
            loai_tai_khoan:'',
            path: '',
        };
    },

	created() {
        this.search();
    },
    methods: {
        search(page) {
            var data = {
                phone:this.phone,
                name: this.name,
                gender:this.gender,
                loai_tai_khoan:this.loai_tai_khoan
            };
            if (page) {
                data.page = page;
            }
            common.loading.show('body');
            $.get('search', data)
                .done(response => {
                    this.results = response.listSearch;
                    if (this.results.data == '' || this.results.data == null) {
                        this.path = null;    
                    }else {
                        this.path = this.results.data[0].pathToResource;
                    }
                    this.exportaccount = response.infoExportExcel;
                    common.loading.hide('body');
                })
                .fail(error => {
                    bootbox.alert('Error!');
                })
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
        deleted(id, status) {
            var data = {
                id:id,
                status:status
            }
            if (status == 1) {
                bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có muốn phục hồi tài khoản này không?',
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
                        bootbox.alert("Phục hồi thành công !!", function() {
                             window.location.reload();
                        });
                        common.loading.hide('body');
                    }
                })
                    }
                }
            });
            }else {
                bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có muốn xoá tài khoản này không?',
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
                        bootbox.alert("Xóa thành công !!", function() {
                             window.location.reload();
                        });
                        common.loading.hide('body');
                    }
                })
                    }
                }
            });
            }
        },
        seeMoreDetail(ten, sdt, ngay_sinh, gioi_tinh, diem_tich, dia_chi, email, avatar, id) {
            $("#avatarcollector_edit").attr('src', this.path + avatar);
            this.selectedFile = avatar;
            $("#id_user").val(id);
            $("#ten").val(ten);
            $("#SDT").val(sdt);
            $("#NS").val(ngay_sinh);
            $("#gender").val(gioi_tinh);
            $("#diemtich").val(diem_tich);
            $("#diachi").val(dia_chi);
            $("#email").val(email);
            $('#update').modal('show');
        },
        edit() {
            common.loading.show('body');
            var data = new FormData();
            if($('#gender').val() == null || $('#gender').val() == "") {
                bootbox.alert('Vui lòng chọn giới tính');
                common.loading.hide('body');
                return false;
            }
            if($('#ten').val() == null || $('#ten').val() == "") {
                bootbox.alert('Vui lòng nhập tên');
                common.loading.hide('body');
                return false;
            }
            if($('#SDT').val() == null || $('#SDT').val() == "") {
                bootbox.alert('Vui lòng nhập số điện thoại');
                common.loading.hide('body');
                return false;
            }
            if($('#NS').val() == null || $('#NS').val() == "") {
                bootbox.alert('Vui lòng chọn ngày sinh');
                common.loading.hide('body');
                return false;
            }
            if($('#diachi').val() == null || $('#diachi').val() == "") {
                bootbox.alert('Vui lòng nhập địa chỉ');
                common.loading.hide('body');
                return false;
            }
            if($('#email').val() == null || $('#email').val() == "") {
                bootbox.alert('Vui lòng điền email');
                common.loading.hide('body');
                return false;
            }
            let url = $('#form_edit_info').attr("action");
            data.append('files_edit', this.selectedFile);
            data.append('ten', $('#ten').val());
            data.append('SDT',  $('#SDT').val());
            data.append('NS',  $('#NS').val());
            data.append('gender',  $('#gender').val());
            data.append('diemtich',  $('#diemtich').val());
            data.append('diachi',  $('#diachi').val());
            data.append('email',  $('#email').val());
            data.append('id',  $('#id_user').val());
            let options = {
                        method: 'POST',
                        data: data,
                        processData: false,
                        contentType: false,
                    };
           
            $.ajax(url, options).done(response => {
                    if (response.error === 0) {
                       bootbox.alert("Thành công !!", function() {
                             window.location.reload();
                        });
                        common.loading.hide('body');
                    } else {
                        bootbox.alert('Thất bại!!!');
                    }
                })
        },
	}
});
