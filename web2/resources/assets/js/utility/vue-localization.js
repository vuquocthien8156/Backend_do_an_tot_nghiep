import VueInternationalization from 'vue-i18n';
import Locale from '../vue-i18n-locales.generated';

Vue.use(VueInternationalization);

const lang = document.documentElement.lang.substr(0, 2);

export default new VueInternationalization({
	locale: lang,
	fallbackLocale: 'en',
	messages: Locale,
	silentTranslationWarn: true,
});