'use strict';
import * as Pagination from 'laravel-vue-pagination';
const app = new Vue({

    el: '#manage-product',
    components: {Pagination},
    data() {
        return {
            results:{},
            exportproduct:{},
            name:'',
            img:'',
            selectedFile: null,
            imageUrl:null,
            ten:'',
            ma: '',
            masp:'',
            gia_goc:'',
            gia_size_vua:'',
            gia_size_lon:'',
            loaisp: '',
            ngay_ra_mat:'',
            mo_ta:''
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
                name: this.name,
                masp: this.masp,
                mo_ta:this.mo_ta,
            };
            if (page) {
                data.page = page;
            }
            $.get('search', data)
                .done(response => {
                    this.results = response.listSearch;
                    this.exportproduct = response.infoExportExcel;
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
        deleted(id) {
            var data = {
                id:id
            }
            bootbox.confirm({
                title: 'Thông báo',
                message: 'Bạn có xoá sản phẩm này không?',
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
                        bootbox.alert("xóa thành công !!", function() {
                             window.location.reload();
                        });
                        common.loading.hide('body');
                    }
                })
                    }
                }
            });
        
            
            
        },
        seeMoreDetail(ma_so, ten, ma_chu, ten_loai_sp, gia_san_pham, gia_vua, gia_lon, so_lan_dat, 
            ngay_ra_mat, mo_ta, ma_loai_sp, img) {
            // $("#edit").css('display','block');
            // $("#body").css('display','none');
            $("#avatarcollector_edit").attr('src', 'http://localhost:8888/' + img);
            this.selectedFile = img;
            $("#id_product").val(ma_so);
            $("#ten").val(ten);
            $("#ma").val(ma_chu);
            $("#loaisp").val(ma_loai_sp);
            $("#gia_goc").val(gia_san_pham);
            $("#gia_size_vua").val(gia_vua);
            $("#gia_size_lon").val(gia_lon);
            $("#ngay_ra_mat").val(ngay_ra_mat);
            $("#mo_ta").val(mo_ta);
            $("#so_lan_order").val(so_lan_dat);
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
            data.append('ma',  $('#ma').val());
            data.append('gia_goc',  $('#gia_goc').val());
            data.append('gia_size_vua',  $('#gia_size_vua').val());
            data.append('gia_size_lon',  $('#gia_size_lon').val());
            data.append('loaisp',  $('#loaisp').val());
            data.append('ngay_ra_mat',  $('#ngay_ra_mat').val());
            data.append('mo_ta',  $('#mo_ta').val());
            data.append('id',  $('#id_product').val());
            data.append('so_lan_order',  $('#so_lan_order').val());
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
