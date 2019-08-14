import Vue from 'vue';
import VueInternationalization from 'vue-i18n';
import Locale from '../../vue-i18n-locales.generated';
import store from '../../store/marketplace';
require('../../bootstrap')

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

Vue.use(VueInternationalization);

const lang = document.documentElement.lang.substr(0, 2); 
// or however you determine your current app locale

const i18n = new VueInternationalization({
    locale: lang,
    messages: Locale
});

const app = new Vue({
	el: '#wrapper',
	i18n,
	store,
	components: {
		'search-form': require('./searchFormComponent.vue').default,
		'search-result': require('./searchResultComponent.vue').default,
		'title-result': require('./titleComponent.vue').default,
		'limit-filter': require('./searchFilterComponent.vue').default,
		'related-keywords': require('./relatedComponent').default,
		'pagination': require('./paginationComponent').default,
		'homepage': require('./homepageComponent').default
	}
});
