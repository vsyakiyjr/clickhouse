(function () {
	'use strict';

	angular.module('app.cms').directive('pageInDirectory', PageInDirectory);

	PageInDirectory.$inject = ['$rootScope'];

	function PageInDirectory($rootScope) {
		return {
			restrict: "EA",
			replace: true,
			scope: {
				page: "=",
				select: "=?"
			},
			templateUrl: "/templates/admin/modules/cms/directives/pageInDirectory.html",
			link: function($scope){
				$scope.isActivePage = function(id){
					return $rootScope.activePageId == id;
				};

				$scope.setCurrentPage = $scope.select || function (page) {
					$rootScope.activePageId = page.id;

					$rootScope.$broadcast('cms:editPage', {
						'page': page
					});
				};
			}
		}
	}
})();