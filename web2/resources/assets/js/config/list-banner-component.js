'use strict';

import draggable from 'vuedraggable';

export default {
    template: '#banner_list',
    components: {
        draggable,
    },
    data() {
        return {}
    },
    props: {
        banners: {
            type: Array,
            default: () => []
        }
    },
    mounted() {
    },
    methods: {
        editBanner(banner) {
            this.$emit('edit-banner', banner);
        },
        deleteBanner(banner) {
            common.loading.show('body');
            $.ajax(`${window.location.pathname}/${banner.id}`, {
                method: 'POST',
                data: {'_method': 'DELETE'}
            })
                .done(response => {
                    bootbox.alert(response.msg, () => {
                        if (response.error == 0) {
                            window.location.reload();
                        }
                    });
                })
                .always(() => {
                    common.loading.hide('body');
                });
        },
        saveDisplayOrder() {
            common.loading.show('body');
            let ordered_ids = this.banners.map(banner => banner.id);
            $.ajax(`${window.location.pathname}/type/${this.bannerType}/display-order`, {
                method: 'POST',
                data: {
                    orders: ordered_ids
                }
            })
            .done(response => {
                bootbox.alert(response.msg, () => {
                    if (response.error == 0) {
                        window.location.reload();
                    }
                });
            })
            .always(() => {
                common.loading.hide('body');
            });
        },
    }
}
