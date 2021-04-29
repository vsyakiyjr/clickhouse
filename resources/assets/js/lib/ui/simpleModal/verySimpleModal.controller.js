
angular.module('ui.simpleModal')
	.controller("VerySimpleModalCtrl", VerySimpleModalCtrl);

VerySimpleModalCtrl.$inject = ['$scope', '$uibModalInstance'];

function VerySimpleModalCtrl($scope, $uibModalInstance) {

	$scope.ok = function () {
		$uibModalInstance.close('ok');
	};

	$scope.cancel = function () {
		$uibModalInstance.dismiss('cancel');
	};

	$scope.$on('$stateChangeStart',	function(event, toState, toParams, fromState, fromParams){
		$scope.cancel();
	});
}

