
angular.module('ui.simplePagination')
	.directive("uiSimplePagination", UiSimplePagination);


UiSimplePagination.$inject = [];

function UiSimplePagination() {
	return {
		restrict: 'E',
		replace: true,
		templateUrl: '/templates/shared/modules/ui/simplePagination/pagination.html',
		scope: false,	// use parents scope
		link: function (scope, elem, attrs) {
		
		
		}
	}
}
