/**
 * @desc Get site tree from selected directory's id. Use 1 (System) to get whole site.
 * @example var Tree = Tree(1);
 */
angular.module('app.cms').service('Tree', Tree);

Tree.$inject = ['$http', '$rootScope', 'ServerInterceptor'];

function Tree($http, $rootScope, ServerInterceptor) {

	function ErrorHandler  (errorData, status) {

		ServerInterceptor.responseError(errorData);

		$rootScope.loading = false;
		if (status === 400) {
			window.location.reload();
		}
		// else {
		// 	$rootScope.errorText = errorData.error;
		// 	$rootScope.errorType = 'error';
		// }
	}

	return {
		'get': function (directoryId) {
			return $http.get("/cms/tree/" + directoryId)
				.then(function (result) {
					return result.data;
				}, ErrorHandler);
		},
		'search': function(searchQuery){
			return $http.post('/cms/tree/search', {
				'search_query': searchQuery
			}).then(function (result) {
				return result.data;
			}, ErrorHandler);
		}
	}
}
