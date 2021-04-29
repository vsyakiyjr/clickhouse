angular.module('ui.loading')
	.service("Loading", LoadingService);

LoadingService.$inject = ['$rootScope'];

function LoadingService ($rootScope) {

	return {
		show: function(title, text, loadingId){
			if(angular.isUndefined(loadingId)){loadingId = '';}
			$rootScope.$broadcast("showLoading" + loadingId, {title: title,	text: text});
		},
		hide: function(loadingId, force){
			if(angular.isUndefined(loadingId)){loadingId = '';}
			$rootScope.$broadcast("hideLoading" + loadingId, force);
		}
	};
}