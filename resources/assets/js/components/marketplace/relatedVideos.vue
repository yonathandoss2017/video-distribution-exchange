<template>
    <div class="col-md-4 single-list-right">

        <div class="title-header">
            <div class="min-menu row">
                <div class="col-md-12">
                    <div class="title font-weight-normal">{{ $t('marketplace.video.related_videos') }}</div>
                </div>
            </div>
        </div>

        <div class="single-grid-right row  [ animated fadeIn ]" id="relatedvid">
            <div class="single-right-grids col-md-12" v-for="video in videos">
                <div class="col-md-5 single-right-grid-left ">
                    <div class="img-parent">
                        <a :href="'/marketplace/' + video.type + '/' + video.ivx_id" class="img-container">
                            <img :src="'/serve/image/' + video.property_id +'/' + video.type + '/' + video.ivx_id + '/' + video.updated_at + '?width=290'" :alt="video.title[0]" />
                        </a>
                    </div>
                    <div class="time"><p>{{ video.entry_duration | convertTime }}</p></div>
                </div>
                <div class="col-md-7 single-right-grid-right">
                    <div><a :href="'/marketplace/' + video.type + '/' + video.ivx_id" class="title">{{ video.title[0] }}</a></div>
                    <span class="info-small">{{ video.property_name[0] }}</span>
                </div>
            </div>
        </div>
        <div v-if="loaded === false" class="text-center animated flash infinite  loading-wrapper">{{ $t('marketplace.common.loading') }}...</div>

        <div class="load" v-if="showMore">
            <button class="button text-uppercase" id="loadMore" v-on:click="loadmore()">{{ $t('marketplace.common.show_more') }}</button>
        </div>

    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        name: "relatedvideos",
        data() {
            return {
                videos: [],
                loaded: true,
                showMore: true
            }
        },
        ready() {
            this.prepareComponent()
        },
        mounted() {
            this.prepareComponent()
        },
        methods: {
            prepareComponent() {
                this.getRelatedVideos({ keywords: keywords, limit: 7})
            },

            getRelatedVideos(payload) {
                this.loaded = false
                axios.get( '/marketplace/related-video', {
                    params: {
                        keywords: payload.keywords,
                        limit: payload.limit,
                        filter: 'video'
                    }
                }).then(response => {
                    this.videos = response.data.response.docs
                    this.loaded = true
                })
            },

            loadmore() {
                this.getRelatedVideos({ keywords: keywords, limit: 12})
                this.showMore = false
            }
        },

        filters: {
            convertTime(time) {
                return moment.unix(time).format('mm:ss')
            }
        }
    }
</script>

<style lang="scss" scoped>
    .loading-wrapper { margin-bottom: 13px;}
</style>
