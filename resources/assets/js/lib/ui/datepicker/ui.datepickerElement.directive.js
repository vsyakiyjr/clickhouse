
angular.module('ui.datepicker')
	.directive("uiDatepickerElement", DatePickerElementDirective);

DatePickerElementDirective.$inject = ["$timeout", "$interval"];

function DatePickerElementDirective($timeout, $interval) {
	return {
		restrict: "E",
		transclude: true,
		replace: true,
		//scope: true,
		templateUrl: '/templates/shared/modules/ui/datepicker/date-picker-element.html',
		link: function (scope, elem, attrs) {


		},
		controller: function($scope, $element, $attrs, $transclude){

			$scope.click = function($event){
			 	angular.element($event.target).parent().find('input[ui-date-picker]').datepicker('show');
			};
			
			$scope.$watch(function(){return $scope.$eval($element.find('input[ui-date-picker]').attr('ng-model'));}, function(newValue, oldValue){
				if(!newValue || newValue == oldValue){
					return;
				}
				
				$element.find('.value').html(newValue);
			});
		}
	}
}