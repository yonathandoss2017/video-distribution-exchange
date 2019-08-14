<template>
    <div class="row search-filter-result">
        <div class="col-md-12">
            <div class="row animated fadeIn">
                <h4 class="text-center full-width" v-if="contents === false">&nbsp;</h4>
                <content-item
                        v-else
                        v-for="(value, key, index) in contents"
                        :key="key"
                        :index="index"
                        :content="value"
                ></content-item>
            </div>
            <div v-if="loaded === false" class="load-content">
                <div class="loader"></div>
                <div class="text-center animated flash infinite">{{ $t('marketplace.common.loading') }}...</div>
            </div>
        </div><!-- .col -->
    </div>
</template>


<script>
    import store from '../../store/marketplace'

    export default {
        props: {
            loadnow: 0,
            channels: 0,
            genres: 0,
            xinhua: 0,
        },
        data() {
            return {
                loadContents: [],
                tempContent: [],
                page: 1,
                loadStatus: false,
                url: '',
                xinhuaChannels: [],
                otherChannels: [],
                // type: this.channels == 1 ? 'channels' : 'genres'
            }
        },
        components: {
            'content-item': require('./contentComponent').default
        },
        ready() {
            this.prepareComponent();
        },

        mounted() {
            this.prepareComponent();
        },

        computed: {
            contents() {
                if (this.loadContents.length !== 0) {
                    // if (this.loadContents.length > 8)
                    //     return this.loadContents.slice(0,8)
                    // else
                        return this.loadContents
                }

                return false
            },
            loaded() {
                return this.loadStatus
            },
        },
        watch: {
            loadnow(newVal, oldVal) {
                if (newVal === 1 && oldVal === 0) {
                    this.prepareComponent()
                }
            }
        },
        methods: {
            prepareComponent() {
                if(this.channels == 1) {
                    this.getOtherChannels()
                } else if(this.genres == 1) {
                    this.getGenres()
                } else if(this.xinhua == 1) {
                    this.getXinhuaChannels()
                }
            },

            getOtherChannels() {

                axios.get('/marketplace/property/cpList')
                .then( response => {
                    if(response.data) {
                        this.loadStatus = false
                        let that = this
                        // var regex = /\u65b0\u534e\u793e/
                        var cpList = [144,214,7,9]
                        _.forEach(cpList, function (val) {
                            var cp = _.find(response.data.data, function (data) { return data.id == val })
                            that.loadContents.push(cp)
                        })
                        this.loadStatus = true
                    }
                })
                .catch( err => {
                    this.loadStatus = true
                })
            },

            getGenres() {
                this.loadStatus = false
                var genreList = Object.values(store.state.genreData)
                this.loadContents = genreList
                this.loadStatus = true
            },

            getXinhuaChannels() {
                axios.get('/marketplace/property/cpList')
                .then( response => {
                    if(response.data) {
                        this.loadStatus = false
                        // var regex = /\u65b0\u534e\u793e/
                        this.loadContents = response.data.filter(data => data.organization_id == 5)
                        this.loadStatus = true
                    }
                })
                .catch( err => {
                    this.loadStatus = true
                })
            },
        }

    }
</script>

<style lang="scss" scoped>
    .full-width { width: 100%;}
</style>
