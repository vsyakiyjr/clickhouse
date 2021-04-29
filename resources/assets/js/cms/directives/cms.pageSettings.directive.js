(function () {
	'use strict';

	angular.module('app.cms').directive('pageSettings', PageSettingsDirective);

	PageSettingsDirective.$inject = [];

	function PageSettingsDirective() {
		return {
			restrict: "EA",
			templateUrl: "/templates/admin/modules/cms/directives/pageSettings.html"
		}
	}
})();