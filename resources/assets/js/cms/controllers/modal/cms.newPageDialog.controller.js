(function () {
	'use strict';

	angular.module('app.cms').controller('newPageDialogCtrl', NewPageDialogController);

	NewPageDialogController.$inject = ['$scope', 'Page', '$uibModalInstance', "$rootScope", 'ParentDirectory', '$timeout', 'CmsConfig', 'EditDirectory', 'Loading', 'Host'];

	function NewPageDialogController($scope, Page, $uibModalInstance, $rootScope, ParentDirectory, $timeout, CmsConfig, EditDirectory, Loading, Host) {
		/**
		 * Create page within current directory
		 * @param {object} newPage {title, description, alias}
		 * @param {string} selectedPreset Preset name for new page
		 */
		$scope.createPage = function (newPage, selectedPreset) {
			var page = new Page(newPage);

			page.content = {block: []};
			for (var blockIndex = 0; blockIndex < $scope.presets[selectedPreset]['blocks'].length; blockIndex++) {
				page.content.block.push(CmsConfig.pageElements[$scope.presets[selectedPreset]['blocks'][blockIndex]]);
			}
			
			Loading.show(
				'Сохранение страницы',
				'Подождите страница сохраняется...'
			);
			page.$create(function (savedPage) {
				$rootScope.$broadcast("cms:refreshTree");
				$rootScope.$broadcast('cms:editPage', {
					'page': savedPage
				});

				$scope.cancel($uibModalInstance);
			});
		};

		$scope.transliterate = EditDirectory.transliterate;
		$scope.cancel = EditDirectory.cancel;
		$scope.checkAlias = EditDirectory.checkAlias;

		/**
		 * Transliterate alias from title if it's empty
		 * @param {object} page {alias, title, description, ?content}
		 */
		$scope.generateAlias = function (page) {
			$scope.alias = page.alias = page.alias.length == 0 ? Utilities.transliterateFullString(page.title).replace('.','-') : page.alias;
		};

		$scope.checkAliasOnBlur = function (inputModel, page) {
			$scope.checkAlias(inputModel, page, 'page', function () {
				$scope.generateAlias(page);
			});
		};

		/**
		 * 'New page' object is used in 'add page' dialog
		 */
		$scope.resetNewPageObject = function () {
			$scope.newPage = {
				title: '',
				description: '',
				alias: '',
				enabled: false,
				host: Host,
				priority: 1,
				force_noindex: false,
				parent_directory_id: $scope.parentDirectory.id,
				parent_directory: $scope.parentDirectory.fullpath
			};
		};

		$scope.setSelectedPreset = function (newPreset) {
			if ($scope.presets.hasOwnProperty(newPreset)) {
				$scope.selectedPreset = newPreset
			}
		};

		$timeout(function () {
			angular.element('input[name=title]').focus();
		}, 0);

		$scope.selectedPreset = 'plainPage';
		$scope.parentDirectory = ParentDirectory;
		$scope.presets = CmsConfig.presets;
		$scope.modalInstance = $uibModalInstance;
		$scope.alias = '';

		$scope.resetNewPageObject();
	}
})();
