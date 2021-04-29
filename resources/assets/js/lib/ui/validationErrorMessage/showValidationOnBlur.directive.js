
angular.module('ui.validationErrorMessage')
	.directive("uiShowValidationOnBlur", UiShowValidationOnBlur);

/**
 * Directive set displaying validation messages only after field blurs.
 *
 */

UiShowValidationOnBlur.$inject = ['$timeout'];

function UiShowValidationOnBlur($timeout) {
	return {
		restrict: 'A',
		require: 'ngModel',
		scope: false,
		link: function (scope, element, attrs, ngModelCtrl) {
			
			// Use parser because $viewChangeListeners depends on validation
			ngModelCtrl.$parsers.push(function(value){
				ngModelCtrl.$uiShowValidation = false;
				
				return value;
			});
			
			element.on('blur', function(){
				
				// with timeout validation message will not blink
				$timeout(function(){
					ngModelCtrl.$uiShowValidation = true;
				}, 300);
				
				
			});
		}
	}
}

