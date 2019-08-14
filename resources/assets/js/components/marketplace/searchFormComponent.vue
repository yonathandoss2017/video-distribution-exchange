<template>
    <div class="search-filter">
        <form v-on:submit.prevent="searchNow()" >
            <!-- search-field for PC -->
            <div class="search-field">
                <div class="input-group">
                    <input type="search" :placeholder="searchForMore" name="keywords" class="input-sm form-control" v-model="keywords">
                    <div class="input-group-btn">
                        <a class="dropdown-toggle genres" data-toggle="dropdown" href="#" aria-expanded="true"><span>{{ $t('marketplace.common.'+selectedGenre) }}</span><i class="fa fa-caret-down" aria-hidden="true"></i></a>
                        <ul class="dropdown-menu multi-column columns-2 genre-options">
                            <div class="row">
                                <div class="col-sm-4 no-padding-right" v-for="(genreGroup, key) in chunkGenres">
                                    <ul class="multi-column-dropdown no-padding-right" v-if="key == 0">
                                        <li v-for="genre in genreGroup"><a :id="genre.key" href="#">{{ $t('marketplace.common.'+genre.text) }}</a></li>
                                    </ul>
                                    <ul class="multi-column-dropdown no-padding" v-if="key == 1">
                                        <li v-for="genre in genreGroup"><a :id="genre.key" href="#">{{ $t('marketplace.common.'+genre.text) }}</a></li>
                                    </ul>
                                    <ul class="multi-column-dropdown" v-if="key == 2" style="margin-left: -15px">
                                        <li v-for="genre in genreGroup"><a :id="genre.key" href="#">{{ $t('marketplace.common.'+genre.text) }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </ul>
                    </div>
                    <div class="input-group-btn">
                        <a class="dropdown-toggle types" data-toggle="dropdown" href="#" aria-expanded="true"><span>{{ selectedFilter | capitalize }}</span><i class="fa fa-caret-down" aria-hidden="true"></i></a>
                        <ul class="dropdown-menu filter-options">
                            <li><a href="#">{{ $t('marketplace.search_form.all_types') }}</a></li>
                            <li><a href="#" title="playlist">{{ $t('marketplace.search_form.playlists_only') }}</a></li>
                            <li><a href="#" title="video">{{ $t('marketplace.search_form.videos_only') }}</a></li>
                        </ul>
                    </div>
                </div>
                <button type="submit" name="" class="btn btn-primary text-uppercase text-center">{{ $t('marketplace.common.search') }}</button>
            </div>

        </form>

        <form v-on:submit.prevent="searchNow()">
            <!-- search-field for mobile -->
            <div class="search-field mobile-3">
                <input type="search" :placeholder="searchForMore" name="keywords" class="input-sm form-control" v-model="keywords">
                <div class="row">
                    <div class="col-6 genres">
                        <select class="btn btn-normal text-capitalize" id="genre-select-option" v-model="selectedGenre">
                            <option value="all_genres">{{ $t('marketplace.search_form.all_genres') }}</option>
                            <option v-for="(genre, key) in formattedGenres" :value="key">{{ $t('marketplace.common.'+genre.text) }}</option>
                        </select>
                    </div>
                    <div class="col-6 all-types">
                        <select class="btn btn-normal text-capitalize" id="filter-select-option" v-model="filterValue">
                            <option value="">{{ $t('marketplace.search_form.all_types') }}</option>
                            <option value="playlist">{{ $t('marketplace.search_form.playlists_only') }}</option>
                            <option value="video">{{ $t('marketplace.search_form.videos_only') }}</option>
                        </select>
                    </div>
                </div>
                <input type="submit" name="" class="btn btn-primary text-uppercase text-center" value="search">
            </div>
        </form>
	</div>
</template>

<script>
import store from '../../store/marketplace'

export default {
    name: 'searchForm',

    props: ['genres'],

    data() {
        return {
            setGenre: '',
            setFilter: '',
            setKeywords: '',
            interval: 5000,
            typingTimer: 0,
            path: window.location.pathname,
            searchForMore: this.$i18n.t('marketplace.search_form.search_for_more')
        }
    },

    created() {

        // This is handle history back and forward button from browser
        window.addEventListener('popstate', () => {
            store.commit('getParameter')
            store.commit('setGenre', store.state.genre)
            store.commit('setFilter', store.state.filter)
            store.dispatch('getVideos')
        })

    },

    // Initial running in first time load
    mounted() {
        this.prepareComponent();
    },

    computed: {

        keywords: {
            get: function () {
                this.setKeywords = store.state.keywords
                return store.state.keywords
            },
            set: function(value) {
                store.commit('setKeywords', value)
            }
        },

        limit() {
            return store.state.limit
        },

		selectedGenre() {
			const localGenre = (store.state.genre) ? store.state.genre : 'all_genres'
            this.setGenre = (localGenre === 'all_genres') ? '' : localGenre
			return localGenre
		},

        selectedFilter() {
			let filter = ''
			switch (store.state.filter) {
				case 'video':
					filter = this.$i18n.t('marketplace.search_form.videos_only')
                    this.setFilter = 'video'
					break
				case 'playlist':
					filter = this.$i18n.t('marketplace.search_form.playlists_only')
                    this.setFilter = 'playlist'
					break
				default:
					filter = this.$i18n.t('marketplace.search_form.all_types')
			}

            return filter
        },

        filterValue() {
           return store.state.filter
        },

        formattedGenres() {
            return JSON.parse(this.genres);
        },

        chunkGenres() { 
            let genres = [{key:'', text: 'all_genres' }];
            _.each(this.formattedGenres, function (genre, key) {
                genre['key'] = key;
                genres.push(genre);
            });

            return _.chunk(genres, 6)
        }

    },
    methods: {
        prepareComponent() {
			
            // Remove placeholder and execute getVideos
            $('#searchform-holder').remove()
			
			if ( this.path.indexOf('search') !== -1 ) { 
                // initial execute
                store.commit('getParameter')
                store.commit('getVideos')
			}
			
			// Add event listerner to genre
			this.watchFilter()
        },
        
        doSearch(event) {
            if (event.code.indexOf('Key') > -1) {
                this.clearTime()
                this.typingTimer = setTimeout(this.executeSearch, this.interval)
            }
        },

        searchNow() {
            this.executeSearch()
        },

        // For clear the time interval of typing
        clearTime() {
            clearTimeout(this.typingTimer)
        },

        executeSearch() {
            if ( this.path.indexOf('search') !== -1 ) { 
                store.commit('detachPlaylistFilter')
                store.commit('resetPropertyName')
                store.commit('resetPropertyId')
                store.dispatch('setPage', 0)
                store.dispatch('getVideos')
                store.dispatch('getRelatedKeywords')
            } else {
                const goToUrl = 'marketplace/search?keywords=' + this.setKeywords + '&genre=' + this.setGenre + '&filter=' + this.setFilter

                window.location = goToUrl
            }
        },

        watchFilter() {

            const genres = document.querySelectorAll('.search-filter .genre-options li>a');

            for(var i=0;i<genres.length;i++){
			  	genres[i].addEventListener('click', (e) => {
					e.preventDefault()
                    store.commit('setGenre', e.target.id)
                },false)
			}
            
			const filters = document.querySelectorAll('.search-filter .filter-options li>a')

            for(var i=0;i<filters.length;i++){
			  	filters[i].addEventListener('click', (e) => {
					e.preventDefault()
					const text = e.target.title
                    store.commit('setFilter', text.toLowerCase())
				},false)
			}

            const mobileGenre = document.getElementById('genre-select-option')
            const mobileFilter = document.getElementById('filter-select-option')

            if (mobileGenre) {
                mobileGenre.addEventListener('change', (e) => {
                    const genre = (e.target.value === 'All Genres') ? '' : e.target.value
                    store.commit('setGenre', genre)
                })
            }

            if (mobileFilter) {
                mobileFilter.addEventListener('change', (e) => {
                    const filter = e.target.value
                    store.commit('setFilter', filter)
                })
            }

        }

    },

	filters: {
		capitalize: function (value) {
			if (!value) return ''
			value = value.toString()
			return value.charAt(0).toUpperCase() + value.slice(1)
		}
	}
}
</script>
