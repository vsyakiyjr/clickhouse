(function () {
	'use strict';

	angular.module('app.cms').directive('blockControls', BlockControlsDirective);

	BlockControlsDirective.$inject = [];

	function BlockControlsDirective() {
		return {
			restrict: "EA",
			replace: true,
			transclude: true,
			templateUrl: "/templates/admin/modules/cms/directives/blockControls.html"
		}
	}
})();