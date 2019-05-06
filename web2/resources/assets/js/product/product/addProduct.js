'use strict';
import * as Pagination from 'laravel-vue-pagination';
const app = new Vue({

    el: '#manage-add-product',
    components: {Pagination},
    data() {
        return {
            ten:'',
            ma:'',
            gia_goc:'',
            gia_size_vua:'',
            gia_size_lon:'',
            ngay_ra_mat:'',
            loaisp:'',
            mo_ta:'',
            selectedFile: null,
        };
    },

	created() {
       
    },
    methods: {
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
        luu() {
            var data = new FormData();
            data.append('files_edit', this.selectedFile);
            let url = $('#add').attr("action");
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
                    } else {
                        alert('Thêm thất bại!!!');
                    }
                })
        }
	}
});
