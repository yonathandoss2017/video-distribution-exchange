<template>
    <div class="col-md-6 filter-by">

        <div class="pull-left view-by" v-if="viewmore === 0">
            <div class="filters-row_select">
                <select class="form-control sort-position" v-model="filter" v-on:change="reloadVideos()">
					<option v-for="view in views" v-bind:value="view.value">{{ $t('marketplace.common.view') }}: {{ view.text }}</option>
                </select>
            </div>
        </div><!-- .view-by -->

        <div class="pull-left show-page">
            <div class="filters-row_select">
                <select class="form-control sort-position" v-model="limit" v-on:change="reloadVideos()">
					<option v-for="limit in limits" v-bind:value="limit.value">{{ $t('marketplace.common.show') }}: {{ limit.text }}</option>
                </select>
              </div>
        </div><!-- .show-page -->

    </div><!-- .filter-by -->
</template>

<script>
import store from '../../store/marketplace'
	
export default {

	data() {
		return {
			views: [
				{
					value: '',
					text: 'All'
				},
				{
					value: 'playlist',
					text: this.$i18n.t('marketplace.search.playlists_only')
				},
				{
					value: 'video',
					text: this.$i18n.t('marketplace.search.videos_only')
				}
			],
			limits: [
				{
					value: 20,
					text: '20 ' + this.$i18n.t('marketplace.search.per_page')
				},
				{
					value: 40,
					text: '40 ' + this.$i18n.t('marketplace.search.per_page')
				},
				{
					value: 60,
					text: '60 ' + this.$i18n.t('marketplace.search.per_page')
				},
			]
		}
	},

    computed: {
    	filter: {
    		get: function() {
    			return store.state.filter
    		},
    		set: function(value) {
    			store.dispatch('setFilter', value)
                store.dispatch('setPage', 0)
    		}
    	},

    	limit: {
    		get: function() {
    			return store.state.limit
    		},
    		set: function(value) {
                const page = Math.ceil( store.state.totalVideos / value) - 1
                
                if (page < store.state.page) {
                    store.commit('setPage', page)
                }

    			store.dispatch('setLimit', value)
    		}
    	},

        viewmore() { console.log(store.state.viewmore)
            return store.state.viewmore
        }
    },

    methods: {
    	reloadVideos() {
    		store.commit('getVideos')
    	}
    }

}
</script>
