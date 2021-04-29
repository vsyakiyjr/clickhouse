(function () {
	'use strict';

	angular.module('app.cms', [
		'ui.bootstrap',
		'cfp.hotkeys',
		'ngResource',
		'ngAnimate',
		'sticky',
		'ngCkeditor',
		'ngAnimate',
		'ngSanitize',
		'jsonFormatter',
		'diff-match-patch',
		'ui.simpleModal',
		'admin.templates',
		'directives.fileread'
	]);

	angular.module('app.templates', []);
	angular.module('admin.templates', []);

	angular.module('appCpanel', [
		'app.cms',
		'app.templates',
		'filters.formatDate',
		'services.valueService',
		'services.serverInterceptor'
	]);
})();

function clone(object){
	return JSON.parse(JSON.stringify(object))
}

function isEmptyObject(object) {
	return Object.getOwnPropertyNames(object).length === 0;
}