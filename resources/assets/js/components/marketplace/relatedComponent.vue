<template>
    <div class="relate-result" v-if="relateds.length > 0">
        <div class="relate-title">{{ $t('marketplace.search.related_result') }}:</div>
        <div class="content">
            <span v-for="(value, key, index) in relateds"><a href="#" v-on:click.prevent="reloadVideos(value)" class="text-green">{{ value }}</a><span v-if="key < 4">, </span> </span>
        </div>
    </div>
</template>

<script>
import store from '../../store/marketplace'

export default {
    name: "related-component",
    created() {
        window.onpopstate = function() {
            store.dispatch('getRelatedKeywords')
        }
    },
    ready() {
        this.prepareComponent()
    },
    mounted() {
        this.prepareComponent()
    },
    computed: {
        relateds: {
            get: function() {
                return store.state.related
            }
        },
    },
    methods: {
        prepareComponent() {
            store.commit('getRelatedKeywords')
        },


        reloadVideos(newKeywords) {
            store.dispatch('setKeywords', newKeywords)
            store.commit('getVideos')
            store.commit('getRelatedKeywords')
        }
    }
}
</script>

