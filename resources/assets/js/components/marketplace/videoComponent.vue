<template>
	<div class="col-md-3 playlist-main">
		
			<section v-if="video.type === 'video'">
	         	<div class="video-img main">
					<a :href="url + '/marketplace/video/' + video.ivx_id"><img :src="'/serve/image/' + video.property_id + '/' + video.type + '/' + video.ivx_id + '/' + video.updated_at+ '?width=510'" :alt="video.title[0]" :title="video.title[0]" /></a>
	                <div class="time main"><p>{{ video.entry_duration | timeFormat }}</p></div>
				</div>
				<div class="info">
					<div class="video-title"><a :href="url + '/marketplace/video/' + video.ivx_id" :title="video.title[0]">{{ video.title[0] }}</a></div>
					<div class="small-detail"><a :href="url + '/marketplace/cp-detail?propertyId=' + video.property_id">{{ video.property_name[0] }}</a></div>
				</div>
			</section>
			<section v-else-if="video.type === 'playlist'">
		        <div class="video-img main">
					<a :href="url + '/marketplace/playlist/' + video.ivx_id"><img :src="'/serve/image/' + video.property_id + '/' + video.type + '/' + video.ivx_id + '/' + video.updated_at+ '?width=510'" :alt="video.title[0]" :title="video.title[0]" /></a>
		            <div class="playlist-detail">
		            	<div class="playlist-number"><span class="amount-numbers">{{video.videos_count}}</span><br>{{ $t('marketplace.common.videos') }}</div>
		            	<div class="playlist-icon"><img src="/images/marketplace/playlist.svg" alt=""></div>
		            </div>
				</div>
		        <label class="playlist-label">{{ $t('marketplace.common.playlist') }}</label>
				<div class="info">
					<div class="video-title"><a :href="url + '/marketplace/playlist/' + video.ivx_id" :title="video.title[0]">{{ video.title[0] }}</a></div>
					<div class="small-detail"><a :href="url + '/marketplace/cp-detail?propertyId=' + video.property_id">{{ video.property_name[0] }}</a></div>
				</div>
			</section>

    </div><!-- .playlist-main -->
</template>

<script>
import moment from "moment";

export default {
  props: {
    video: {}
  },
  data() {
    return {
      url: window.location.origin
    };
  },

  filters: {
    timeFormat(value) {
      var duration = moment.unix(value);
      return duration.format("mm:ss");
    }
  }
};
</script>

<style lang="scss" scoped>
	.playlist-wrapper .video-img .time {
		display: none;
	}
	.marketplace .playlist-wrapper .playlist-main .info {
		padding: 6px 9px 9px;
	}
</style>