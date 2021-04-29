(function () {
	'use strict';

	angular.module('app.cms').controller('pageDiffModalCtrl', PageDiffModalController);

	PageDiffModalController.$inject = ['$scope', '$uibModalInstance', '$controller', 'PageObject', 'CmsConfig'];

	function PageDiffModalController($scope, $uibModalInstance, $controller, PageObject, CmsConfig) {
		$controller('VerySimpleModalCtrl', {
			$scope:            $scope,
			$uibModalInstance: $uibModalInstance
		});

		$scope.options = {
			editCost: 4,
			attrs: {
				insert: {
					'data-attr': 'insert',
					'class': 'insertion'
				},
				delete: {
					'data-attr': 'delete'
				},
				equal: {
					'data-attr': 'equal'
				}
			}
		};

		$scope.diffAttrs = [
			{name: 'alias', label: 'Alias'},
			{name: 'title', label: 'Title'},
			{name: 'description', label: 'Description'},
			{name: 'keywords', label: 'Keywords'},
			{name: 'breadcrumbs_title', label: 'Название для хлебных крошек'}
		];

		$scope.trackedAttrs = [
			{name: 'enabled', label: 'Включена'},
			{name: 'priority', label: 'Приоритет в директории'},
			{name: 'image_path', label: 'Картинка'},
			{name: 'sitemap_priority', label: 'Приоритет в Sitemap'}
		];

		$scope.maxContentBlocks = new Array(
			Math.max(
				PageObject.history.content.block.length,
				PageObject.actual.content.block.length
			)
		);

		$scope.CmsConfig = CmsConfig;
		$scope.page = PageObject;
	}
})();