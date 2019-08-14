import Vue from 'vue'
import Vuex from 'vuex'
import { getURLParameter } from '../helper-library/get-url-parameter'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        videos: [],
        keywords: '',
        related: [],
        limit: 0,
        filter: '',
        mostpopular: 0,
        latestUpdate: 0,
        justIn: 0,
        totalVideos: 0,
        viewmore: 0,
        position: 0,
        page: 0,
        genre: '',
        loaded: true,
        playlist_id: null,
        playlist_name: null,
        propertyName: '',
        genreData:[],
        propertyId: null,
        loadCNCVideo: sessionStorage.getItem('loacalState') ? JSON.parse(sessionStorage.getItem('loacalState')).loadCNCVideo : false,
        loadChinaVideo: sessionStorage.getItem('loacalState') ? JSON.parse(sessionStorage.getItem('loacalState')).loadChinaVideo : false,
        loadSelectionVideo: sessionStorage.getItem('loacalState') ? JSON.parse(sessionStorage.getItem('loacalState')).loadSelectionVideo : false,
        loadGlobal: sessionStorage.getItem('loacalState') ? JSON.parse(sessionStorage.getItem('loacalState')).loadGlobal : true
    },
    mutations: {
    
        getVideos(state, payload) {

            const start = state.limit * state.page
            state.loaded = false
            const genre = (state.genre === 'all_genres') ? '' : state.genre

            if (state.keywords !== '') state.viewmore = 0

            axios.get('/marketplace/get-related', {
                params: {
                    keywords: state.keywords,
                    limit: state.limit,
                    start: start,
                    filter: state.filter,
                    genre: genre,
                    latestUpdate: state.latestUpdate,
                    justIn: state.justIn,
                    mostpopular: state.mostpopular,
                    playlist_id: state.playlist_id,
                    propertyName: state.propertyName,
                    propertyId: state.propertyId
                }
              }).then( response => {

                state.videos = response.data.response
                state.loaded = true

                if (state.videos !== null) {
                    state.totalVideos = state.videos.numFound
                    state.position = state.videos.start
                } else {
                    state.totalVideos = 0
                    state.position = 0
                }
            })
        },

        getRelatedKeywords(state) {
            if (state.keywords !== '') {
                axios.get('/marketplace/related-keywords', {
                    params: {
                        keywords: state.keywords,
                        limit: 5,
                        start: 0,
                        filter: state.filter
                    }
                }).then( response => { 
                    const relatedKeywords = response.data.facet_counts.facet_fields.keywords
                    let temporaryKeywords = [] 
                    state.related = []

                    relatedKeywords.forEach( function (loop) {
                        if (!isNumeric(loop)) {
                            temporaryKeywords.push(loop)
                        }
                    })

                    if (temporaryKeywords.length > 0) {
                        for (var i = 0; i < 5; i++) {
                            state.related.push(temporaryKeywords[Math.floor(Math.random()*temporaryKeywords.length)])
                        }
                    } 

                })
            } else {
                state.related = []
            }
        },

        getParameter(state) {
            state.keywords = ( getURLParameter('keywords') ? getURLParameter('keywords') : '')
            state.limit = ( getURLParameter('limit') ? getURLParameter('limit') : 20 )
            state.filter = ( getURLParameter('filter') ? getURLParameter('filter') : '')
            state.page = ( getURLParameter('page') ? getURLParameter('page') : 0)
            state.genre = ( getURLParameter('genre') ? getURLParameter('genre') : 'all_genres')
            state.latestUpdate = ( getURLParameter('latestUpdate') ? getURLParameter('latestUpdate') : '' )
            state.justIn = ( getURLParameter('justin') ? getURLParameter('justin') : 0)
            state.mostpopular = ( getURLParameter('mostpopular') ? getURLParameter('mostpopular') : 0)
            state.playlist_id = ( getURLParameter('playlist_id') ? getURLParameter('playlist_id') : null)
            state.playlist_name = ( getURLParameter('playlist_name') ? getURLParameter('playlist_name') : null)
            state.viewmore = ( getURLParameter('viewmore') ? getURLParameter('viewmore') : 0)
            state.propertyName = ( getURLParameter('propertyName') ? getURLParameter('propertyName') : '')
            state.propertyId = ( getURLParameter('propertyId') ? getURLParameter('propertyId') : '')
        },

        setKeywords(state, keywords) {
            state.keywords = keywords
        },

        setFilter(state, filter) {
            state.filter = filter
        },

        setLimit(state, limit) {
            state.limit = limit
        },

        setPage(state, page) {
            state.page = page
        },

        setLatest(state, latest) {
            state.justIn = latest
        },

        setMostPopular(state, mostpopular) {
            state.mostpopular = mostpopular
        },

        resetGenre(state) {
            state.genre = ''
        },

        setGenre(state, genre) {
            state.genre = genre
        },

        detachPlaylistFilter(state) {
            state.playlist_id = null;
        },

        resetPropertyName(state) {
            state.propertyName = ''
        },

        resetPropertyId(state) {
            state.propertyId = null
        },

        getGenreData(state, genreData) {
            state.genreData = genreData
        },

        pushUrl(state) {
            const localKeywords = (state.keywords !== undefined ? 'keywords=' + state.keywords : '')
            const localPage = (state.page !== undefined && state.page !== null ? '&page=' + state.page : '&page=' + 0)
            const localLimit = (state.limit !== undefined && state.limit !== null ? '&limit=' + state.limit : '')
            const localFilter = (state.filter !== undefined && state.filter !== null ? '&filter=' + state.filter : '')
            const localGenre = (state.genre !== undefined && state.genre !== null ? '&genre=' + state.genre : '')
            const localLatestUpdate = (state.latestUpdate !== undefined && state.latestUpdate !== null ? '&latestUpdate=' + state.latestUpdate : '')
            const localJustIn = (state.justIn !== undefined && state.justIn !== null ? '&justin=' + state.justIn : '')
            const localMostpopular = (state.mostpopular !== undefined && state.mostpopular !== null ? '&mostpopular=' + state.mostpopular : '')
            const localPlaylistId = (state.playlist_id !== undefined && state.playlist_id !== null ? '&playlist_id=' + state.playlist_id : '')
            const localPlaylistName = (state.playlist_name !== undefined && state.playlist_name !== null ? '&playlist_name=' + state.playlist_name : '')
            const localViewmore = (state.viewmore !== undefined && state.viewmore !== 0 ? '&viewmore=1' : '')
            const localPropertyName = (state.propertyName !== undefined ? '&propertyName=' + state.propertyName : '')
            const localPropertyId = (state.propertyId !== undefined && state.propertyId !== null ? '&propertyId=' + state.propertyId : '')

            const newUrl = localKeywords+
                localPage+
                localLimit+
                localGenre+
                localFilter+
                localLatestUpdate+
                localJustIn+
                localMostpopular+
                localPlaylistId+
                localPlaylistName+
                localViewmore+
                localPropertyName+
                localPropertyId

            setTimeout(function(){
                window.history.pushState("", "", "search?"+newUrl)
            }, 888)
        },

        setActiveTab (state, tabName) {
            switch (tabName) {
                case 'cncVideo':
                    state.loadCNCVideo = true
                    state.loadChinaVideo = false
                    state.loadSelectionVideo = false
                    state.loadGlobal = false
                    break;
                case 'chinaVideo':
                    state.loadChinaVideo = true
                    state.loadCNCVideo = false
                    state.loadSelectionVideo = false
                    state.loadGlobal = false
                    break;
                case 'selectionVideo':
                    state.loadSelectionVideo = true
                    state.loadCNCVideo = false
                    state.loadChinaVideo = false
                    state.loadGlobal = false
                    break;
                case 'global':
                    state.loadCNCVideo = false
                    state.loadChinaVideo = false
                    state.loadSelectionVideo = false
                    state.loadGlobal = true
                    break;
            }   
            sessionStorage.setItem('loacalState',JSON.stringify(state));
        }
    },
    actions: {
        getVideos({commit}) {
           commit('getVideos')
        },

        getRelatedKeywords({commit}) {
           commit('getRelatedKeywords')
        },

        setKeywords({commit}, keywords) {
            commit('setKeywords', keywords)
            commit('setPage', 0)
            commit('pushUrl')
        },

        setFilter({commit}, filter) {
            commit('setFilter', filter)
            commit('pushUrl')
        },

        setLimit({commit, state}, limit) {
            commit('setLimit', limit)
            commit('pushUrl')
        },

        setPage({commit}, page) {
            commit('setPage', page)
            commit('pushUrl')
        },

        resetGenre({commit}) {
            commit('resetGenre')
        },
        
        setGenre({commit}, genre) {
            commit('setGenre', genre)
            commit('pushUrl')
        },
        setActiveTab({commit}, tabName) {
            commit('setActiveTab', tabName)
        }
    }
})

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
