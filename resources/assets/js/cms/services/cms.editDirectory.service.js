angular.module("app.cms").service('EditDirectory', EditDirectoryService);

EditDirectoryService.$inject = ["$http", 'ServerInterceptor'];

function EditDirectoryService($http, ServerInterceptor) {

	return {
		/**
		 * Generate fullpath for directory
		 * @param newDirectory
		 */
		setFullPath: function (newDirectory) {
			var path;
			if (newDirectory.parent_directory === "/") {
				path = "/" + newDirectory.alias;
			} else {
				path = newDirectory.parent_directory + "/" + newDirectory.alias;
			}
			newDirectory.fullpath = newDirectory.alias.length > 0 ? path : newDirectory.fullpath;
		},

		/**
		 * Transliterate specific field
		 * @param object
		 * @param property
		 */
		transliterate: function (object, property) {
			if (object.hasOwnProperty(property)) {
				object[property] = Utilities.transliterateFullString(object[property]);
			}
		},

		/**
		 * Close given modal instance
		 * @param $uibModalInstance
		 */
		cancel: function ($uibModalInstance) {
			$uibModalInstance.dismiss('cancel');
		},

		/**
		 * Check for alias availability
		 * @param inputModel
		 * @param scopeModel
		 * @param objectType page or directory
		 * @param callback Executed on success
		 * @returns {boolean}
		 */
		checkAlias: function (inputModel, scopeModel, objectType, callback) {
			if (inputModel.$error.pattern) return false;

			var path,
				alias = inputModel.$modelValue;
			if (typeof alias === "undefined" || alias.length === 0 || alias === scopeModel.alias) {
				return false;
			}
			if (scopeModel.parent_directory == '/') {
				path = scopeModel.parent_directory + alias;
			} else {
				path = scopeModel.parent_directory + '/' + alias;
			}

			inputModel.$setValidity('alias', false);
			$http({
				method: "POST",
				url: "/cms/" + objectType + "/available",
				data: JSON.stringify({
					path: path
				})
			}).then(function (data) {
				var aliasIsValid = !data.available;
				inputModel.$setValidity('alias', aliasIsValid);
				scopeModel.alias = alias;

				if (typeof callback === "function") {
					callback();
				}
			}, ServerInterceptor.responseError);
		}
	}
}