<template>
    <div class="col-md-8 single-video-bottom  [ animated fadeIn ]" id="otherPlaylistCt" v-if="show">
        <div class="divider w-100"></div>
        <div id="other-playlists" class="video-listing">
            <div class="title-header">
                <div class="min-menu row">
                    <div class="col-md-12">
                        <div class="title font-weight-normal">{{ $t('marketplace.video.other_playlists_with_the_same_video') }}</div>
                    </div>
                </div>
            </div>
            <div class="row search-filter-result" id="otherPlaylist">

                <div class="col-md-4 playlist-main" v-for="p in playlists">
                    <section>
                        <div class="video-img main img-parent">
                            <a :href="'/marketplace/playlist/' + p.id " class="img-container">
                                <img :src="'/serve/image/' + p.property_id + '/playlist/' + p.id + '/' + convertDate(p.updated_at) + '?width=510'" :alt="p.name" :title="p.name">
                            </a>
                            <div class="playlist-detail">
                                <div class="playlist-number">
                                    <span class="amount-numbers">{{ p.entries_count }}</span><br>{{ $t('marketplace.common.videos') }}
                                </div>
                                <div class="playlist-icon">
                                    <img src="/images/marketplace/playlist.svg" alt="">
                                </div>
                            </div>
                        </div>
                        <label class="playlist-label">{{ $t('marketplace.common.playlist') }}</label>
                        <div class="info">
                            <div class="video-title">
                                <a :href="'/marketplace/playlist/' + p.id" :title="p.name">{{ p.name }}</a>
                            </div>
                            <div class="small-detail">{{ p.content_provider.name }}</div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        name: "otherplaylists",
        data() {
            return {
                playlists: [],
                show: false
            }
        },
        ready() {
            this.prepareComponent()
        },

        mounted() {
            this.prepareComponent()
        },

        methods: {
            prepareComponent(){
                this.getPlaylists()
            },

            getPlaylists() {
                axios.get('/marketplace/get-playlists', {
                    params: {
                        playlist_id: playlist_id,
                        entries: entry_id
                    }
                }).then(response => {
                    this.playlists = response.data.dataPlaylist
                    this.show = (this.playlists.length > 0) ? true : false
                })
            },

            convertDate(time) {
                return moment(time).format('x')
            }

        }

    }
</script>

<style scoped>

</style>
