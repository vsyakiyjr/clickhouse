
angular.module('ui.validationErrorMessage')
	.directive("uiValidationErrorMessage", UiValidationErrorMessage);

/**
 * Directive must be placed inside form (form directive).
 * Directive takes field data (validation errors, etc.) from formController of form directive
 *
 * Default messages can be overridden and/or extended using messages attribute.
 *
 * Usage:
 * <ui-validation-error-message field-name="passenger-{{passenger.index}}-last-name"
 * 								messages="{
 * 										'pattern':  'Допускаются только латинские буквы',
 * 										'required': 'Обязательное поле'
								}"
 * ></ui-validation-error-message>
 *
 *
 */

UiValidationErrorMessage.$inject = [];

function UiValidationErrorMessage() {
	return {
		restrict: 'EA',
		require: '^^form',
		replace: true,
		templateUrl: '/templates/shared/modules/ui/validationErrorMessage/message.html',
		scope:{
			fieldName: '@',
			hideOnPristine: '@?',
			validationMessages: '=*?messages'
		},
		link: function (scope, elem, attrs, formCtrl) {
			
			var hideOnPristine = Boolean(scope.$eval(scope.hideOnPristine));
			
			scope.defaultMessages = {
				'required': References.uiValidation.required,
				'pattern':  References.uiValidation.pattern,
				'email':    References.uiValidation.email
			};
			
			scope.messages = null;
			scope.class = '';
			
			scope.linkAction = function(message){
				if(!message || !angular.isFunction(message.link.callback)){
					return;
				}
				
				message.link.callback();
			};
			
			function hasErrors(){
				if(angular.isUndefined(formCtrl[scope.fieldName]) || formCtrl[scope.fieldName].$valid){
					return false;
				}
				
				if(angular.isDefined(formCtrl[scope.fieldName].$uiShowValidation) && formCtrl[scope.fieldName].$uiShowValidation === false){
					// Hide if need to show validation only on blur
					return false;
				}
				
				if(hideOnPristine && formCtrl[scope.fieldName].$pristine){
					return false;
				}
				
				return !angular.equals(formCtrl[scope.fieldName].$error, {});
			}
			
			// Build array of error messages for field with name = <fieldName>
			function buildMessages(){
				if(!hasErrors()){
					scope.messages = null;
					scope.class = '';
					return;
				}
				
				scope.messages = [];
				scope.class = '';
				var errors = formCtrl[scope.fieldName].$error;
				
				for(var type in errors){
					if(!errors.hasOwnProperty(type)){
						continue;
					}

					var typeMessages = getErrorMessages(type);
					
					if(typeMessages.length > 0){
						scope.class += ' has-' + type;
					}

					for(var i = 0; i < typeMessages.length; i++){
						if(!typeMessages[i]){
							continue;
						}

						var message = {
							'text': typeMessages[i],
							'link': null
						};

						// Last message of type and type has validation link
						if(
							(i == typeMessages.length - 1) &&
							formCtrl[scope.fieldName].$uiValidationLinks &&
							formCtrl[scope.fieldName].$uiValidationLinks[type]
						){
							message.link = formCtrl[scope.fieldName].$uiValidationLinks[type];
						}

						scope.messages.push(message);
					}

				}
				
				if(scope.messages.length == 0){
					scope.messages = null;
				}
			}
			
			// Get messages for error type. Both validation messages and custom
			function getErrorMessages(type){
				var messages = [];
				var validationMessages = getErrorMessage(type);
				var customMessages = getCustomValidationMessages(type);
				
				if(validationMessages){
					messages = messages.concat(validationMessages);
				}
				if(customMessages){
					messages = messages.concat(customMessages);
				}
				
				return messages;
			}
			
			// Get message for error type. Takes message from messages attribute or from defaultMessages
			function getErrorMessage(type){
				
				if(!angular.isString(type)){
					return ;
				}
				// Look for message in messages attribute
				if(angular.isDefined(scope.validationMessages) && angular.isDefined(scope.validationMessages[type])){
					return scope.validationMessages[type];
				}
				// Look for message in default messages
				if(angular.isDefined(scope.defaultMessages) && angular.isDefined(scope.defaultMessages[type])){
					return scope.defaultMessages[type];
				}
				return null;
			}
			
			// Get custom validation messages from field for error type.
			function getCustomValidationMessages(type){
				
				if(
					!formCtrl[scope.fieldName].$customValidationMessages ||
					!formCtrl[scope.fieldName].$customValidationMessages[type]
				){
					return null;
				}
				
				var messages = formCtrl[scope.fieldName].$customValidationMessages[type];
				
				if(angular.isString(messages) || angular.isArray(messages)){
					return messages;
				}
				
				return null;
			}
			
			// Remove rather large attribute for better DOM inspection
			elem.removeAttr('messages');
			

			scope.$watch(function(){
				return (formCtrl[scope.fieldName] || {}).$error
			}, function(newValue, oldValue){
				buildMessages();
			}, true);

			scope.$watch(function(){
				return (formCtrl[scope.fieldName] || {}).$uiValidationLinks
			}, function(newValue, oldValue){
				buildMessages();
			}, true);

			scope.$watch(function(){
				return (formCtrl[scope.fieldName] || {}).$uiShowValidation
			}, function(newValue, oldValue){
				buildMessages();
			}, true);
			
			
		}
	}
}

