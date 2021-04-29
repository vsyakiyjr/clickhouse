
angular.module('ui.message')
	.directive("message", MessageDirective);

MessageDirective.$inject = ['$timeout'];

/**
 * 	Directive displays error/information message
 * 	To display message use
 * 		$rootScope.$broadcast("pushMessage", {
 * 			text: 'Message text',
 * 			type: 'error'
 * 		});
 * 	Type can be error|danger|success|message|warning|info
 *

 * 	To hide block use
 * 		$rootScope.$broadcast("hideMessage");
 *
 */
function MessageDirective($timeout) {
	return {
		restrict: 'E',
		replace: true,
		templateUrl: '/templates/shared/modules/ui/message/message.html',
		scope:{
			msgId: 	"@"
		},
		link: function (scope, elem, attrs, model) {

			if(angular.isUndefined(scope.msgId)){scope.msgId = '';}

			scope.timeout = false;
			scope.tblMessage = {
				message: '',
				type: '',
				active: false
			};

			scope.hideMessage = function(){
				if(scope.timeout){
					$timeout.cancel(scope.timeout);
				}

				scope.tblMessage.active = false;
			};

			scope.tblMessageClass = function(){
				var resultClass = '';

				switch(scope.tblMessage.type){
					case 'danger':
					case 'error':
						resultClass =  'alert-danger';
						break;
					case 'success':
					case 'message':
						resultClass =  'alert-success';
						break;
					case 'warning':
						resultClass =  'alert-warning';
						break;
					case 'info':
					default:
						resultClass =  'alert-info';
						break;
				}

				if(scope.msgId.length){
					resultClass = resultClass + ' msg-' + scope.msgId;
				}

				return resultClass;
			};


			scope.$on("pushMessage" + scope.msgId, function(event, data){

				if(scope.timeout){
					$timeout.cancel(scope.timeout);
				}

				if(angular.isUndefined(data.text))	 {data.text = '';}
				if(angular.isUndefined(data.type))	 {data.type = 'message';}
				if(angular.isUndefined(data.timeout)){data.timeout = 0;}

				scope.tblMessage = {
					'message': data.text,
					'type':    data.type,
					'active':  true,
					'array':   angular.isArray(data.text)
				};


				if(data.timeout > 0){
					scope.timeout = $timeout(
						function() {
							scope.hideMessage();
						},
						data.timeout
					);
				}

			});

			scope.$on("hideMessage" + scope.msgId, function(event, data){
				scope.hideMessage();
			});

			scope.$on("$stateChangeSuccess", function(event, toState, toParams, fromState, fromParams){
				scope.hideMessage();
			});
		}
	}
}