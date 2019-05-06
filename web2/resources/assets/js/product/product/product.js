'use strict';
import * as Pagination from 'laravel-vue-pagination';
const app = new Vue({

    el: '#manage-product',
    components: {Pagination},
    data() {
        return {
            results:{},
            name:'',
            img:'',
            selectedFile: null,
        };
    },

	created() {
        this.search();
    },
    methods: {
        search(page) {
            console.log("asd");
            var data = {
                name: this.name,
            };
            if (page) {
                data.page = page;
            }
            $.get('search', data)
                .done(response => {
                    console.log(response);
                    this.results = response.listSearch;
                })
                .fail(error => {
                    alert('Error!');
                })
        },
        deleted(id) {
            var data = {
                id:id
            }
            var r = confirm('bạn muốn xóa');
            if (r == true) {
                $.post('delete', data)
                .done(response => {
                    if (response.error == 0) {
                        alert("xóa thành công !!");
                        window.location = 'product';
                    }
                })
            }
        },
        seeMoreDetail(ma_so, ten, ma_chu, ten_loai_sp, gia_san_pham, gia_vua, gia_lon, so_lan_dat, 
            ngay_ra_mat, mo_ta, ma_loai_sp) {
            $("#edit").css('display','block');
            $("#body").css('display','none');
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
        },
        edit() {
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
                        alert('Thành công!!!');
                        window.location = 'manage';
                    } else {
                        alert('Thất bại!!!');
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
            console.log(this.selectedFile);
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
	}
});
