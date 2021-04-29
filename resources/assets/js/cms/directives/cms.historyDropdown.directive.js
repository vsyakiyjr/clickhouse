angular.module('app.cms').directive('historyDropdown', HistoryDropdownDirective);

HistoryDropdownDirective.$inject = [];

function HistoryDropdownDirective() {
	return {
		restrict: "EA",
		templateUrl: "/templates/admin/modules/cms/directives/historyDropdown.html",
		link: function () {

		}
	}
}