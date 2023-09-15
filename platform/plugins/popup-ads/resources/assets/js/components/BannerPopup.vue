<template>
    <div class="home-popup__background" v-if="isShow" @click="closePopup()">
        <div class="home-popup__content">
            <div class="simple-banner with-placeholder">
                <a target="_self" :href="dataAds.url">
                    <img :src="dataAds.image" alt="" class="banner-image" />
                </a>
            </div>
            <div class="home-popup__close-area">
                <div class="popup__close-btn" @click="closePopup()">
                    <svg
                        viewBox="0 0 16 16"
                        stroke="#EE4D2D"
                        class="home-popup__close-button"
                    >
                        <path
                            stroke-linecap="round"
                            d="M1.1,1.1L15.2,15.2"
                        ></path>
                        <path stroke-linecap="round" d="M15,1L0.9,15.1"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { HalfCircleSpinner } from "epic-spinners";
export default {
    components: {
        HalfCircleSpinner,
    },
    props: {
        adsUrl: {
            type: String,
            default: () => null,
            required: true,
        },
    },
    data() {
        return {
            dataAds: null,
            isShow: false,
        };
    },
    mounted() {
        this.fetchAdsData();
    },
    methods: {
        fetchAdsData() {
            // sessionStorage.clear();
            axios({
                method: "post",
                url: this.adsUrl,
            })
                .then((response) => {
                    const result = response.data;
                    if (!result.error) {
                        this.checkAndSaveData(result.data);
                    }
                })
                .catch((err) => console.log(err));
        },
        checkAndSaveData(data, db) {
            for (let i = 0; i < data.length; i++) {
                const dataDB = data[i];
                const checkStorage = sessionStorage.getItem(dataDB.key);
                if (!checkStorage) {
                    const dateNow = new Date().getTime();
                    // 5 menit
                    const timeStampAgo = dateNow + 15 * 60 * 1000;
                    const dataStorage = {
                        key: btoa(dataDB.key),
                        timeStamp: timeStampAgo.toString().replace(/\D/g, ""),
                        sessionId: this.generateUUID(),
                        prefix: btoa(
                            JSON.stringify({
                                image: dataDB.image,
                                url: dataDB.url,
                            })
                        ),
                    };
                    sessionStorage.setItem(
                        dataDB.key,
                        JSON.stringify(dataStorage)
                    );
                    this.dataAds = dataDB;
                    this.isShow = true;
                    document.body.style.overflow = "hidden";
                    window.scrollTo({
                        top: 0,
                        behavior: "smooth",
                    });
                    break;
                } else {
                    const dataStorage = JSON.parse(checkStorage);
                    const currentTimeStamp = new Date().getTime();
                    const storedTimeStamp = parseInt(dataStorage.timeStamp);

                    if (currentTimeStamp > storedTimeStamp) {
                        const prefix = JSON.parse(atob(dataStorage.prefix));
                        const dateNow = new Date().getTime();
                        // 5 menit
                        const timeStampAgo = dateNow + 15 * 60 * 1000;
                        this.dataAds = prefix;
                        this.isShow = true;

                        dataStorage.timeStamp = timeStampAgo
                            .toString()
                            .replace(/\D/g, "");

                        sessionStorage.setItem(
                            dataDB.key,
                            JSON.stringify(dataStorage)
                        );
                        document.body.style.overflow = "hidden";
                        window.scrollTo({
                            top: 0,
                            behavior: "smooth",
                        });
                        break;
                    }
                }
            }
        },
        generateUUID() {
            return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(
                /[xy]/g,
                function (c) {
                    var r = (Math.random() * 16) | 0,
                        v = c === "x" ? r : (r & 0x3) | 0x8;
                    return v.toString(16);
                }
            );
        },
        closePopup() {
            this.isShow = false;
            document.body.style.overflow = "";
        },
    },
};
</script>
