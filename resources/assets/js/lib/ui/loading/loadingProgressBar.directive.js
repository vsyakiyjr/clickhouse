
angular.module('ui.loading')
	.directive("uiLoadingProgressBar", LoadingProgressBarDirective);

LoadingProgressBarDirective.$inject = [];


function LoadingProgressBarDirective() {
	return {
		restrict: 'E',
		replace: true,
		templateUrl: '/templates/shared/modules/ui/loading/loading-progress-bar.html',
		scope:{
			finished: 	"<?"
		},
		link: function (scope, elem, attrs, model) {
		
		
		}
	}
}