<template>
    <div class="row search-filter-result">
        <div class="col-md-12">
            <div class="row animated fadeIn">
                <div class="col-md-3 playlist-main" v-for="genre in genres">
                    <div  class="video-img main">
                        <a :href="url + '/marketplace/search?playlist_id=' + genre.id">
                            <img :src="genre.image" :alt="genre.value" :title="genre.value" />
                            <span class="genre">{{ genre.name }}</span>
                        </a>
                        <div class="playlist-detail">
                            <div class="playlist-number"><span class="amount-numbers">{{ genre.videos_count }}</span><br>{{ $t('marketplace.common.videos') }}</div>
                            <div class="playlist-icon"><img src="/images/marketplace/playlist.svg" alt=""></div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="loaded === false" class="load-content genreList">
                <div class="loader"></div>
                <div class="text-center animated flash infinite">{{ $t('marketplace.common.loading') }}...</div>
            </div>
        </div><!-- .col -->
    </div>
</template>


<script>
    export default {
        props: {
            loadnow: false,
        },
        data() {
            return {
                url: window.location.origin,
                loadStatus: false,
                genreList:[
                    {
                        name: this.$t('marketplace.common.politics'),
                        image: '/images/marketplace/politics.png',
                        value: 'politics',
                        id: 171
                    },
                    {
                        name: this.$t('marketplace.common.finance'),
                        image: '/images/marketplace/finance.png',
                        value: 'finance',
                        id: 172
                    },
                    {
                        name: this.$t('marketplace.common.science'),
                        image: '/images/marketplace/science.png',
                        value: 'science',
                        id: 173
                    },
                    {
                        name: this.$t('marketplace.common.society'),
                        image: '/images/marketplace/society.png',
                        value: 'society',
                        id: 174
                    },
                    {
                        name: this.$t('marketplace.common.cultrue'),
                        image: '/images/marketplace/cultrue.png',
                        value: 'cultrue',
                        id: 175
                    },
                    {
                        name: this.$t('marketplace.common.military'),
                        image: '/images/marketplace/military.png',
                        value: 'military',
                        id: 176
                    },
                    {
                        name: this.$t('marketplace.common.education'),
                        image: '/images/marketplace/education.png',
                        value: 'education',
                        id: 177
                    },
                    {
                        name: this.$t('marketplace.common.health'),
                        image: '/images/marketplace/health.png',
                        value: 'health',
                        id: 178
                    },
                    {
                        name: this.$t('marketplace.common.sports'),
                        image: '/images/marketplace/sports.png',
                        value: 'sports',
                        id: 179
                    },
                    {
                        name: this.$t('marketplace.common.food'),
                        image: '/images/marketplace/food.png',
                        value: 'food',
                        id: 180
                    },
                    {
                        name: this.$t('marketplace.common.nature'),
                        image: '/images/marketplace/nature.png',
                        value: 'nature',
                        id: 181
                    },
                    {
                        name: this.$t('marketplace.common.travel'),
                        image: '/images/marketplace/travel.png',
                        value: 'travel',
                        id: 182
                    },
                    {
                        name: this.$t('marketplace.common.automobile'),
                        image: '/images/marketplace/automobile.png',
                        value: 'automobile',
                        id: 183
                    },
                    {
                        name: this.$t('marketplace.common.character'),
                        image: '/images/marketplace/character.png',
                        value: 'character',
                        id: 184
                    },
                    {
                        name: this.$t('marketplace.common.others'),
                        image: '/images/marketplace/others.png',
                        value: 'others',
                        id: 185
                    },
                    {
                        name: this.$t('marketplace.common.cncoriginal'),
                        image: '/images/marketplace/cncoriginal.png',
                        value: 'cncoriginal',
                        id: 186
                    },
                ]
            }
        },
        ready() {
            this.prepareComponent();
        },

        mounted() {
            this.prepareComponent();
        },
        computed: {
            genres() {
                return this.genreList
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
            },
        },
        methods: {
            prepareComponent() {
                if (this.loadnow) {
                    this.getVideos()
                }
            },

            getVideos() {

                this.loadStatus = false


                axios.get('/marketplace/query?start=0&limit=16&filter=playlist&propertyId=149')
                .then( response => {
                    this.loadStatus = true

                    if(response.data.response) {

                        _.each(this.genreList, function(genre) { 

                           var playlist = _.find(response.data.response.docs, function(o) { return o.ivx_id == genre.id; });

                           genre.videos_count = playlist.videos_count
                        });
                    }

                }).catch( err => {
                    this.loadStatus = true
                })
            }

        }

    }
</script>

<style lang="scss" scoped>
    .playlist-wrapper .genre {
        color: #fff;
        position: absolute;
        left: 12px;
        top: 6px;
        font-size: 20px;
        font-weight: 500;
        margin: 0
    }
</style>
