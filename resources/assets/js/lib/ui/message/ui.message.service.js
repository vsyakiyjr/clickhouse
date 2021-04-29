angular.module('ui.message')
	.service("Message", MessageService);

MessageService.$inject = ['$rootScope', '$timeout'];

function MessageService ($rootScope, $timeout) {

	function pushMessage(text, type, timeout, msgId){
		if(angular.isUndefined(msgId)){msgId = '';}
		$rootScope.$broadcast("pushMessage" + msgId, {text: text, type: type, timeout: timeout});
	}

	return {
		push: function(text, type, timeout, msgId){
			pushMessage(text, type, timeout, msgId);
		},
		hide: function(msgId){
			if(angular.isUndefined(msgId)){msgId = '';}
			$rootScope.$broadcast("hideMessage" + msgId);
		},
		pushError: function(text, timeout, msgId){
			pushMessage(text, 'error', timeout, msgId);
		},
		pushWarning: function(text, timeout, msgId){
			pushMessage(text, 'warning', timeout, msgId);
		},
		pushSuccess: function(text, timeout, msgId){
			pushMessage(text, 'success', timeout, msgId);
		},
		pushInfo: function(text, timeout, msgId){
			pushMessage(text, 'info', timeout, msgId);
		}
	};
}