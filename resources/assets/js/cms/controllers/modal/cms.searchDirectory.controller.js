angular.module('app.cms').controller('searchDirectoryModalCtrl', SearchDirectoryModalController);

SearchDirectoryModalController.$inject = ['$scope', '$uibModalInstance', 'Directory', 'EditDirectory', 'Host', '$timeout'];

function SearchDirectoryModalController($scope, $uibModalInstance, Directory, EditDirectory, Host, $timeout) {
	$scope.cancel = EditDirectory.cancel;
	$scope.modalInstance = $uibModalInstance;

	$scope.autocomplete = function (value) {
		return Directory.find({
			'query': value,
			'host': Host,
		}).$promise.then(function (data) {
			return data.data;
		})
	};

	$scope.selectDirectory = function (directory) {
		$scope.directory = directory;
	};

	$scope.ok = function () {
		$uibModalInstance.close($scope.directory);
	};

	$timeout(function () {
		angular.element('#directorySearchQuery').focus();
	}, 20)
}