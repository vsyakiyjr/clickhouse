angular.module('app.cms').directive("formInjection", FormInjectionDirective);

function FormInjectionDirective(){
	return {
		restrict: "EA",
		controller: 'formController',
		templateUrl: '/templates/app/states/form.html'
	}
}