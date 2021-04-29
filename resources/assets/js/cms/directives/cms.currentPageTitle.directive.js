(function(){
	'use strict';
	angular.module('app.cms').directive('currentPageTitle', CurrentPageTitleDirective);

	CurrentPageTitleDirective.$inject = [];

	function CurrentPageTitleDirective() {
		return {
			restrict: "EA",
			replace: true,
			templateUrl: "/templates/admin/modules/cms/directives/currentPageTitle.html"
		}
	}
})();