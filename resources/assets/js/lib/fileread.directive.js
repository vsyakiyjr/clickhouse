(function () {
	'use strict';
	angular.module('directives.fileread', []);
	angular.module('directives.fileread').directive('fileread', FilereadDirective);

	function FilereadDirective() {
		return {
			scope: {
				fileread: "="
			},
			link: function (scope, element) {
				element.bind("change", function (changeEvent) {
					var reader = new FileReader();
					reader.onload = function (loadEvent) {
						scope.$apply(function () {
							scope.fileread = loadEvent.target.result;
						});
					};
					reader.readAsDataURL(changeEvent.target.files[0]);
				});
			}
		}
	}
})();