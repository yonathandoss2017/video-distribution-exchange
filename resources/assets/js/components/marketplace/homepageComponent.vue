<template>
	
	<div>

		<div class="container-fluid playlist-listing playlist-wrapper  [ animated fadeIn ]">

	        <div class="container">

	            <div class="row">
	                <div class="col-md-12">
	                    <div class="main-title">{{ $t('marketplace.index.block1_title') }}</div>
	                    <div class="title-description">{{ $t('marketplace.index.block1_sub_title') }}</div>
	                </div>
	            </div>

	            <div class="swipe-tab">

	                <ul class="nav nav-pills nav-stacked text-uppercase text-center" id="pills-tab" role="tablist">
	                    <li class="nav-item">
	                        <a class="nav-link" :class="{ active: state.loadCNCVideo }" id="cnc-tab" data-toggle="pill" href="#cnc-video" role="tab" aria-controls="cnc-video" aria-selected="true" @click="dispatch('setActiveTab','cncVideo')">
	                        	<img v-if="state.loadCNCVideo" src="/images/marketplace/cnc-circle-hover.png" alt="">
	                        	<img v-else src="/images/marketplace/cnc-circle.png" alt="">
                            <span>{{ $t('marketplace.index.cnc_video') }}</span>
                            <!-- <span class="mobile-3">{{ $t('marketplace.index.recently') }}</span> -->
	                        </a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link" :class="{ active: state.loadChinaVideo }" id="china-tab" data-toggle="pill" href="#china-video" role="tab" aria-controls="china-video" aria-selected="false" @click="dispatch('setActiveTab','chinaVideo')">
	                        	<img v-if="state.loadChinaVideo" src="/images/marketplace/china-circle-hover.png" alt="">
	                        	<img v-else src="/images/marketplace/china-circle.png" alt="">
                            <span>{{ $t('marketplace.index.china_video') }}</span>
                            <!-- <span class="mobile-3">{{ $t('marketplace.index.popular') }}</span> -->
	                        </a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link" :class="{ active: state.loadGlobal }" id="global-tab" data-toggle="pill" href="#global" role="tab" aria-controls="global" aria-selected="false" @click="dispatch('setActiveTab','global')">
	                        	<img v-if="state.loadGlobal " src="/images/marketplace/global-circle-hover.png" alt="">
	                        	<img v-else src="/images/marketplace/global-circle.png" alt="">
                            <span>{{ $t('marketplace.index.global_video') }}</span>
                            <!-- <span class="mobile-3">{{ $t('marketplace.index.all') }}</span> -->
	                        </a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link" :class="{ active: state.loadSelectionVideo }" id="selection-tab" data-toggle="pill" href="#selection-video" role="tab" aria-controls="selection-video" aria-selected="false" @click="dispatch('setActiveTab','selectionVideo')">
	                        	<img v-if="state.loadSelectionVideo " src="/images/marketplace/market-circle-hover.png" alt="">
	                        	<img v-else src="/images/marketplace/market-circle.png" alt="">
                            <span>{{ $t('marketplace.index.selection_video') }}</span>
                            <!-- <span class="mobile-3">{{ $t('marketplace.index.all') }}</span> -->
	                        </a>
	                    </li>
	                </ul>

	                <div class="tab-content" id="pills-tabContent">

	                    <div class="tab-pane fade" :class="[state.loadCNCVideo ? 'show active' : '']" id="cnc-video" role="tabpanel" aria-labelledby="cnc-tab">
	                        <load-videos playlist="1" cncVideo="1" :loadnow="state.loadCNCVideo" :key="1"></load-videos>
	                    </div><!-- .tab-pane -->

	                    <div class="tab-pane fade" :class="[state.loadChinaVideo ? 'show active' : '']" id="china-video" role="tabpanel" aria-labelledby="china-tab">
	                        <load-videos playlist="1" chinaVideo="1" :loadnow="state.loadChinaVideo" :key="2"></load-videos>
	                    </div><!-- .tab-pane -->

	                    <div class="tab-pane fade" :class="[state.loadGlobal ? 'show active' : '']" id="global" role="tabpanel" aria-labelledby="global-tab">
	                        <load-images playlist="1" global="1" :loadnow="state.loadGlobal" :key="4"></load-images>
	                    </div><!-- .tab-pane -->

	                    <div class="tab-pane fade" :class="[state.loadSelectionVideo ? 'show active' : '']" id="selection-video" role="tabpanel" aria-labelledby="selection-tab">
	                        <load-videos playlist="1" selectionVideo="1" :loadnow="state.loadSelectionVideo" :key="3"></load-videos>
	                    </div><!-- .tab-pane -->

	                </div><!-- .tab-content -->

	            </div><!-- .swipe-tab -->


	        </div><!-- .container -->

	    </div><!-- .playlist-wrapper -->

	    <!-- <div class="container-fluid playlist-listing video-wrapper">
	        <div class="container">
	            <div class="row">
	                <div class="col-md-12">
	                    <div class="main-title">{{ $t('marketplace.index.videos_released_for_you') }}</div>
	                    <div class="title-description">{{ $t('marketplace.index.all_carefully_featured_and_hand_picked_by_our_quality_assessmen') }}</div>
	                </div>
	            </div>
	            <div class="swipe-tab">
	                <ul class="nav nav-pills text-uppercase text-center" role="tablist">
	                    <li class="nav-item">
	                        <a class="nav-link active" id="featured-tab" data-toggle="pill" href="#featured" role="tab" aria-controls="featured" aria-selected="true" @click="loadTab('videoFeatured')">{{ $t('marketplace.index.featured') }}</a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link" id="just-in-tab" data-toggle="pill" href="#just-in" role="tab" aria-controls="just-in" aria-selected="false" @click="loadTab('videoJustIn')">{{ $t('marketplace.index.just_in') }}</a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link" id="all-videos-tab" data-toggle="pill" href="#all-videos" role="tab" aria-controls="all-videos" aria-selected="false" @click="loadTab('videoAll')">
	                            <span>{{ $t('marketplace.index.all_videos') }}</span>
	                            <span class="mobile-3">{{ $t('marketplace.index.all') }}</span>
	                        </a>
	                    </li>
	                </ul>
	                <div class="tab-content">
	                    <div class="tab-pane fade show active" id="featured" role="tabpanel" aria-labelledby="featured-tab">
	                        <load-videos chinaVideo="1" :loadnow="loadVideoFeatured" :key="4"></load-videos>
	                    </div>
	                    <div class="tab-pane fade" id="just-in" role="tabpanel" aria-labelledby="just-in-tab">
	                        <load-videos latest="1" :loadnow="loadVideoJustIn" :key="5"></load-videos>
	                    </div>
	                    <div class="tab-pane fade" id="all-videos" role="tabpanel" aria-labelledby="all-videos-tab">
	                        <load-videos random="1" :loadnow="loadVideoAll" :key="6"></load-videos>
	                    </div>
	                </div>
	            </div>

	        </div>
	    </div> --><!-- .video-wrapper -->

	    <div class="container-fluid playlist-listing video-wrapper">
	        <div class="container">
	            <div class="row">
	                <div class="col-md-12">
	                    <div class="main-title">{{ $t('marketplace.index.block2_title') }}</div>
	                    <div class="title-description">{{ $t('marketplace.index.block2_sub_title') }}</div>
	                </div>
	            </div>
	            <div class="swipe-tab">
	                <ul class="nav nav-pills text-uppercase text-center" role="tablist">
	                	<!-- <li class="nav-item">
	                        <a class="nav-link active" id="xinhua-tab" data-toggle="pill" href="#xinhua" role="tab" aria-controls="xinhua" aria-selected="true" @click="loadTab('xinhua')">新华专区</a>
	                    </li> -->
	                    <li class="nav-item">
	                        <a class="nav-link active" id="channels-tab" data-toggle="pill" href="#channels" role="tab" aria-controls="channels" aria-selected="true" @click="loadTab('channels')">{{ $t('marketplace.index.channels') }}</a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link" id="genres-tab" data-toggle="pill" href="#genres" role="tab" aria-controls="genres" aria-selected="false" @click="loadTab('genres')">{{ $t('marketplace.common.genres') }}</a>
	                    </li>
	                </ul>
	                <div class="tab-content">
	                		<!-- <div class="tab-pane fade show active" id="xinhua" role="tabpanel" aria-labelledby="xinhua-tab">
	                        <load-contents xinhua="1" :loadnow="loadXinhua" :key="1"></load-contents>
	                    </div> -->
	                    <div class="tab-pane fade show active" id="channels" role="tabpanel" aria-labelledby="channels-tab">
	                        <load-contents channels="1" :loadnow="loadChannels" :key="2"></load-contents>
	                    </div>
	                    <div class="tab-pane fade" id="genres" role="tabpanel" aria-labelledby="genres-tab">
	                        <load-contents genres="1" :loadnow="loadGenres" :key="3"></load-contents>
	                    </div>
	                </div>
	            </div>

	        </div>
	    </div><!-- .content-wrapper -->

	</div>

</template>

<script>

    export default {
    	props: {
		    genredata: {},
		  },
    	data() {
    		return {
    			loadVideoFeatured: 1,
    			loadVideoJustIn: 0,
    			loadVideoAll: 0,
    			loadChannels: 1,
    			loadGenres: 0,
    			loadXinhua: 0,
    		}
    	},
    	components: {
				'load-videos': require('./loadVideos').default,
				'load-contents': require('./loadContents').default,
				'load-images': require('./loadImages').default
			},
			created() {
				window.addEventListener("beforeunload",()=>{
	        sessionStorage.setItem("loacalState",JSON.stringify(this.$store.state))
	    	})
				this.$store.commit('getGenreData', this.genredata)
			},
			mounted() {
          document.getElementsByClassName('load-marketplace')[0].remove()
      },
      computed: {
        state() {
            return this.$store.state
        },
        dispatch() {
            return this.$store.dispatch
        },
      },
    	methods: {
	        loadTab( loadVar) {
	        	switch (loadVar) {
	        		case 'videoFeatured':
	        			this.loadVideoFeatured = 1
	        			break;
        			case 'videoJustIn':
	        			this.loadVideoJustIn = 1
	        			break;
        			case 'videoAll':
	        			this.loadVideoAll = 1
	        			break;
        			case 'channels':
	        			this.loadChannels = 1
	        			break;
        			case 'genres':
	        			this.loadGenres = 1
	        			break;
        			case 'xinhua':
	        			this.loadXinhua = 1
	        			break;
	        		default:
	        			break;
	        	}
	        }
	    }
    }

</script>

<style lang="scss" scoped>
	.playlist-wrapper .nav-item {
		height: auto;
	}
	.playlist-wrapper .nav-link {
	    display: flex;
    	flex-direction: column;
    	align-items: center;
    	height: auto !important;
	}
	.playlist-wrapper .nav-link img {
		width: 64px;
		margin-bottom: 10px;
	}
	.playlist-wrapper .nav-link {
		color: #333;
	}
	.playlist-wrapper .nav-link.active {
		color: #BA0334;
		background: none;
	}
	.playlist-wrapper .search-filter-result {
	    margin-top: 16px;
	}
</style>