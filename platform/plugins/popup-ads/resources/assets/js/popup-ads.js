import BannerPopup from './components/BannerPopup.vue';

if (typeof vueApp !== 'undefined') {
    vueApp.booting(vue => {
        vue.component('banner-popup', BannerPopup);
    });
}
