
angular.module('ui.loading')
	.directive("loading", LoadingDirective);

/**
 * 	Directive displays loading block
 * 	To display block use
 * 		$rootScope.$broadcast("showLoading", {
 * 			title: 'Loading',
 * 			text:  'Please wait.'
 * 		});
 * 	To hide block use
 * 		$rootScope.$broadcast("hideLoading");
 *
 */

LoadingDirective.$inject = ['$timeout'];

function LoadingDirective($timeout) {
	return {
		restrict: 'E',
		replace: true,
		templateUrl: '/templates/shared/modules/ui/loading/loading.html',
		scope:{
			loadingId: 	"@",
			title: 		"@",
			text: 		"@"
		},
		link: function (scope, elem, attrs, model) {
			// todo: set force hiding or by attribute or at show broadcast -- for fast hiding loadings on manager pages
			
			scope.loadingActive = false;
			scope.closing = false;
			
			var timeoutPromise;
			
			var defaultStringTitle = References.uiLoading.default.title ? References.uiLoading.default.title : 'Loading...';
			var defaultStringText  = References.uiLoading.default.text  ? References.uiLoading.default.text  : 'Подождите идет загрузка...';

			if(angular.isUndefined(scope.loadingId)){scope.loadingId = '';}

			scope.defaultTitle = angular.isDefined(scope.title) ? scope.title : defaultStringTitle;
			scope.defaultText  = angular.isDefined(scope.text)  ? scope.text  : defaultStringText;

			scope.$on("showLoading" + scope.loadingId, function(event, data){
				angular.element('body').addClass('loading-active');
				scope.closing = false;
				$timeout.cancel(timeoutPromise);

				scope.title = angular.isUndefined(data.title) ? scope.defaultTitle : data.title;
				scope.text  = angular.isUndefined(data.text)  ? scope.defaultText  : data.text;
				scope.loadingActive = true;

			});
			scope.$on("hideLoading" + scope.loadingId, function(event, forceHide){

				scope.closing = true;

				var hideLoading = function(){
					scope.loadingActive = false;
					angular.element('body').removeClass('loading-active');
				};

				if(forceHide){
					hideLoading();
				}else{
					timeoutPromise = $timeout(function(){
						hideLoading();
					}, 1500);
				}

			});
		}
	}
}