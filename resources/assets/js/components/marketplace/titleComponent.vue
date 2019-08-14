<template>
	<div class="title-header">
        <div class="search-result text-center" v-if="playlistName">
                <span class="text-green font-weight-bold">{{ videos.numFound | formatNumber }}</span>
                {{ $t('marketplace.search.results_found_for_the_playlist') }}
                <span class="text-green font-weight-bold">“<span class="playlist__name">{{ playlistName }}</span>”</span>
        </div>
        <div class="search-result text-center" v-else-if="propertyName">
                <span class="text-green font-weight-bold">{{ videos.numFound | formatNumber }}</span>
                {{ $t('marketplace.search.results_found_in_property') }}
                <span class="text-green font-weight-bold capitalize">“{{ propertyName }}”</span>
        </div>
        <div class="search-result text-center" v-else-if="keywords !== '' && genre">
                <span class="text-green font-weight-bold">{{ videos.numFound | formatNumber }}</span>
                {{ $t('marketplace.search.results_found_for') }}
                <span class="text-green font-weight-bold capitalize">“{{ keywords }}”</span>
                {{ $t('marketplace.search.in_genre') }}
                <span class="text-green font-weight-bold capitalize">“{{ $t('marketplace.common.'+genre) }}”</span>
        </div>
        <div class="search-result text-center" v-else-if="genre && keywords === null || !keywords">
                <span class="text-green font-weight-bold">{{ videos.numFound | formatNumber }}</span>
                {{ $t('marketplace.search.results_found_for_the_genre') }}
                <span class="text-green font-weight-bold capitalize">“{{ $t('marketplace.common.'+genre) }}”</span>
        </div>
        <div class="search-result text-center" v-else>
                <span class="text-green font-weight-bold">{{ videos.numFound | formatNumber }}</span>
                {{ $t('marketplace.search.results_found_for') }}
                <span class="text-green font-weight-bold capitalize">“{{ keywords }}”</span>
        </div>
    </div>
</template>

<script>
import store from '../../store/marketplace'
import numeral from 'numeral'

export default {

    computed: {

    	videos() {
    		if (store.state.videos) {
    			return store.state.videos
    		} else {
    			return false
    		}
    	},

        keywords() { 
            const keywords = (store.state.keywords) ? store.state.keywords : ''
            return store.state.keywords
        },

        playlistName() {
    	    return store.state.playlist_name
        },

        propertyName() {
    	    return store.state.propertyName
        },

        genre() {
            const genre = (store.state.genre) ? store.state.genre : "all_genres"
            return genre
        }
    },

    filters: {
    	formatNumber(number) {
            return numeral(number).format('0,0')
        }
    }

}
</script>

<style lang="scss" scoped>
        .playlist__name { max-width: 200px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; vertical-align: top;
                display: inline-block;}
        .capitalize {
            text-transform: capitalize;
        }
</style>
