
angular.module('ui.datepicker')
	.directive("uiDatepickerClicker", DatePickerClickerDirective);

DatePickerClickerDirective.$inject = [];

function DatePickerClickerDirective() {
	return {
		restrict: "A",
		link: function (scope, elem, attrs) {

			elem.click(function(){
				
				var element = jQuery(this);
				var input = element.find('input[ui-date-picker]');
				
				if(input.length == 0){
					input = element.siblings('input[ui-date-picker]');
				}
				
				if(input.length > 0){
					input.datepicker('show');
				}
				
			});
		
		}
	}
}