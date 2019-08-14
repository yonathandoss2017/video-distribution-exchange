<template>
    
    <div class="row pagination-style">
        
        <div class="col-sm-5">
            <div class="dataTables_info">
                <span id="number-playlist">{{ start }}</span> to <span id="count-playlist">{{ limit }}</span> of <span id="total-result">{{ totalVideos }}</span>
            </div>
        </div>
    	<div class="col-md-7 filter-menu-pagination">
        
            <div class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination">
                    <li class="paginate_button previous disabled"><a href="#" v-on:click.prevent="goTo(0)"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>
                    <li class="paginate_button"><a href="#" v-on:click.prevent="goTo(page-1)"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>

                    <li class="paginate_button" v-for="index in totalPages" v-if="index-1 < page+4 && index-1 > page-5" v-bind:class="{ active : index === page+1}">
                        <a v-if="index-1 === page+3 && page <= 1">...</a>
                        <a v-else-if="index-1 === page-4 && page+1 === totalPages">...</a>
                        <a v-else-if="index-1 === page+2 && page > 1 && page !== totalPages">...</a>
                        <a v-else-if="index-1 === page-2 && page > 1 && page+1 !== totalPages">...</a>
                        <a href="#" v-on:click.prevent="goTo(index-1)" v-else-if="index-1 !== page+3 && index-1 !== page-4 && index-1 > page-3">{{ index }}</a>
                    </li> <!-- && index-1 >= page-2  || -->
                    
                    <li class="paginate_button"><a href="#" v-on:click.prevent="goTo(page+1)"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
                    <li class="paginate_button next" id="editable_next"><a href="#" v-on:click.prevent="goTo(totalPages-1)"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>
                </ul>
            </div><!--.paging_simple_numbers -->
                        
        </div>

    </div>
</template>

<script>
import store from '../../store/marketplace'
	
export default {

	data() {
		return {
		}
	},

	ready() {
    	this.prepareComponent();
    },

    mounted() {
    	this.prepareComponent();
    },

    computed: {
    	totalVideos() {
    		return store.state.totalVideos
    	},
    	start() {
    		return store.state.limit * parseInt(store.state.page) + 1
    	},
    	limit() { 
    		if (store.state.videos.docs) {
	    		return (store.state.limit * parseInt(store.state.page) + 1) + store.state.videos.docs.length
	    	} else {
	    		return store.state.limit * (parseInt(store.state.page) + 1)
	    	}
    		// return store.state.videos.length
    	},
    	totalPages() {
    		return Math.ceil( store.state.totalVideos / store.state.limit)
    	},
    	page() {
    		return parseInt(store.state.page)
    	}
    },

    methods: {
    	prepareComponent() {},

    	goTo(pageNum) {
    		store.dispatch('setPage', pageNum)
    		store.dispatch('getVideos')
    	}
    }

}
</script>