'use strict';

Vue.component('banner', {
    template: '#banner_modal',
    data() {
        return {
            bannerActionType: 1,
            urlToOpen: null,
            selectedFile: null,
            editingBanner: null,

            cropper: null,
            choosedImage: null,
            imageUrl: null,
            aspectRatio: 3 / 2,
            bannerType: 1,
        }
    },
    props: {
        // bannerType: {
        //     type: Number,
        //     default: null
        // }
    },
    mounted() {
        $(this.$refs.modalEl).modal({
            show: false,
        });
        $(this.$refs.modalEl).on('hidden.bs.modal', e => {
            this.bannerActionType = 1;
            this.urlToOpen = null;
            this.editingBanner = null;
            this.selectedFile = null;

            this.imageUrl = null;
            if (this.cropper) {
                this.pDestroyCropper();
            }
        });

        this.pInitCropper();
    },
    methods: {
        open(bannerToEdit) {
            $(this.$refs.modalEl).modal('show');
            if (bannerToEdit) {
                this.editingBanner = bannerToEdit;
                this.imageUrl = bannerToEdit.originalResource;
                this.bannerActionType = bannerToEdit.bannerActionType;
                const TYPE_OPEN_URL = 4;
                if (TYPE_OPEN_URL === this.bannerActionType) {
                    this.urlToOpen = bannerToEdit.actionOnClickTarget;
                }

                if (this.cropper) {
                    this.pDestroyCropper();
                }
                Vue.nextTick(this.pInitCropper);
            }
        },
        close() {
            $(this.$refs.modalEl).modal('hide');
        },
        pInitCropper() {
            let image = document.getElementById('crop_image');
            this.cropper = new Cropper(this.$refs.bannerImgEl, {
                viewMode: 3,
                initialAspectRatio: this.aspectRatio,
                aspectRatio: this.aspectRatio
            });
        },
        pDestroyCropper() {
            this.cropper.destroy();
            this.cropper = null;
        },
        onSelectImageHandler(e) {
            if (this.cropper) {
                this.pDestroyCropper();
            }

            let files = e.target.files;
            let done = (url) => {
                this.$refs.fileInputEl.value = '';
                //this.$refs.bannerImgEl.src = url;
                this.imageUrl = url;
                Vue.nextTick(this.pInitCropper);
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
        changeAspectCropRatio(ratio) {
            this.aspectRatio = ratio;
            this.cropper.setAspectRatio(ratio);
        },
        saveBanner() {
            if (this.aspectRatio != null) {
                if (!this.cropper) {
                    return;
                }
                common.loading.show('body');
                try {
                    let canvas = this.cropper.getCroppedCanvas();
                    canvas.toBlob(blob => {
                        var formData = new FormData();

                        formData.append('file', this.selectedFile);
                        formData.append('banner', blob);
                        formData.append('bannerType', this.bannerType);
                        formData.append('bannerActionType', this.bannerActionType);
                        formData.append('aspectRatio', this.aspectRatio);
                        if (this.urlToOpen) {
                            formData.append('urlToOpen', this.urlToOpen);
                        }

                        let url = window.location.pathname;
                        let options = {
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                        };

                        if (this.editingBanner) {
                            url = `${url}/${this.editingBanner.id}`;
                            formData.append('_method', 'PUT');
                        }

                        $.ajax(url, options)
                            .done(response => {
                                let msg = response.msg;
                                if (response.error === 0) {
                                    common.loading.hide('body');
                                    bootbox.alert("Lưu thành công !!", function() {
                                        window.location.reload();
                                    })
                                } 
                            })
                            .always(() => {
                                common.loading.hide('body');
                            });
                    }, 'image/jpeg', 1);
                } catch (e) {
                    bootbox.alert('Error');
                    common.loading.hide('body');
                }
            } else {
                alert('Vui lòng chọn tỉ lệ màng hình')
            }
        },

        hideRatio() {
            if(this.bannerType == 2) {
                $('#Ratio1').hide();
                $('#Ratio7').hide();
                $('#Ratio3').hide();
                $('#Ratio2').hide();
                $('#Ratio5').hide();
                $('#Ratio6').hide();
                this.changeAspectCropRatio(3/2);
            } else {
                $('#Ratio1').show();
                $('#Ratio7').show();
                $('#Ratio3').show();
                $('#Ratio2').show();
                $('#Ratio5').show();
                $('#Ratio6').show();
            }
        },
    }
});