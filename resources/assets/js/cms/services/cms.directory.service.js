angular.module('app.cms').service('Directory', Directory);

Directory.$inject = ['$resource', 'ServerInterceptor'];

function Directory($resource, ServerInterceptor) {
	return $resource('/cms/directory/:id', {
		'id': "@id"
	}, {
		root: {
			url: '/cms/tree/root',
			method: 'GET',
			'interceptor': ServerInterceptor
		},
		get: {
			method: 'GET',
			'interceptor': ServerInterceptor
		},
		save: {
			method: 'PUT',
			'interceptor': ServerInterceptor
		},
		create: {
			method: 'POST',
			'interceptor': ServerInterceptor
		},
		find: {
			url: '/cms/directory/find',
			method: 'POST',
			'interceptor': ServerInterceptor
		}
	});
}