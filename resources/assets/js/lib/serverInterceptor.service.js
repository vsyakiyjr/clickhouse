var phpdebugbar;
angular.module('services.serverInterceptor', ['ui.loading', 'ui.message']);

angular.module('services.serverInterceptor')
	.factory('ServerInterceptor', ServerInterceptor);

ServerInterceptor.$inject = ['Loading', 'Message', '$q', '$window', '$timeout', '$rootScope'];

function ServerInterceptor(Loading, Message, $q, $window, $timeout, $rootScope) {
	
	function getErrorText(code, defaultText){
		if(
			angular.isDefined(References) &&
			angular.isDefined(References.serverInterceptor) &&
			angular.isDefined(References.serverInterceptor.errors) &&
			angular.isDefined(References.serverInterceptor.errors[code])
		){
			return References.serverInterceptor.errors[code];
		}else{
			return defaultText;
		}
	}
	
	function handle_phpdebugbar_response(response) {
		if(angular.isUndefined(phpdebugbar) || angular.isUndefined(phpdebugbar.ajaxHandler)){
			return;
		}
		
		// We have a debugbar and an ajaxHandler
		// Dig through response to look for the
		// debugbar id.
		var headers = response && response.headers && response.headers();
		if (!headers) {
			return;
		}
		// Not very elegant, but this is how the debugbar.js defines the header.
		var headerName = phpdebugbar.ajaxHandler.headerName + '-id';
		var debugBarID = headers[headerName];
		if (debugBarID) {
			// A debugBarID was found! Now we just pass the
			// id to the debugbar to load the data
			phpdebugbar.loadDataSet(debugBarID, ('ajax'));
		}
	}
	
	function handleResponseError(response) {
		var error = '';

		var errorData = [];
		var errorCode;

		if(angular.isString(response.data)){
			// just string
			errorData = response.data;
		}else if(angular.isDefined(response.data) && angular.isDefined(response.data.error) && angular.isDefined(response.data.error.message)){
			// error object
			// todo: convert error message to site language using error code
			errorData = response.data.error.message;
			errorCode = response.data.error.code || '';
			
			if(response.data.error.code == 'FORM_VALIDATION_ERRORS'){
				sendSetFormServerValidation(response.data.error);
			}
		}else if(angular.isDefined(response.data) && angular.isDefined(response.data.errors) && angular.isDefined(response.data.message)){
			// object like:
			// {
			// 		"message": "The given data was invalid.",
			// 		"errors": {
			// 			"first_name": [
			// 				"The first_name field is required."
			// 			],
			// 			"last_name":[
			// 				"The last_name field is required.",
			// 				"The last_name field is very required."
			// 			]
			// 		}
			// }
			var message = response.data.message == 'The given data was invalid' ? 'Обнаружены следующие ошибки при сохранении:' : response.data.message;

			if(response.data.line && response.data.file) {
				message = `${message} on ${response.data.file} line ${response.data.line}`;
			}

			errorData = [message];
			
			angular.forEach(response.data.errors, function(value, key) {
				errorData = errorData.concat(value);
			});
		}else{
			// object like:
			// {
			// 		"contacts.phone": [
			// 			"validation.phone"
			// 		],
			// 		"contacts.phone7":[
			// 			"The contacts.phone7 field is required."
			// 		]
			// }
			angular.forEach(response.data, function(value, key) {
				errorData = errorData.concat(value);
			});
		}


		switch (response.status) {
			case 401:
				error = getErrorText('401', 'Пользователь не авторизирован.');
				break;
			case 403:
				error = getErrorText('403', 'Недостаточно прав.');
				break;
			case 404:
				error = getErrorText('404', 'Ресурс не найден.');
				break;
			case 408:
				error = getErrorText('408', 'Время ожидания истекло.');
				break;
			case 422:
				error = errorData;
				break;
			default: {
				error = response.data.message || getErrorText('default', 'Произошла ошибка.');
				break;
			}
		}

		return error;

	}

	function sendSetFormServerValidation(data){
		
		$rootScope.$broadcast('set-form-server-validation--' + data.formName, data);
		
	}
	
	return {
		response: function(response){
			Loading.hide();
			
			handle_phpdebugbar_response(response);

			return response.data || $q.when(response);
		},
		responseError: function (response) {
			var error = handleResponseError(response);

			Loading.hide();

			if (error && response.status !== 401) {

				try {
					if (response.data.error.code == 'SEARCH_NO_RESULTS') {
						Message.pushWarning(error);
					} else {
						Message.pushError(error);
					}
				} catch (e){
					Message.pushError(error);
				}
			}

			if(response.status == 401){
				$timeout(function(){
					$window.location.reload();
				}, 0);
			}

			//return response;
		}
	};


}