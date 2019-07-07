'use strict';
import * as Pagination from 'laravel-vue-pagination';
const app = new Vue({

    el: '#manage-discount',
    components: {Pagination},
    data() {
        return {
            results:{},
            type:1,
            selectedFile: null,
            imageUrl:null,
            listImg:'',
            path:'',
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
                type:this.type,
            };
            if (page) {
                data.page = page;
            }
            $.get('search', data)
                .done(response => {
                    this.results = response.listSearch;
                    if (this.results.data == '' || this.results.data == null) {
                        this.path = null;    
                    }else {
                        this.path = this.results.data[0].pathToResource;
                    }
                    common.loading.hide('body');
                })
                .fail(error => {
                    alert('Error!');
                })
        },

        deleted(id,status) {
            var data = {
                id:id,
                status:status
            }
            if(status == 1) {
                bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có muốn phục hồi khuyến mãi này không?',
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
                message: 'Bạn có muốn xoá khuyến mãi này không?',
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
        seeMoreDetail(ma_code,ten_khuyen_mai,mo_ta,so_phan_tram,so_tien,so_sp_qui_dinh,so_tien_qui_dinh_toi_thieu,gioi_han_so_code,ngay_bat_dau,ngay_ket_thuc,hinh_anh,ma_khuyen_mai,so_sp_tang_kem,ma_san_pham) {
            $("#avatarcollector_edit").attr('src', this.path + hinh_anh);
            this.selectedFile = hinh_anh;
            $('#ten').val(ten_khuyen_mai);
            $('#ma').val(ma_code);
            $('#id').val(ma_khuyen_mai);
            $('#MT').val(mo_ta);
            $('#SPT').val(so_phan_tram);
            $('#ST').val(so_tien);
            $('#SSPQD').val(so_sp_qui_dinh);
            $('#STQDTT').val(so_tien_qui_dinh_toi_thieu);
            $('#GHSC').val(gioi_han_so_code);
            $('#NBD').val(ngay_bat_dau);
            $('#NKT').val(ngay_ket_thuc);
            $('#SSPTK').val(so_sp_tang_kem);
            $('#SP').val(ma_san_pham);
            console.log(ma_san_pham);
            $('#update').modal('show');
        },
        add() {
            $('#add').modal('show');
        },
        showMore(id) {
            common.loading.show('body');
            $("#frames").text('');
            $("#id_update").val(id);
            var data = {
                id:id,
                type:4
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
        edit() {
            common.loading.show('body');
            var data = new FormData();
            let url = $('#form_edit_info').attr("action");
            data.append('files_edit', this.selectedFile);
            data.append('ten_khuyen_mai',$('#ten').val());
            data.append('ma_code',$('#ma').val());
            data.append('mo_ta',$('#MT').val());
            data.append('so_phan_tram',$('#SPT').val());
            data.append('so_tien',$('#ST').val());
            data.append('so_sp_qui_dinh',$('#SSPQD').val());
            data.append('so_tien_qui_dinh_toi_thieu',$('#STQDTT').val());
            data.append('gioi_han_so_code',$('#GHSC').val());
            data.append('ngay_bat_dau',$('#NBD').val());
            data.append('ngay_ket_thuc',$('#NKT').val());
            data.append('id',$('#id').val());
            data.append('type',this.type);
            data.append('ma_san_pham',$('#SP').val());
            data.append('so_sp_tang_kem',$('#SSPTK').val());
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
