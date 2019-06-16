'use strict';
import * as Pagination from 'laravel-vue-pagination';
const app = new Vue({

    el: '#manage-thongke',
    components: {Pagination},
    data() {
        return {
            results:{},
            exportproduct:{},
            img:'',
            thongke: 'week',
            selectedFile: null,
            imageUrl:null,
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
                thongke: this.thongke,
            };
            if (page) {
                data.page = page;
            }
            $.get('search-thong-ke', data)
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
	}
});
