
angular.module('filters.formatDate', []);

angular.module('filters.formatDate').filter("formatDate", FormatDateFilter);


FormatDateFilter.$inject = [];

function FormatDateFilter() {
	return function (inputValue, dateFormat, lang) {

		if(!angular.isString(inputValue)){
			return null;
		}
		
		var locale = 'ru';
		var localeConvert = {'uk': 'ua'};
		
		if(References && References.currentLocale){
			locale = References.currentLocale;
		}

		lang = lang || locale;
		
		lang = localeConvert[lang] || lang;
		
		return Utilities.createDate(Utilities.parseDateString(inputValue), dateFormat, lang, false);
	}
}