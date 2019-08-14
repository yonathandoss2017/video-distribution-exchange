<template>
	<div class="col-md-6 filter-menu-pagination" v-if="totalVideos !== 0">
    
        <div class="dataTables_paginate paging_simple_numbers">
            <ul class="pagination">
                <li class="paginate_button previous disabled" v-if="page !== 0"><a :href="'/marketplace/search?keywords=&page=0&limit=20&filter=playlist'"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>
                <li class="paginate_button" v-if="page !== 0"><a :href="'/marketplace/search?keywords=&page=' + (page) + '&limit=20&filter=playlist'"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>

                <li class="paginate_button" v-for="index in totalPages" v-if="index-1 < page+4 && index-1 > page-5" v-bind:class="{ active : index === page+1}">
                	<a v-if="index-1 === page+3 && page <= 1">...</a>
                    <a v-else-if="index-1 === page-4 && page+1 === totalPages">...</a>
                	<a v-else-if="index-1 === page+2 && page > 1 && page !== totalPages">...</a>
                    <a v-else-if="index-1 === page-2 && page > 1 && page+1 !== totalPages">...</a>
                	<a :href="'/marketplace/search?keywords=&page=' + (index-1) + '&limit=20&filter=playlist'" v-else-if="index-1 !== page+3 && index-1 !== page-4 && index-1 > page-3">{{ index }}</a>
                </li>
                
                <li class="paginate_button" v-if="page !== (totalPages-1)"><a :href="'/marketplace/search?keywords=&page=' + (page+1) + '&limit=20&filter=playlist'"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
                <li class="paginate_button next" id="editable_next" v-if="page !== (totalPages-1)"><a :href="'/marketplace/search?keywords=&page=' + (totalPages-1) + '&limit=20&filter=playlist'"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>
            </ul>
        </div><!--.paging_simple_numbers -->
                    
        <div class="dataTables_info float-right">
                <span id="number-playlist">{{ start }}</span> to <span id="count-playlist">{{ limit }}</span> of <span id="total-result">{{ totalVideos }}</span>
        </div>
    </div>
</template>

<script>
import store from '../../store/marketplace'
	
export default {
    computed: {
    	totalVideos() {
    		return store.state.totalVideos
    	},
    	start() {
            if (store.state.totalVideos === 0) {
                return 0
            } else {
        		return store.state.limit * parseInt(store.state.page) + 1
            }
    	},
    	limit() { 
    		if (store.state.totalVideos === 0 || store.state.videos === null) {
                return 0
            } else if (store.state.videos.docs) {
	    		return (store.state.limit * parseInt(store.state.page)) + store.state.videos.docs.length
	    	} else {
	    		return store.state.limit * (parseInt(store.state.page) + 1)
	    	}
    	},
    	totalPages() {
    		return Math.ceil( store.state.totalVideos / store.state.limit)
    	},
    	page() {
    		return parseInt(store.state.page)
    	}
    }

}
</script>