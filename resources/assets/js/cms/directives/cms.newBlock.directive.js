(function () {
	'use strict';

	angular.module('app.cms').directive('newBlock', NewBlockDirective);

	NewBlockDirective.$inject = ['CmsConfig'];

	function NewBlockDirective(CmsConfig) {
		return {
			restrict: "EA",
			transclude: true,
			replace: true,
			scope: {
				type: "@",
				addBlock: "="
			},
			templateUrl: "/templates/admin/modules/cms/directives/newBlock.html",
			link: function($scope){
				$scope.availableElements = CmsConfig[$scope.type + 'Elements'];
			}
		}
	}
})();