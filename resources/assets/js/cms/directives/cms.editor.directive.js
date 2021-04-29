angular.module('app.cms').directive('editor', EditorDirective);

EditorDirective.$inject = [];

function EditorDirective() {
	return {
		restrict: "EA",
		replace: true,
		templateUrl: "/templates/admin/modules/cms/directives/editor.html"
	}
}