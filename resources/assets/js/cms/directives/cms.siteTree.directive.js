angular.module('app.cms').directive('siteTree', SiteTreeDirective);

SiteTreeDirective.$inject = ['RecursionHelper'];

SiteTreeDirectiveController.$inject = ['$scope', '$uibModal', 'Directory'];

function SiteTreeDirectiveController ($scope, $uibModal, Directory) {
	$scope.loadDirectoryContents = function(tree) {
		tree.collapsed = !tree.collapsed;
		if (!tree.pages) {
			$scope.loading = true;
			Directory.get({id: tree.id}, function(directory) {
				for (var prop in directory) {
					if (!directory.hasOwnProperty(prop)) {
						continue;
					}

					tree[prop] = directory[prop];
				}
				$scope.loading = false;
			})
		}
	};

	$scope.collapseTree = function(tree){
		if (tree && tree.parent_directory == '/') {
			tree.collapsed = true;
		}
	};


	$scope.toggleAddDirectoryDialog = function (parentDir) {
		$uibModal.open({
			animation: true,
			templateUrl: '/templates/admin/modules/cms/directives/newDirectoryDialog.html',
			controller: 'newDirectoryDialogCtrl',
			resolve: {
				'ParentDirectory': function () {
					return parentDir;
				},
				'Host': function () {
					return $scope.host
				}
			}
		});
	};

	$scope.toggleAddPageDialog = function (parentDir) {
		$uibModal.open({
			animation: true,
			templateUrl: '/templates/admin/modules/cms/directives/newPageDialog.html',
			controller: 'newPageDialogCtrl',
			resolve: {
				'ParentDirectory': function () {
					return parentDir;
				},
				'Host': function () {
					return $scope.host
				}
			}
		});
	};

	$scope.editDirectoryDialog = function (directory, $event) {
		if (directory.fullpath === "/") {
			return false;
		}

		$event.stopPropagation();
		$uibModal.open({
			animation: true,
			templateUrl: '/templates/admin/modules/cms/directives/newDirectoryDialog.html',
			controller: 'editDirectoryCtrl',
			resolve: {
				'DirectoryId': function () {
					return directory.id;
				}
			}
		});
	};

	$scope.loading = false;
}

function SiteTreeDirective(RecursionHelper) {
	return {
		restrict: "EA",
		scope: {
			tree: "=siteTree",
			host: "=",
			loading: "=?"
		},
		templateUrl: "/templates/admin/modules/cms/siteTree.html",
		compile: function (element) {
			return RecursionHelper.compile(element);
		},

		controller: SiteTreeDirectiveController
	}
}