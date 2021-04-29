angular.module('app.cms').filter('boolean', BooleanFilter);

function BooleanFilter() {
	return function (input) {
		if (typeof input === "boolean") return input;

		if (typeof input === "string") {
			if (input.toLowerCase() === "true" || input === "1") {
				return true;
			} else if (input.toLowerCase() === "false" || input === "0") {
				return false;
			}
		}

		// if (typeof input === "object" && $.isEmptyObject(input)) return false;
		return input == true;
	}
}