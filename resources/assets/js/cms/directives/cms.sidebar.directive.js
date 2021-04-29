angular.module('app.cms').directive('sidebar', SidebarDirective);

SidebarDirective.$inject = ['CmsConfig', 'Page'];

function SidebarDirective(CmsConfig, Page) {
	return {
		restrict:   "EA",
		scope: {
			pageId: "="
		},
		templateUrl: "/templates/admin/modules/cms/sidebar.html",
		link:        function ($scope) {
			$scope.availableSidebarElements = CmsConfig.sidebarElements;
			$scope.page = new Page($scope.pageId);

			$scope.addBlock = function(blockType){
				if (typeof $scope.availableSidebarElements[blockType] === "undefined") return;

				var newBlock = clone($scope.availableSidebarElements[blockType]);
				$scope.page.sidebar.content.block = Array.isArray($scope.page.sidebar.content.block) ? $scope.page.sidebar.content.block : [];
				$scope.page.sidebar.content.block.push(newBlock);

			};

			$scope.save = function(){

			}
		}
	}
}