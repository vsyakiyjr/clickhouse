angular.module('app.cms').service('Page', Page);

Page.$inject = ['$resource', 'ServerInterceptor'];

function Page($resource, ServerInterceptor) {
	function transformPageResponse(data) {
		var page = angular.fromJson(data);

		var prepareContent = function(content){
			content = content && content.block ? content : {block: []};
			content.block = angular.isArray(content.block) ? content.block : [content.block];

			return content;
		};

		page.content = prepareContent(page.content);

		if (page.sidebar){
			page.sidebar.content = prepareContent(page.sidebar.content);
		}

		return page;
	}

	return $resource(
		'/cms/page/:pageId',
		{
			pageId: '@id'
		},
		{
			get: {
				method: 'GET',
				transformResponse: transformPageResponse,
				'interceptor': ServerInterceptor
			},
			save: {
				method: 'PUT',
				transformResponse: transformPageResponse,
				'interceptor': ServerInterceptor
			},
			create: {
				'method': "POST",
				transformResponse: transformPageResponse,
				'interceptor': ServerInterceptor
			},
			find: {
				url: '/cms/page/find',
				method: 'POST',
				transformResponse: transformPageResponse,
				'interceptor': ServerInterceptor
			},
			getTranslation:{
				'url' :'/cms/page/get-translation',
				'method' : 'POST',
				transformResponse: transformPageResponse,
				'interceptor': ServerInterceptor
			},
			saveTranslation:{
				'url' :'/cms/page/save-translation',
				'method' : 'POST',
				'interceptor': ServerInterceptor
			},
			createPromo:{
				'url' :'/cms/page/create-promo',
				'method' : 'POST',
				'interceptor': ServerInterceptor
			},
            createSpecialOffer:{
                'url': '/cms/page/create-special-offer',
                'method': 'POST',
                'interceptor': ServerInterceptor
            },
            aircompaniesAutocomplete: {
                'url' :'/cms/aircompanies',
                'method' : 'POST',
                'interceptor': ServerInterceptor,
                'isArray': true,
            },
		}
	);
}
