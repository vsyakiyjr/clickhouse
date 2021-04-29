angular.module('app.cms').directive('flashMessage', FlashMessageDirective);
FlashMessageDirective.$inject = ['$timeout', 'hotkeys'];

function FlashMessageDirective($timeout, hotkeys) {
	return {
		restrict: "EA",
		scope: {},
		templateUrl: "/templates/admin/modules/cms/directives/flashMessage.html",
		link: function ($scope) {
			$scope.$on('flashMessage:show', function (event, data) {
				$scope.class = data.class;
				$scope.message = data.message;
				$timeout(function () {
					$scope.closeFlashMessage()
				}, 6000);
			});

			hotkeys.bindTo($scope)
				.add({
					combo: 'esc',
					description: 'Close message',
					callback: function (event) {
						if ($scope.message) {
							event.stopImmediatePropagation();
							$scope.closeFlashMessage();
						}
					}
				}).add({
				combo: 'enter',
				description: 'Close message',
				callback: function (event) {
					if ($scope.message) {
						event.stopImmediatePropagation();
						$scope.closeFlashMessage();
					}
				}
			});

			$scope.closeFlashMessage = function () {
				$scope.message = '';
			};
		}
	}
}