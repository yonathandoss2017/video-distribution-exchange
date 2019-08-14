<template>
  <div class="col-md-3 playlist-main">
    
      <section v-if="content.type_value === 'cp'">
            <div class="video-img main">
          <a :href="url + '/marketplace/cp-detail?propertyId=' + content.id"><img :src="content.logo ? content.logo : (url + '/images/property-logo-default.png')" :alt="content.name" :title="content.name" /></a>
        </div>
        <div class="info">
          <div class="video-title" style="text-align: center;"><a :href="url + '/marketplace/cp-detail?propertyId=' + content.id">{{ content.name }}</a></div>
        </div>
      </section>

      <section v-else>
        <div class="video-img main">
          <a :href="url + '/marketplace/search?genre=' + content.text"><img :src="content.image" :alt="content.text" :title="content.text" /></a>
        </div>
        <div class="info">
          <div class="video-title" style="text-align: center;"><a :href="url + '/marketplace/search?genre=' + content.text">{{ $t('marketplace.common.'+content.text) }}</a></div>
        </div>
      </section>

    </div><!-- .playlist-main -->
</template>

<script>
export default {
  props: {
    content: {},
  },
  data() {
    return {
      url: window.location.origin
    };
  },
};
</script>