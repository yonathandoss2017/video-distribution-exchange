<template>
    <div class="row search-filter-result">
        <div class="col-md-12">
            <div class="row animated fadeIn">
                <h4 class="text-center full-width" v-if="videos === false">&nbsp;</h4>
                <video-item
                        v-else
                        v-for="(value, key, index) in videos"
                        :key="key"
                        :index="index"
                        :video="value"
                ></video-item>
            </div>
            <div v-if="loaded === false" class="load-content">
                <div class="loader"></div>
                <div class="text-center animated flash infinite">{{ $t('marketplace.common.loading') }}...</div>
            </div>

            <div class="view-more text-uppercase text-center">
                <a :href="url"></a>
            </div>
        </div><!-- .col -->
    </div>
</template>


<script>

    export default {
        props: {
            playlist: 0,
            latest: 0,
            latestUpdate: 0,
            mostpopular: 0,
            random: 0,
            loadnow: false,
            cncVideo: 0,
            chinaVideo: 0,
            selectionVideo: 0,
        },
        data() {
            return {
                loadVideos: [],
                tempVideo: [],
                page: 1,
                loadStatus: false,
                url: ''
            }
        },
        components: {
            'video-item': require('./videoComponent').default
        },
        ready() {
            this.prepareComponent();
        },

        mounted() {
            this.prepareComponent();
        },

        computed: {
            videos() {
                if (this.loadVideos.length !== 0) {
                    
                    this.tempVideo = []

                    this.loadVideos.docs.forEach(  (video) => {
                        this.tempVideo.push(video)
                    })

                    return this.tempVideo
                }

                return false
            },
            loaded() {
                return this.loadStatus
            },
        },
        watch: {
            loadnow(newVal, oldVal) {
                if (newVal && !oldVal) {
                    this.prepareComponent()
                }
            }
        },
        methods: {
            prepareComponent() {
                if (this.loadnow) {
                    this.getVideos()

                    const filter = (1 === parseInt(this.selectionVideo) ? '&filter=playlist' : '&filter=video')
                    const cncPropertyId = (1 === parseInt(this.cncVideo) ? '&propertyId=144' : '')
                    const chinaPropertyId = (1 === parseInt(this.chinaVideo) ? '&propertyId=224,216,217,169,213,214,215,172' : '')
                    const selectionPropertyId = (1 === parseInt(this.selectionVideo) ? '&propertyId=7,8,9,10,140,141,143,148,175,176,177,219,220,221,222,223,181,182' : '')
                    const latestUpdate = '&latestUpdate=1'

                    this.url = '/marketplace/search?keywords=&page=0&limit=20' + filter + cncPropertyId + chinaPropertyId + selectionPropertyId + latestUpdate
                }
            },

            getVideos() {

                this.loadStatus = false

                const api = '/marketplace/query?start=0&limit=16'
                const filter = (1 === parseInt(this.selectionVideo) ? '&filter=playlist' : '&filter=video')
                const cncPropertyId = (1 === parseInt(this.cncVideo) ? '&propertyId=144' : '')
                const chinaPropertyId = (1 === parseInt(this.chinaVideo) ? '&propertyId=224,216,217,169,213,214,215,172' : '')
                const selectionPropertyId = (1 === parseInt(this.selectionVideo) ? '&propertyId=7,8,9,10,140,141,143,148,175,176,177,219,220,221,222,223,181,182' : '')
                const latestUpdate = '&latestUpdate=1'

                axios.get(api + filter + cncPropertyId + chinaPropertyId + selectionPropertyId +latestUpdate)
                .then( response => {
                    this.loadStatus = true

                    if(response.data.response) {
                        this.loadVideos = response.data.response
                    }
                }).catch( err => {
                    this.loadStatus = true
                })
            }

        }

    }
</script>

<style lang="scss" scoped>
    .full-width { width: 100%;}
    .playlist-wrapper .view-more {
        width: 56px;
        height: 56px;
        background-image: url(/images/marketplace/more-circle.png);
        background-size: contain;
        background-color: transparent;
        margin-top: 50px;
    }
    .playlist-wrapper .view-more:hover {
        background-image: url(/images/marketplace/more-circle-hover.png);
    }
</style>
