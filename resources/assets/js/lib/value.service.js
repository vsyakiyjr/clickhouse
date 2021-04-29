/**
 * Service to check values by different rules
 */
angular.module('services.valueService', []);

angular.module('services.valueService')
	.service('Value', ValueService);

ValueService.$inject = [];

function ValueService() {
	return {
		check: function (value, defaultVal) {
			if (angular.isUndefined(value)) {
				return defaultVal;
			}

			return value;
		},
		checkString: function (value, defaultVal) {
			if (angular.isString(value)) {
				return value;
			}

			return angular.isString(defaultVal) ? defaultVal : '';
		},
		checkArray: function (value, defaultVal) {
			if (angular.isArray(value)) {
				return value;
			}

			return angular.isArray(defaultVal) ? defaultVal : [];
		},
		checkNumber: function (value, defaultVal, min, max) {

			// Если отсутсвует value - используем значение по умолчению
			if (angular.isUndefined(value)) {
				value = defaultVal;
			}

			// Если не задано значение по умолчанию или массив для проверки
			if (angular.isUndefined(value)) {
				return;
			}

			value = parseInt(value);
			defaultVal = parseInt(defaultVal);

			// Если не число
			if (isNaN(value)) {
				// Если значение по умолчанию не число
				if (isNaN(defaultVal)) {
					return;
				}
				value = defaultVal;
			}

			// Если значение выходит за рамки
			if ((angular.isDefined(min) && value < min) || (angular.isDefined(max) && value > max)) {
				// Если значение по умолчанию выходит за рамки
				if ((angular.isDefined(min) && defaultVal < min) || (angular.isDefined(max) && defaultVal > max)) {
					return;
				}
				value = defaultVal;
			}

			return value;
		},
		checkInArray: function (value, defaultVal, arr) {

			// Если отсутсвует value - используем значение по умолчению
			if (angular.isUndefined(value)) {
				value = defaultVal;
			}

			// Если не задано значение по умолчанию или массив для проверки
			if (angular.isUndefined(value) || !Array.isArray(arr)) {
				return;
			}

			// Если value нет в массиве
			if (arr.indexOf(value) == -1) {
				// Если при этом defaultVal нет в массиве
				if (arr.indexOf(defaultVal) == -1) {
					return;
				} else {	// если есть
					return defaultVal;
				}
			}

			return value;
		}
	}
}