'use strict';
import * as Pagination from 'laravel-vue-pagination';
const app = new Vue({

    el: '#manage-news',
    components: {Pagination},
    data() {
        return {
            results:{},
            type:1,
            name:'',
            selectedFile: null,
            imageUrl:null,
            listImg:''
        };
    },

	created() {
        this.search();
    },

    filters: {
        contentSubstr:function(string) {
            return string.substring(0,30)
        },
    },

    methods: {
        search(page) {
            common.loading.show('body');
            var data = {
                name:this.name,
            };
            if (page) {
                data.page = page;
            }
            $.get('search', data)
                .done(response => {
                    this.results = response.listSearch;
                    common.loading.hide('body');
                })
                .fail(error => {
                    alert('Error!');
                })
        },

        showDescription(description) {
            $('#ModalShowDescription').modal('show');
            $('#description_show').text(description);
        },
        showMore(id) {
            common.loading.show('body');
            $("#frames").text('');
            $("#id_update").val(id);
            var data = {
                id:id,
                type:2
            }
            $.get('show-more-img', data)
                .done(response => {
                    this.listImg = response.listImg;
                    common.loading.hide('body');
                })
                .fail(error => {
                    alert('Error!');
                    common.loading.hide('body');
                })
            $('#showMore').modal('show');
        },
        deleted(id,status) {
            var data = {
                id:id,
                status:status
            }
            if (status == 1) {
                bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có muốn phục hồi tin tức này không?',
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
                message: 'Bạn có xoá tin tức này không?',
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
        seeMoreDetail(ten_tin_tuc, ma_tin_tuc, noi_dung, ngay_dang, hinh_tin_tuc) {
            var day = ngay_dang.split('-');
            var date = day[2]+'-'+day[1]+'-'+day[0];
            $('#ten').val(ten_tin_tuc);
            $('#id').val(ma_tin_tuc);
            $('#ND').val(noi_dung);
            $('#date').val(date);
            $("#avatarcollector_edit").attr('src', 'http://localhost:8000/' + hinh_tin_tuc);
            this.selectedFile = hinh_tin_tuc;
            $('#update').modal('show');
        },
        add() {
            $('#add').modal('show');
        },
        edit() {
            common.loading.show('body');
            var data = new FormData();
            let url = $('#form_edit_info').attr("action");
            data.append('files_edit', this.selectedFile);
            data.append('ten', $('#ten').val());
            data.append('ND', $('#ND').val());
            data.append('date', $('#date').val());
            data.append('id', $('#id').val());
            let options = {
                        method: 'POST',
                        data: data,
                        processData: false,
                        contentType: false,
                    };
           
            $.ajax(url, options).done(response => {
                    if (response.error === 0) {
                        bootbox.alert("Sửa thành công !!", function() {
                             window.location.reload();
                        });
                        common.loading.hide('body');
                    } else {
                        bootbox.alert('Thất bại!!!');
                        common.loading.hide('body');
                    }
                })
        },
        onSelectImageHandler(e) {
            this.avatar_path = null;
            let files = e.target.files;
            let done = (url) => {
                // this.$refs.fileInputEl.value = '';
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
        edit1 : function(event) {
            var data = new FormData();
            data.append('files_edit', this.selectedFile);
            let url = $('#form_edit_info').attr("action");
            let options = {
                        method: 'POST',
                        data: data,
                        processData: false,
                        contentType: false,
                    };
            $.ajax(url, options).done(response => {
                    if (response.error === 0) {
                        
                    } else {
                        bootbox.alert('Error!!!');
                        common.loading.hide('body');
                    }
                })
        },
        exit() {
            $("#edit").css('display','none');
            $("#body").css('display','block');
        },
        luu() {
            // common.loading.show('body');
            var data = new FormData();
            data.append('files_edit', this.selectedFile);
            let url = $('#add-new').attr("action");
            data.append('ten', this.ten);
            data.append('ma', this.ma);
            data.append('gia_goc', this.gia_goc);
            data.append('gia_size_vua', this.gia_size_vua);
            data.append('gia_size_lon', this.gia_size_lon);
            data.append('loaisp', this.loaisp);
            data.append('ngay_ra_mat', this.ngay_ra_mat);
            data.append('mo_ta', this.mo_ta);
            let options = {
                        method: 'POST',
                        data: data,
                        processData: false,
                        contentType: false,
                    };
            $.ajax(url, options).done(response => {
                    if (response.error === 0) {
                        alert('Thêm thành công!!!');
                        window.location = 'manage';
                        common.loading.hide('body');
                        window.location.reload();
                    } else {
                        alert('Thêm thất bại!!!');
                        common.loading.hide('body');
                    }
                })
        }
	}
});
