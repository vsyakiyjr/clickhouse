
angular.module('ui.simpleModal')
	.directive("modalClose", ModalClose);

ModalClose.$inject = [];

function ModalClose() {
	return {
		restrict: 'E',
		replace: true,
		templateUrl: '/templates/shared/modules/ui/simpleModal/modal-close.html',
		scope: false,
		link: function (scope, elem, attrs, ctrl) {
			
			
		}
	}
}
