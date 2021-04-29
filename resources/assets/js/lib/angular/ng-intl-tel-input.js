angular.module('ngIntlTelInput', []);angular.module('ngIntlTelInput')
	.provider('ngIntlTelInput', function () {
		var me = this;
		var props = {};
		var setFn = function (obj) {
			if (typeof obj === 'object') {
				for (var key in obj) {
					props[key] = obj[key];
				}
			}
		};
		me.set = setFn;
		
		me.$get = ['$log', function ($log) {
			return Object.create(me, {
				init: {
					value: function (elm) {
						if (!window.intlTelInputUtils) {
							$log.warn('intlTelInputUtils is not defined. Formatting and validation will not work.');
						}
						elm.intlTelInput(props);
					}
				},
			});
		}];
	});
angular.module('ngIntlTelInput')
	.directive('ngIntlTelInput', ['ngIntlTelInput', '$log', '$window', '$parse', '$timeout',
		function (ngIntlTelInput, $log, $window, $parse, $timeout) {
			return {
				restrict: 'A',
				require: 'ngModel',
				link: function (scope, elm, attr, ctrl) {
					
					// https://www.npmjs.com/package/intl-tel-input
					
					var currentCountry = null;
					
					// Warning for bad directive usage.
					if ((!!attr.type && (attr.type !== 'text' && attr.type !== 'tel')) || elm[0].tagName !== 'INPUT') {
						$log.warn('ng-intl-tel-input can only be applied to a *text* or *tel* input');
						return;
					}
					// Override default country.
					if (attr.initialCountry) {
						ngIntlTelInput.set({initialCountry: attr.initialCountry});
					}
					ngIntlTelInput.set({
						nationalMode: true,
						preferredCountries: ['ua'],
						separateDialCode: true,
						utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/14.0.8/js/utils.js',
						//hiddenInput: 'huinja'
					});
					// Initialize.
					ngIntlTelInput.init(elm);
					// Set Selected Country Data.
					function setSelectedCountryData(model) {
						var getter = $parse(model);
						var setter = getter.assign;
						setter(scope, elm.intlTelInput('getSelectedCountryData'));
					}
					// Handle Country Changes.
					function handleCountryChange() {
						
						var selectedCountry = elm.intlTelInput('getSelectedCountryData');
						
						if(currentCountry == selectedCountry.iso2){
							return;
						}
						
						// console.log('handleCountryChange', elm.intlTelInput('getSelectedCountryData'), elm.intlTelInput('getNumber'));
						// console.log('viewValue', ctrl.$viewValue);
						// console.log('modelValue', ctrl.$modelValue);
						// console.log('$pending', ctrl.$pending);
						// console.warn('ctrl', ctrl);
						// console.log('intlTelInput', elm.intlTelInput('getNumber'));
						
						currentCountry = selectedCountry.iso2;
						
						if (attr.selectedCountry){
							setSelectedCountryData(attr.selectedCountry);
						}
						// scope.$apply(function(){
						// 	// let val = ctrl.$viewValue;
						// 	// ctrl.$setViewValue(null);
						// 	// ctrl.$setViewValue(val);
						// 	ctrl.$validate();
						// });
						
						// ctrl.$commitViewValue();
						// ctrl.$modelValue = ctrl.$viewValue;
						
						$timeout(function(){
							scope.$apply(function (){
								// let value = ctrl.$viewValue;
								// ctrl.$setViewValue(null);
								// ctrl.$setViewValue(value);
								
								// let value = elm.val();
								// ctrl.$setViewValue(null);
								// ctrl.$setViewValue(value);
								//
								
							//	ctrl.$render();
								ctrl.$setViewValue(elm.intlTelInput('getNumber'));
								
								//
								// ctrl.$rollbackViewValue();
								ctrl.$validate();
							});
						}, 10);
						
					}
					// Country Change cleanup.
					function cleanUp() {
						angular.element($window).off('countrychange', handleCountryChange);
					}
					angular.element($window).on('countrychange', handleCountryChange);
					scope.$on('$destroy', cleanUp);
					// Selected Country Data.
					if (attr.selectedCountry) {
						setSelectedCountryData(attr.selectedCountry);
					}
					// Validation.
					ctrl.$validators.ngIntlTelInput = function (value) {
						// console.info('$validators', value);
						// if phone number is deleted / empty do not run phone number validation
						if (value || elm[0].value.length > 0) {
							return elm.intlTelInput('isValidNumber');
						} else {
							return true;
						}
					};
					// Set model value to valid, formatted version.
					ctrl.$parsers.push(function (value) {
						// console.info('$parsers', value);
						return elm.intlTelInput('getNumber');
					});
					// Set input value to model value and trigger evaluation.
					ctrl.$formatters.push(function (value) {
						// console.info('$formatters', value);
						if (value) {
							if(value.charAt(0) !== '+') {
								value = '+' + value;
							}
							elm.intlTelInput('setNumber', value);
						}
						return value;
					});
				}
			};
		}]);
