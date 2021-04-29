/**
 * @desc Display preview of text block. Preview length could be configured with cut-length attribute and is 300 chars as default.
 * @example <span textblock-preview="block" cut-length="150"></span>
 */
(function () {
	'use strict';

	angular.module('app.cms').directive('textblockPreview', textblockPreview);

	textblockPreview.$inject = ['$filter'];

	function textblockPreview($filter) {
		return {
			restrict: "EA",
			scope: {
				originBlock: "=textblockPreview",
				cutLength: "@?"
			},
			template: '{{renderedValue}}',
			link: function ($scope) {
				$scope.cutLength = $scope.cutLength ? parseInt($scope.cutLength) : 300;
				var textToCut;
				// var firstTextBlock = jsonPath($scope.originBlock.content, "$.block[?(@.type == 'text')]")[0];
				var firstTextBlock = '';

				if (!firstTextBlock) {
					textToCut = '';
				} else {
					textToCut = firstTextBlock.content;
				}

				$scope.renderedValue = $filter('cutText')(textToCut, $scope.cutLength)
			}
		}
	}
})();