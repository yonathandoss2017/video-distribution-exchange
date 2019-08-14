<template>
	<div class="row search-filter-result">
        <div class="col-md-12">
            <div v-if="loaded === false" class="load-content">
                <div class="loader"></div>
                <div class="text-center animated flash infinite">{{ $t('marketplace.common.loading') }}...</div>
            </div>
            <div v-else-if="loaded === true" class="row animated fadeIn">
                <h4 class="text-center full-width" v-if="videos === false">{{ $t('marketplace.search.results_not_found') }}</h4>
        		<video-item
                    v-else
                    v-for="(value, key, index) in videos" 
                    :key="key" 
                    :index="index" 
                    :video="value"
                    ></video-item>
            </div>
        </div><!-- .container -->
	</div>
</template>


<script>
import store from '../../store/marketplace'
	
export default {
    props: {
        playlist: false,
        latest: false,
        mostpopular: false,
    },
	
    components: {
		'video-item': require('./videoComponent').default
	},
    
    computed: {
    	videos() {
            if ( !store.state.videos || store.state.videos.numFound === 0 ) {
                return false
            } else {
                return store.state.videos.docs
            }
    		 
    	},
        loaded() {
            return store.state.loaded
        }
    }

}
</script>

<style lang="scss" scoped>
    .full-width { width: 100%;}
</style>
