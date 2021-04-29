
angular.module('ui.simpleModal')
	.controller("SimpleModalCtrl", SimpleModalCtrl);

SimpleModalCtrl.$inject = ['$scope', '$uibModalInstance', '$controller', 'config', 'hotkeys'];

function SimpleModalCtrl($scope, $uibModalInstance, $controller, config, hotkeys) {

	$scope.templateUrl	= config.templateUrl;	// Template's URL to load in modal window
	$scope.text			= config.text;			// Text to show in modal window (if templateUrl is not defined)
	$scope.html			= config.html;			// HTML to show in modal window (if templateUrl is not defined)
	$scope.title		= config.title;			// Title of modal window
	$scope.data			= config.data;			// Data to path to template
	$scope.config		= config.config;		// Configuration variables
	$scope.buttons		= config.buttons;		// Array of buttons. Each button is like
												// 		{
												// 			'name':   'name_of_button',		// will be returned as result
												// 			'text':   'Buttons text',		// text on button
												// 			'class':  'btn-success',		// Class of button
												// 			'cancel': false					// if true - button will dismiss modal
												// 		}
	// extend controller
	var parentCtrl = $controller('VerySimpleModalCtrl', {
		$scope: $scope,
		$uibModalInstance: $uibModalInstance
	});
	// Methods:
	// 		$scope.ok()
	// 		$scope.cancel()
	// Events:
	// 		$scope.$on('$stateChangeStart')

	$scope.uiSimpleModal = true;

	// Action on cancel of modal
	$scope.cancel = function (button) {
		var reason = 'cancel';

		if(angular.isDefined(button) && angular.isDefined(button.name)){
			reason = button.name;
		}

		$uibModalInstance.dismiss(reason);
	};

	if(config.enterToSubmit) {
		hotkeys.bindTo($scope)
			.add({
				combo: [
					'enter',
				],
				description: 'Okay',
				callback: function (event) {
					event.preventDefault();
					$scope.buttonClick($scope.buttons[0]);
				}
			});
	}

	// Process button click
	$scope.buttonClick = function(button){
		if(angular.isString(button)){
			button = {
				'name':   button,
				'cancel': false
			}
		}
		
		if(button.cancel){
			$scope.cancel(button);
		}else{
			var result = button.name;
			
			if(config.returnData == true){
				result = {
					'button': button.name,
					'data':   $scope.data
				}
			}
			
			$uibModalInstance.close(result);
		}
	};


}

