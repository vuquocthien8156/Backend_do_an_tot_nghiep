'use strict';
import './create-banner-component';
import ListBanner from './list-banner-component';
import draggable from 'vuedraggable';

const app = new Vue({
    el: '#app_banner',
    components: { ListBanner, draggable },
    data() {
        return {
            bannerType: null,
        };
    },
    created() {
    },
    mounted() {
    },
    watch: {

    },
    methods: {
        showEditBannerModal(bannerType, bannerToEdit) {
            this.bannerType = bannerType;
            this.$refs.banner.open(bannerToEdit);
        },
        saveDisplayOrder() {
            common.loading.show('body');
            let ordered_ids = this.banners.map(banner => banner.id);
            console.log(ordered_ids);

            // $.ajax(`${window.location.pathname}/type/${this.bannerType}/display-order`, {
            //     method: 'POST',
            //     data: {
            //         orders: ordered_ids
            //     }
            // })
            // .done(response => {
            //     bootbox.alert(response.msg, () => {
            //         if (response.error == 0) {
            //             window.location.reload();
            //         }
            //     });
            // })
            // .always(() => {
            //     common.loading.hide('body');
            // });
        },
    }
});