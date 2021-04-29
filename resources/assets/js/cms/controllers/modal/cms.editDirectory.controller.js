(function () {
	'use strict';

	angular.module('app.cms').controller('editDirectoryCtrl', EditDirectoryController);

	EditDirectoryController.$inject = ['$scope', 'Directory', 'DirectoryId', '$uibModalInstance', "$timeout", '$rootScope', 'EditDirectory'];

	function EditDirectoryController($scope, Directory, DirectoryId, $uibModalInstance, $timeout, $rootScope, EditDirectory) {
		/**
		 * Save directory
		 */
		$scope.save = function () {
			$scope.directory.$save(function () {
				$rootScope.$broadcast('flashMessage:show', {
					'class': 'success',
					'message': 'Директория ' + $scope.directory.fullpath + ' успешно сохранена'
				});
				$rootScope.$broadcast("cms:refreshTree");
				$scope.cancel($scope.modalInstance);
			});
		};

		/**
		 * Update full path and check alias availability
		 * @param inputModel
		 * @param directory
		 */
		$scope.checkAliasOnBlur = function (inputModel, directory) {
			$scope.checkAlias(inputModel, directory, 'directory', function () {
				$scope.setFullPath(directory);
			});
		};

		$scope.setFullPath = EditDirectory.setFullPath;
		$scope.transliterate = EditDirectory.transliterate;
		$scope.cancel = EditDirectory.cancel;
		$scope.checkAlias = EditDirectory.checkAlias;

		$timeout(function () {
			angular.element('input[name=alias]').focus();
		}, 0);

		$scope.directory = Directory.get({'id': DirectoryId}, function (data) {
			$scope.alias = $scope.directory.alias = data.fullpath.replace(new RegExp("^" + data.parent_directory + '{1}\/{0,1}'), '');
			$scope.path = data.fullpath;
		});
		$scope.modalInstance = $uibModalInstance;

		$scope.titleText = "Редактирование директории";
		$scope.buttonText = 'Сохранить';

	}
})();