angular.module('ui.simpleModal')
	.service("uiSimpleModal", SimpleModalService);

//		Usage:
//
//		uiSimpleModal(options).then(closeCallback, dismissCallback);
//
//      OPTIONS:
// 			title: 		   {string} title of modal window;
// 			titleIcon:     {string} class for icon span in title;
//			windowClass:   {string} class for modal window;
// 			backdrop:      {boolean} close modal by clicking backdrop. Default true
// 			size:		   {string} size of modal window. Accepted values: '', 'sm', 'lg';
// 			templateUrl:   {string} template's URL to load in modal window;
//			text:		   {string} text to show in modal window (if templateUrl is not defined);
//			html:		   {string} html to show in modal window (if templateUrl is not defined);
// 			data:		   {object} the object that will be passed to the template for displaying some data;
// 			returnData:	   {boolean} the data object will be returned;
// 			enterToSubmit: {boolean} Enter to submit simple form;
// 			buttons	:	   {array of string} array of buttons to display. Array like ['ok', 'cancel'] or ['yes', 'no'].
// 											 Will be used to build array of buttons.
// 			customButtons: {array of objects} array of button's objects. Will be used to display buttons. Overrides buttons.
// 											  Custom buttons can be created. Each button object is like:
// 												{
// 													'name':   'name_of_button',		// will be returned as result
// 													'text':   'Buttons text',		// text on button
// 													'class':  'btn-success',		// Class of button
// 													'cancel': false					// if true - button will dismiss modal
// 																					//    false - button will close modal
// 												}
// 			wrapTemplate: {boolean} 	true  - modal's content will be wrapped in wrap.html template
//										false - (only for templateUrl) content will be shown without wrapping
//
// 			showTitleClose:{booled}	true  - show close button in modal's header
// 									false - hide close button in modal's header
//
//
//		CALLBACKS:
//			closeCallback(button) - A callback executed when a modal window is closed.
// 									params:
//										button - {string} name of the pressed button
//
//			dismissCallback(button) - A callback executed when a modal window is dissmised.
// 									params:
//										button - {string} name of the pressed button ('cancel' by default)
//
//



SimpleModalService.$inject = ['$uibModal', 'Value'];

function SimpleModalService ($uibModal, Value) {

	return function(config){
		if(	angular.isUndefined(config) ||
			(angular.isUndefined(config.templateUrl) && angular.isUndefined(config.text) && angular.isUndefined(config.html))
		){
			console.error('uiSimpleModal: wrong config!');
			return;
		}

		/**
		 * Prepares array of button objects
		 *
		 * @param {array} buttons - array of button's names (e.g. ['yes', 'no', 'cancel'])
		 */
		function prepareButtons(buttons){
			// Default array
			if(!angular.isArray(buttons)){
				buttons = ['cancel'];
			}

			var resultButtons = [];

			// Build result array
			angular.forEach(buttons, function(item){
				switch (item){
					case 'ok':
						this.push({'name': 'ok',	 'text': References.buttons.ok,		'class': 'btn-success',	'cancel': false});
						break;
					case 'apply':
						this.push({'name': 'apply',	 'text': References.buttons.apply,  'class': 'btn-success','cancel': false});
						break;
					case 'save':
						this.push({'name': 'save',	 'text': References.buttons.save,   'class': 'btn-success','cancel': false});
						break;
					case 'cancel':
						this.push({'name': 'cancel', 'text': References.buttons.cancel,	'class': 'btn-danger',	'cancel': true});
						break;
					case 'close':
						this.push({'name': 'close',  'text': References.buttons.close,	'class': 'btn-danger',	'cancel': true});
						break;
					case 'yes':
						this.push({'name': 'yes',	 'text': References.buttons.yes,	'class': 'btn-success',	'cancel': false});
						break;
					case 'no':
						this.push({'name': 'no',	 'text': References.buttons.no,		'class': 'btn-danger',	'cancel': false});
						break;
					default :
						this.push({'name': item,	 'text': item,		'class': 'btn-default',	'cancel': false});
				}
			}, resultButtons);

			return resultButtons;
		}

		// check config values and set defaults
		var wrapTemplateUrl = '/templates/shared/modules/ui/simpleModal/wrap.html';

		var windowClass = Value.checkString(config.windowClass);
		var size = Value.checkInArray(config.size, '', ['', 'sm', 'lg', 'xlg']);
		var wrapTemplate = Value.check(config.wrapTemplate, true);
		var templateUrl = Value.check(config.templateUrl, false);

		var customButtons = Value.check(config.customButtons, false);
		var buttons = Value.check(config.buttons, ['cancel']);
		var backdrop = 'static';


		// Prepare config object
		var windowConfig = {};

		windowConfig.title		 = Value.check(config.title, '');
		windowConfig.data		 = Value.check(config.data, {});
		windowConfig.text		 = Value.check(config.text, false);
		windowConfig.html		 = Value.check(config.html, false);
		windowConfig.returnData	 = Value.check(config.returnData, false);
		windowConfig.templateUrl = Value.check(config.templateUrl, false);
		windowConfig.enterToSubmit = Value.check(config.enterToSubmit, false);

		windowConfig.config = {
			showTitleClose: Value.check(config.showTitleClose, true),
			titleIcon: Value.checkString(config.titleIcon)
		};

		// Use custom buttons or generate buttons from array
		if(customButtons){
			windowConfig.buttons = customButtons;
		}else{
			windowConfig.buttons = prepareButtons(buttons);
		}

		// Use wrap template if selected. For text - wrap template always
		if(wrapTemplate || !windowConfig.templateUrl){
			templateUrl = wrapTemplateUrl;
		}

		// Open modal window
		var modalInstance = $uibModal.open({
			animation: true,
			windowClass: windowClass,
			templateUrl: templateUrl,
			backdrop: backdrop,
			keyboard: false,
			controller: 'SimpleModalCtrl',
			size: size,
			resolve: {
				config: function () {
					return windowConfig;
				}
			}
		});
	

		// Return promise
		return modalInstance.result;

	};
}
