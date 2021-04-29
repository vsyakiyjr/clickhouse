(function () {
	'use strict';

	angular.module('app.cms').controller('newDirectoryDialogCtrl', NewDirectoryDialogController);

	NewDirectoryDialogController.$inject = ['$scope', 'Directory', '$uibModalInstance', 'ParentDirectory', "$timeout", '$rootScope', 'EditDirectory', 'Host'];

	function NewDirectoryDialogController($scope, Directory, $uibModalInstance, ParentDirectory, $timeout, $rootScope, EditDirectory, Host) {

		/**
		 * Create new directory
		 * @param newDirectory
		 */
		$scope.save = function (newDirectory) {
			var dir = new Directory(newDirectory);
			dir.$create(function () {
				$rootScope.$broadcast('flashMessage:show', {
					'class': 'success',
					'message': 'Директория ' + dir.fullpath + ' успешно создана'
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

		/**
		 * 'New directory' object is used in 'add directory' dialog
		 */
		$scope.resetNewDirectoryObject = function () {
			$scope.directory = {
				alias: '',
				description: '',
				fullpath: "",
				host: Host,
				parent_directory: $scope.parentDirectory.fullpath
			};
		};

		$scope.setFullPath = EditDirectory.setFullPath;
		$scope.transliterate = EditDirectory.transliterate;
		$scope.cancel = EditDirectory.cancel;
		$scope.checkAlias = EditDirectory.checkAlias;

		$timeout(function () {
			angular.element('input[name=alias]').focus();
		}, 0);

		$scope.parentDirectory = ParentDirectory;
		$scope.path = $scope.parentDirectory.fullpath;
		$scope.modalInstance = $uibModalInstance;
		$scope.titleText = "Добавить директорию в ";
		$scope.buttonText = 'Создать';
		$scope.alias = '';

		$scope.resetNewDirectoryObject();
	}
})();