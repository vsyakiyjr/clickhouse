
angular.module('ui.loading').directive("uiOnSubmitLoader", uiOnSubmitLoader);

uiOnSubmitLoader.$inject = ['Loading', '$timeout'];

function uiOnSubmitLoader(Loading, $timeout) {
	return {
		restrict: 'A',
		scope: {
			text: '@uiOnSubmitLoader',
			title: '@uiOnSubmitLoaderTitle',
		},
		link: function (scope, element, attrs){
			
			if(!scope.title){
				scope.title = 'Загрузка';
			}
		
			element.on('submit', function onSubmit(e){
				
				$timeout(function(){
					Loading.show(
						scope.title,
						scope.text
					);
				});
			});
		}
	}
}
