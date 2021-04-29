(function () {
	'use strict';

	angular.module('app.cms').controller('Cms', CmsController);

	CmsController.$inject = ['$scope', '$rootScope', 'hotkeys', 'Directory', 'Page', 'CmsConfig', 'EditDirectory', '$uibModal', 'Tree', '$timeout', 'Loading', '$location', 'uiSimpleModal'];

	function CmsController($scope, $rootScope, hotkeys, Directory, Page, CmsConfig, EditDirectory, $uibModal, Tree, $timeout, Loading, $location, uiSimpleModal) {
		/**
		 * Calculate page path to display
		 * @returns {string}
		 */
		$scope.calculateAlias = function () {
			let alias;

			if ($scope.page.parent_directory === "/") {
				alias = "/" + $scope.page.alias;
			} else {
				alias = $scope.page.parent_directory + "/" + $scope.page.alias;
			}

			let devHosts = [
				'rebuild.biletkin.com',
				'aviakassa-search-engine.local.test',
				'test.aviakassa.net',
			];

			let currentHost = window.location.hostname;
			let host = devHosts.indexOf(window.location.hostname) > -1 ? currentHost : $scope.host;

			return 'https://' +  host + alias;
		};


		/**
		 * Refresh site tree
		 */
		$scope.refreshTree = function () {
			$scope.loading = true;
			$scope.tree = undefined;

			Directory.root({host: $scope.host}, function(root){
				Directory.get(root,	function (result) {
					$scope.tree = result;
					$scope.loading = false;

					if(!$location.search().pageId){
						$location.search({host: $scope.host})
					}
				});
			})
		};

		/**
		 * Save page
		 */
		$scope.savePage = function () {

			Loading.show(
				'Сохранение страницы',
				'Подождите страница сохраняется...'
			);
			$scope.page.$save(function () {
				var alias = $scope.calculateAlias();
				$rootScope.$broadcast('flashMessage:show', {
					'class': 'success',
					'message': 'Страница ' + alias + ' успешно сохранена'
				});

				$scope.unwrapAircompaniesList();
			});
		};

		$scope.setActiveTab = function(tab){
			$scope.activeWrap = tab;

			if (tab == 'search') {
				$timeout(function(){
					angular.element('#cms-search').focus();
				}, 25)
			}
		};

		$scope.activeTabClass = function(tab){
			return $scope.activeWrap == tab ? 'active' : '';
		};

		//search timeout outside of function to be able cancel it
		var waitTimeout;

		$scope.searchWithinTree = function(query){
			if (query.length < 3) return;
			$scope.searchIsActive = false;

			var goSearch = function(){
				waitTimeout = $timeout(function(){
					if (query) {
						$scope.searchIsActive = true;
						Tree.search(query).then(function(data){
							$scope.searchResults = data;
							$scope.searchIsActive = false;
						});
					}
				}, 500);
			};

			if(waitTimeout) {
				$timeout.cancel(waitTimeout); // cancel previous request to prevent spamming
			}

			goSearch();
		};

		$scope.loadHistoryEntry = function($index){
			if ($scope.useDiff) {
				return $uibModal.open({
					size: 'lg',
					controller: 'pageDiffModalCtrl',
					templateUrl: '/templates/admin/modules/cms/directives/pageDiff.html',
					resolve: {
						PageObject: function(){
							return {
								actual:	$scope.originalPage,
								history: $scope.history[$index]
							}
						}
					}
				});
			}

			$scope.historyIndex = $index;

			if ($index == 'actual') {
				$scope.page = $scope.originalPage;

				return $scope.editMode = $index;
			}

			if (!$scope.history[$index]) {
				return;
			}

			$scope.editMode = 'history';
			$scope.page = $scope.history[$index];
		};


		$scope.hosts = ['ikeamania.by'];

		/**
		 * Executing phase shift
		 */
		hotkeys.bindTo($scope)
			.add({
				combo: [
					'ctrl+s',
					'command+s'
				],
				description: 'Save page',
				callback: function (event) {
					event.preventDefault();
					$scope.savePage($scope.page);
				}
			});

		/**
		 * Check is page with such alias exists
		 * @param inputModel
		 */
		$scope.checkAlias = EditDirectory.checkAlias;
		$scope.currentAircompany = {name: null};

		$scope.availablePageElements = CmsConfig.pageElements;

		$scope.$on('cms:refreshTree', function () {
			$scope.refreshTree();
		});

		$scope.openPage = function(id){
			$scope.page = Page.get({
				'pageId': id
			}, function (page) {
				$scope.alias = page.alias;
				$scope.history = page.history;

				$scope.originalPage = page;
				$scope.page.host = $scope.host;

                $scope.unwrapAircompaniesList();

				$scope.getFullPopularDirectionAirportObjects();
			});
		};

		$scope.$on('cms:editPage', function (event, data) {
			$location.search('pageId', data.page.id);

			$scope.openPage(data.page.id);
		});

		$scope.activeWrap = 'structure';

		$scope.useDiff = true;

		$scope.editMode = 'normal';
		$scope.historyIndex = 'actual';

		$scope.alias = '';
		$scope.page = false;

		var pageId = $location.search().pageId;
		$scope.host = $location.search().host || 'ikeamania.by';

		if (pageId) {
			$scope.openPage(pageId);
		}

		$scope.refreshTree();
	}
})();
