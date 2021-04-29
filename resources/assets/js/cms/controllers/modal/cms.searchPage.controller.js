angular.module('app.cms').controller('searchPageModalCtrl', SearchPageModalController);

SearchPageModalController.$inject = ['$scope', '$uibModalInstance', 'Page', 'EditDirectory', '$timeout'];

function SearchPageModalController($scope, $uibModalInstance, Page, EditDirectory, $timeout) {
	$scope.cancel = EditDirectory.cancel;
	$scope.modalInstance = $uibModalInstance;

	$scope.autocomplete = function (value) {
		return Page.find({
			'query': value
		}).$promise.then(function (data) {
			return data.data;
		});
	};

	$scope.selectPage = function (page) {
		$scope.page = page;
	};

	$scope.ok = function () {
		$uibModalInstance.close($scope.page);
	};

	$timeout(function () {
		angular.element('#pageSearchQuery').focus();
	}, 20)
}