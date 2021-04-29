/* Russian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Andrew Stromnov (stromnov@gmail.com). */
$(function ($) {


	var checkOffset = $.datepicker._checkOffset;

	$.datepicker._checkOffsetOverload = $.datepicker._checkOffset;
	$.datepicker._checkOffset = function(inst, offset, isFixed) {
		//offset = checkOffset(inst, offset, isFixed);
		offset = $.datepicker._checkOffsetOverload.apply(this, arguments);

		var isModal = $('body').hasClass('modal-open');

		if(inst.settings.avUiDatepicker && !isModal) {

			var dpHeight = inst.dpDiv.outerHeight();
			var inputHeight = inst.input ? inst.input.outerHeight() : 0;
			var inputTop = inst.input ? inst.input.offset().top : 0;
			var inputBottom = inputTop + inputHeight;
			var dpBottom = inputBottom + dpHeight;

			var minViewPort = $(document).scrollTop();
			var maxViewPort = minViewPort + document.documentElement.clientHeight;

			var newScroll = minViewPort + (dpBottom - maxViewPort) + 15;

			if(dpHeight > document.documentElement.clientHeight){
				newScroll = inputTop;
			}

			offset.top = inputBottom;


			//if(dpBottom > maxViewPort && !inst.dpDiv.hasClass('ui-datepicker-dialog')){
			if(dpBottom > maxViewPort && !inst.settings.uiDatepickerDialog){
				$('html, body').animate({
					scrollTop: newScroll
				}, 300);
			}

		}

		// todo: maybe scroll to selected month
		if(inst.dpDiv.hasClass('modal-full-page')){
			inst.dpDiv.scrollTop(0);
		}

		return offset;
	};

	$.datepicker._updateDatepickerOverload = $.datepicker._updateDatepicker;
	$.datepicker._updateDatepicker = function(inst){
		var result = $.datepicker._updateDatepickerOverload.apply(this, arguments);

		if(inst && inst.settings && inst.settings.afterUpdateDatepicker && $.isFunction(inst.settings.afterUpdateDatepicker)){

			inst.settings.afterUpdateDatepicker.apply(this, [inst])
		}

		return result;
	};

	$.datepicker._generateHTMLOverload = $.datepicker._generateHTML;
	$.datepicker._generateHTML = function(inst){
		var html = $.datepicker._generateHTMLOverload.apply(this, arguments);


		var elem = jQuery('<container>' + html + '</container>');

		if(inst && inst.settings && inst.settings.onGenerateHTML && $.isFunction(inst.settings.onGenerateHTML)){
			inst.settings.onGenerateHTML.apply(this, [elem]);
		}


		inst.dpDiv.addClass(elem.attr('class'));

		html = elem.html();

		return html;
	};

	$.datepicker._generateMonthYearHeaderOverload = $.datepicker._generateMonthYearHeader;
	$.datepicker._generateMonthYearHeader = function(inst, drawMonth, drawYear, minDate, maxDate, secondary, monthNames, monthNamesShort){
		var html = $.datepicker._generateMonthYearHeaderOverload.apply(this, arguments);

		var elem = jQuery(html);

		if(inst && inst.settings && inst.settings.onGenerateMonthYearHeader && $.isFunction(inst.settings.onGenerateMonthYearHeader)){
			var argumentsArray = Array.prototype.slice.call(arguments);
			argumentsArray.unshift(elem);	// Add jQuery element (html) as first argument

			inst.settings.onGenerateMonthYearHeader.apply(this, argumentsArray);
		}

		html = elem.get(0).outerHTML;

		return html;
	};

	$.datepicker._attachHandlersOverload = $.datepicker._attachHandlers;
	$.datepicker._attachHandlers = function(inst){

		var result = $.datepicker._attachHandlersOverload.apply(this, arguments);


		if(inst && inst.settings && inst.settings.onAttachHandlers && $.isFunction(inst.settings.onAttachHandlers)){

			inst.settings.onAttachHandlers.apply(this, [inst])
		}

		return result;
	};
});

angular.module('ui.datepicker')
	.directive("uiDatePicker", DatePickerDirective);

DatePickerDirective.$inject = ["$timeout", "$parse", 'Value', '$filter', 'ApiServer'];

function DatePickerDirective($timeout, $parse, Value, $filter, ApiServer) {

	$(function ($) {
		$.datepicker.regional[References.currentLocale] = {
			closeText:          References.datepicker.closeText,
			prevText:           '',
			nextText:           '',
			currentText:        References.datepicker.currentText,
			monthNames:         References.date.months,
			monthNamesSelected: References.date.monthsSelect,
			monthNamesShort:    References.date.months,
			dayNames:           References.datepicker.dayNames,
			dayNamesShort:      References.datepicker.dayNamesShort,
			dayNamesMin:        References.datepicker.dayNamesMin,
			weekHeader:         References.datepicker.weekHeader,
			dateFormat:         References.datepicker.dateFormat,
			firstDay:           parseInt(References.datepicker.firstDay),
			isRTL:              false,
			showMonthAfterYear: false,
			changeMonth:        false,
			changeYear:         true,
			yearSuffix:         '',
			showButtonPanel:    true
		};
		$.datepicker.setDefaults($.datepicker.regional[References.currentLocale]);

	});

	return {
		restrict: "A",
		require: "ngModel",
		link: function (scope, elem, attrs, ngModelCtrl) {

			function evalDate(date){
				if(angular.isUndefined(date)){
					return;
				}
				// Распарсим дату
				var parsedDate = Utilities.createDate(date);
				// Если дата не получилась
				if(parsedDate === false){
					// Рспарсим в дату просчитанное выражение angular
					parsedDate = Utilities.createDate(scope.$eval(date));
					// Если и это не получилось
					if(parsedDate === false){
						// вернем undefined
						return;
					}
				}

				// вернем распарсенную дату
				return parsedDate;
			}

			function showInDialogFull(){
				return window.screen.width < 600 && noDialog == false;
			}

			var changeMonth = true;
			var nullable = scope.$eval(attrs.nullable);
			var isBirthDate = scope.$eval(attrs.isBirthDate);
			var noNeedReturnTicketButton = scope.$eval(attrs.noNeedReturnTicketButton);
			var noRangeHighlight = scope.$eval(attrs.noRangeHighlight);
			var datepickerType = attrs.datepickerType || 'air';

			var minDate = evalDate(attrs.minDate);
			var maxDate = evalDate(attrs.maxDate);

			var defaultNumOfMonth = scope.$eval(attrs.numOfMonth);
			if( ! (angular.isNumber(defaultNumOfMonth) && isFinite(defaultNumOfMonth)) ){
				defaultNumOfMonth = 2;	// количество месяцев по умолчанию
			}

			var fullPageNumOfMonth = scope.$eval(attrs.fullPageNumOfMonth);
			if( ! (angular.isNumber(fullPageNumOfMonth) && isFinite(fullPageNumOfMonth)) ){
				fullPageNumOfMonth = false;	// по умолчанию выключено
			}

			if(isBirthDate){
				defaultNumOfMonth = 1;
				changeMonth = true;
			}

			var minPrices;

			var yearRange = undefined;
			if(angular.isDefined(attrs.yearRange)){
				try{
					yearRange = scope.$eval(attrs.yearRange);
				} catch ( e ) {
					console.error('Error processing yearRange attribute!');
				}
			}

			var mainClass = 'classic';
			// var mainClass = 'with-prices';

			var slaveDatepicker = angular.isUndefined(attrs.slaveDatepicker) ? false : attrs.slaveDatepicker;
			var nameDatepicker  = angular.isUndefined(attrs.name)    		 ? false : attrs.name;
			var additionalClass;
			var closeDelay		= angular.isUndefined(attrs.closeDelay)		 ? false : parseInt(attrs.closeDelay);

			var noDialog = angular.isDefined(attrs.noDialog);	// Используется для предотвращения использования "диалога" на маленьких мобильных устройствах

			// используются для определения диапазона дат
			var masterDatepickerElement = $('input[ui-date-picker][slave-datepicker="' + nameDatepicker + '"]');
			var slaveDatepickerElement  = $('input[ui-date-picker][name="' + slaveDatepicker + '"]');

			// Используется для активации и изменения минимальной даты следующего элемента в цепочке
			//	let nextDatepickerElement  = $('input[date-picker][name="' + slaveDatepicker + '"]');
			var nextDatepickerElement  = slaveDatepickerElement;

			if(nextDatepickerElement.length == 0){
				nextDatepickerElement = false;
			}


			if(masterDatepickerElement.length > 0){
				// есть мастер элемент - значит сам элемент ведомый
				//console.info('есть мастер');
				slaveDatepickerElement = $(elem);
			}else{
				// нет мастер элемента. Возможно сам элемент и есть мастер
				//console.info('нет мастера');

				if(slaveDatepickerElement.length > 0){
					// есть ведомый - значит сам элемент мастер
					//console.info('  ---- есть ведомый');
					masterDatepickerElement = $(elem);
				}else{
					// иначе элемент отдельно стоящий
					//console.info('  ---- нет ведомого');
					slaveDatepickerElement  = false;
					masterDatepickerElement = false;
				}
			}

			function noNeedReturnTicketButtonHandler(){
				scope.$emit('frm-search-set-type-ow');
				elem.datepicker('hide');
			}

			function addSelectMonthYearHandler(inst, datepickerObj){

				var target = $("#" + inst.id);

				inst.dpDiv.find('.ui-datepicker-month-year').change(function(){

					//var selected = $(this).find(':selected');
					var groupIndex = $(this).parents('.ui-datepicker-group').index();

					var value = $(this).val();

					if(!value){return;}

					var data = value.split('-', 2);
					var month = parseInt(data[0]);
					var year  = parseInt(data[1]);

					if(!angular.isNumber(month) || !angular.isNumber(year)){
						return;
					}

					inst.selectedMonth = inst.drawMonth = month;
					inst.selectedYear  = inst.drawYear  = year;

					inst.settings.showCurrentAtPos = Math.max(groupIndex, 0);

					datepickerObj._notifyChange(inst);
					datepickerObj._adjustDate(target);

				});
			}


			function addSkyupLegend(div) {
				if(elem.attr('date-type') == 'to'){
					return;
				}

				var buttonpane = div.find('.ui-datepicker-buttonpane');

				if(buttonpane.length == 0){
					buttonpane = jQuery('<div/>', {
						class: 'ui-datepicker-buttonpane ui-widget-content'
					}).appendTo(div);

				}

				var skyupDirectFlightText = References.datepicker.directFlight;
				var skyupDirectFlight = jQuery(`<div class="skyup-legend"><span class="highlight"></span><span class="text"> - ${skyupDirectFlightText}</span> <img src="/aircompanies/PQ.png" alt="SkyUP logo" /></div>`)

				buttonpane.append(skyupDirectFlight);
			}

			function addButtonNoNeedReturnTicket(div){

				if(!noNeedReturnTicketButton){
					return;
				}

				var masterDate = masterDatepickerElement.datepicker('getDate');
				var slaveDate  = slaveDatepickerElement.datepicker('getDate');

				if(!masterDate){
					return;
				}

				var buttonpane = div.find('.ui-datepicker-buttonpane');
				var groupCount = div.find('.ui-datepicker-group').length;


				if(buttonpane.length == 0){
					buttonpane = jQuery('<div/>', {
						class: 'ui-datepicker-buttonpane ui-widget-content'
					}).appendTo(div);

				}

				var buttonText =  References.datepicker.noNeedReturnTicketButtonText; //Value.checkString(, 'Обратный билет не нужен');


				var noNeedReturnTicketButtonElement = jQuery('<span/>', {
					class: 'btn btn-primary btn-no-need-return-ticket',
					text: buttonText
				//	click: noNeedReturnTicketButtonHandler
				});

				if(groupCount == 2){
					noNeedReturnTicketButtonElement = jQuery('<div/>', {class: 'buttonpane-block'}).append(noNeedReturnTicketButtonElement);
				}

				//buttonpane.append(noNeedReturnTicketButtonElement);
				buttonpane.append(noNeedReturnTicketButtonElement);

				if (slaveDate) {
                    var timeDiff = Math.abs(slaveDate.getTime() - masterDate.getTime());
                    var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

                    // todo: make better checker/getter for values to work if References is empty

                    var textDaysReference = datepickerType == 'air' ? 'returnInDays' : 'returnZDInDays';

                    var textReturnInDays = References.datepicker[textDaysReference];
                    var daysPlural = References.days_plurals;
                    var text = References.datepicker[textDaysReference];

                    if (diffDays != 0) {
                        text = textReturnInDays.replace('%DAYS%', $filter('plural')(diffDays, daysPlural));
                    } else {
                        text = References.datepicker['returnSameDay'];
                    }
				} else {
				    text = '';
                }

				var returnInDaysElement = jQuery('<span/>', {
					class: 'ui-datepicker-dates-range-text',
					text: text
				});

				if(groupCount == 2){
					returnInDaysElement = jQuery('<div/>', {class: 'buttonpane-block'}).append(returnInDaysElement);
				}

				buttonpane.append(returnInDaysElement);
			}

			function subheaderAdder(div){

				if( !showInDialogFull() ){
					return;
				}

				var subHeaderText = angular.isUndefined(attrs.subHeader) ? null : attrs.subHeader;

				var defaultSubHeaderText =  Value.checkString(References.datepicker.defaultSubHeaderText, 'Выберите дату');

				if(!subHeaderText){
					if(fullPageNumOfMonth){
						subHeaderText = defaultSubHeaderText;
					}else{
						return;
					}
				}

				div.find('.ui-datepicker-subheader').remove();


				var subheaderElement = jQuery('<div/>', {
					class: 'ui-datepicker-subheader',
					text: subHeaderText
				});

				if(fullPageNumOfMonth){

					div.addClass('modal-full-page');
					subheaderElement.addClass('header');

					subheaderElement.append(jQuery('<a/>', {
						'class': 'close-button',
						'data-handler': 'hide',
						'data-event': 'click'
					}).append(
						jQuery('<span/>', {
							class: 'fa fa-times'
						})
					));

					div.append(subheaderElement);
				}else{

					var headerElement = div.find('.ui-datepicker-header');
					if(headerElement.length > 0){
						headerElement.after(subheaderElement);
					}

				}

			}

			function highLightSkyupDates() {
				var from = $('.segment-ow-rt .from-airport-block .airport-display .iata').text().replace('[', '').replace(']', '');
				var to   = $('.segment-ow-rt .to-airport-block .airport-display .iata').text().replace('[', '').replace(']', '');

				var elemType = elem.attr('date-type');
				var dateFrom = $('.ui-datepicker td[data-full-date]:first').data('full-date');
				var dateTo   = $('.ui-datepicker td[data-full-date]:last').data('full-date');

				var params = {
					from: from,
					to: to,
					date_from: dateFrom,
					date_to: dateTo,
				} ;

				if(elemType == 'to') {
					params.from = to;
					params.to = from;
				}

				ApiServer.schedule(params, function(dates){
					dates.forEach(function(date){
						$(".ui-datepicker td[data-full-date='" + date + "']").addClass('skyup')
					});

					if(dates && dates.length > 0) {
						$('.skyup-legend').show();
					} else {
						$('.skyup-legend').hide();
					}
				});
			}

			if(elem.attr('date-type')){
				$(document).on('click', '.ui-datepicker-next, .ui-datepicker-prev', function(){
					$timeout(function(){
						highLightSkyupDates();
					}, 50);
				});
			}

			var closeTimeoutPromise = false;
			var options;
			options = {
				avUiDatepicker: true,
				uiDatepickerDialog: false,
				numberOfMonths: defaultNumOfMonth,
				minDate: minDate,
				maxDate: maxDate,
				yearRange: yearRange,
				showButtonPanel: false,

				changeYear:  true,
				changeMonth: changeMonth,

				beforeShow: function(input, inst){

					var options = {
						numberOfMonths: defaultNumOfMonth,
						uiDatepickerDialog: false,
						changeYear: true,
						changeMonth: changeMonth,
						showCurrentAtPos: 0
					};

					noRangeHighlight = scope.$eval(attrs.noRangeHighlight);
					additionalClass = angular.isUndefined(attrs.addClass) ? '' : attrs.addClass;

					inst.dpDiv.addClass(additionalClass);
					inst.dpDiv.addClass(mainClass);

					var testElem = jQuery('<div/>', {'class': 'ui-datepicker ui-datepicker-multi ui-datepicker-multi-' + options.numberOfMonths}).appendTo('body');

					var defaultWidth = testElem.outerWidth();
					testElem.remove();

					var clientWidth = window.screen.width;

					//delete widthDiv;
					if(defaultWidth >= clientWidth && options.numberOfMonths > 1){
						// Показывается больше одного месяца и они не влязят на екран
						options.numberOfMonths = 1;
					}

					if(showInDialogFull()){
						if(fullPageNumOfMonth){

							// если маленькое мобильное устройство и включена опция - показываем "на весь экран"
							inst.dpDiv.addClass('ui-datepicker-dialog-full');
							options.numberOfMonths = fullPageNumOfMonth;
							options.showAnim  = 'fadeIn';
							options.duration  = 300;
							options.changeYear = false;
							options.changeMonth = false;

						}else{
							// если маленькое мобильное устройство - показываем "диалогом" (отображается в центре без привязки к инпуту)
							inst.dpDiv.addClass('ui-datepicker-dialog');

						}
						options.uiDatepickerDialog = true;
					}

					if(noNeedReturnTicketButton) {

						if(masterDatepickerElement) {
							var masterDate = masterDatepickerElement.datepicker('getDate');
							var currentDate = elem.datepicker('getDate');

							if(currentDate){	// current date is not null
								if(masterDate.getMonth() == currentDate.getMonth() && masterDate.getFullYear() == currentDate.getFullYear()){
									options.showCurrentAtPos = 0;
								}else{
									options.showCurrentAtPos = 1;
								}
							}

						}
					}

					// подсвечивать даты дейтпикера только
					if(elem.attr('date-type')){
						$timeout(function(){
							highLightSkyupDates();
						}, 50);
					}

					// возвращаем изменяемые настройки (количество месяцев)
					return options;
				//	return {numberOfMonths: numOfMonths, uiDatepickerDialog: uiDatepickerDialog};

				},
				onSelect: function (dateText, inst) {
					// if called from previous datepicker's onSelect "inst" will be undefined

					//console.log('onSelect', elem.attr('name'), dateText, inst, ngModelCtrl.$isEmpty(dateText) );

					if(dateText){

						$timeout(function(){
							ngModelCtrl.$setValidity('required', true);
						});
						scope.$apply(function (){
							ngModelCtrl.$setViewValue(dateText);
							ngModelCtrl.$validate();
						});
					}else{
						if(elem.is(":disabled") == false){
							$timeout(function(){
								ngModelCtrl.$setValidity('required', false);
							});
						}

						scope.$apply(function (){
							ngModelCtrl.$setViewValue(null);
							ngModelCtrl.$validate();
						});
					}

					// Если есть следующий элемент в цепочке
					if(nextDatepickerElement){
						if(elem.datepicker('getDate') >= nextDatepickerElement.datepicker('getDate')){
							nextDatepickerElement.datepicker('setDate', null);
						}

						// Установить ему минимальную дату
						nextDatepickerElement.datepicker("option", "minDate", dateText);
						// Необходимо вызвать для обновления модели следующего datepicker'а
						nextDatepickerElement.datepicker("option", "onSelect")(
							Utilities.createDate(nextDatepickerElement.datepicker('getDate'))
						);


						// если следующий datepicker не выключен - показать его
						if(nextDatepickerElement.is(":disabled") == false){
							$timeout(function(){
								nextDatepickerElement.datepicker("show");
							}, 100);
						}
					}

					// If close delay is specified and "onSelect" is called by user
					if(closeDelay && !isNaN(closeDelay) && inst){

						inst.settings.showCurrentAtPos = 0; // prevent jumping on slave datepicker
						inst.inline = true;	// to prevent close

						if(closeTimeoutPromise){
							$timeout.cancel(closeTimeoutPromise)
						}
						closeTimeoutPromise = $timeout(function(){
							elem.datepicker('hide');
							elem.blur();
						}, closeDelay);
					}

				},
				onClose : function (dateText, inst) {
					// Убрать дополнительный класс добавляемый к блоку datepicker
					inst.dpDiv.removeClass(additionalClass);
					inst.dpDiv.removeClass('ui-datepicker-dialog-full');
					inst.dpDiv.removeClass('ui-datepicker-dialog');
					inst.dpDiv.removeClass('modal-full-page');

					inst.inline = false;
					if(closeTimeoutPromise){
						$timeout.cancel(closeTimeoutPromise)
					}
				},
				afterUpdateDatepicker: function(inst){

				},
				onGenerateHTML: function(div){

					// Добавляем subheader (если нужно)
					subheaderAdder(div);

					// Добавляем кнопку "не нужен обратный билет"
					addButtonNoNeedReturnTicket(div);

					addSkyupLegend(div);
				},
				onGenerateMonthYearHeader: function(div, inst, drawMonth, drawYear, minDate, maxDate, secondary, monthNames, monthNamesShort){
					// div - header block (jQuery element)

					if(!scope.$eval(attrs.singleMonthYearSelect)){
						return;
					}

					if(inst.settings.changeMonth === false || inst.settings.changeYear === false){
						// if changing month or year is disabled
						return;
					}

					// if(secondary){
					// 	// Render secondary (without controls) headers "as is".
					// 	return;
					// }
					if(!minDate || !maxDate){
						// Render month-year control only for dates range.
						return;
					}



					var minYear = minDate.getFullYear();
					var maxYear = maxDate.getFullYear();
					var minMonth = minDate.getMonth();
					var maxMonth = maxDate.getMonth();

					// Clear previously generated elements from header
					div.empty();

					var select = jQuery('<select/>', {
						class: 'ui-datepicker-month-year'
					});

					for (var year = minYear; year <= maxYear; year++) {
						for (var month = 0; month < 12; month++) {
							if(
								(year === minYear && month < minMonth) ||	// month before min date
								(year === maxYear && month > minMonth)		// month after  max date
							){
								continue;
							}

							select.append(jQuery('<option/>', {
								text:  monthNames[month]+' '+year,
								value: month+'-'+year,
								selected: (year == drawYear && month == drawMonth)
							}));
						}
					}


					div.append(select);

				},
				onAttachHandlers: function(inst){
					inst.dpDiv.find('.btn-no-need-return-ticket').click(noNeedReturnTicketButtonHandler);

					var datepickerObj = this;		// like 'this' in _selectMonthYear method
					addSelectMonthYearHandler(inst, datepickerObj);

				}
			};

			// Если есть елементы начала и конца диапазона
			if(slaveDatepickerElement !== false && masterDatepickerElement !== false ){

				// Добавим callback для отображения дня
				options.beforeShowDay = function(date){
					var tdClass = '';
					var masterDate;
					var slaveDate;

					if(
						slaveDatepickerElement.is(":disabled")  == false &&
						masterDatepickerElement.is(":disabled") == false &&
						noRangeHighlight == false
					){
						// отображать диапазон только если ведомый элемент не выключен

						masterDate = masterDatepickerElement.datepicker('getDate');
						slaveDate  = slaveDatepickerElement.datepicker('getDate');

						if(angular.isDate(masterDate) && angular.isDate(slaveDate)){
							if(date >= masterDate && date <= slaveDate){
								// Дата внутри диапазона
								tdClass += 'ui-datepicker-day-in-range';
							}
						}
						if(angular.isDate(masterDate) && date.getTime() == masterDate.getTime()){
							// начальная дата диапазона
							tdClass += ' ui-datepicker-day-start-range';
						}
						if(angular.isDate(slaveDate) && date.getTime() == slaveDate.getTime()){
							// конечная дата диапазона
							tdClass += ' ui-datepicker-day-end-range';
						}
					}

					return [true, tdClass, ''];
				};
			}


			elem.datepicker(options);

			// fix to prevent soft keyboard on mobile devices
			elem.on('focus', function (event) {
				event.preventDefault();
				elem.blur();
			});


			// Cleanup on destroy, prevent memory leaking
			elem.on('$destroy', function () {
				elem.datepicker('hide');
				elem.datepicker('destroy');
			});

			function formatter(value){
				return Utilities.createDate(value, 'd.m.Y') || undefined;
			}

			function parser(value){
				return Utilities.createDate(value, 'd.m.Y') || undefined;
			}

			function validatorDatepicker(modelValue, viewValue){

				var value = modelValue || viewValue;

				var modelDateObj  = Utilities.createDate(value, undefined, undefined, true);
				var minDateObj    = Utilities.createDate(elem.datepicker('option', 'minDate'), undefined, undefined, true);
				var maxDateObj    = Utilities.createDate(elem.datepicker('option', 'maxDate'), undefined, undefined, true);

				var result = true;

				if( !angular.isDate(modelDateObj) ||
					( angular.isDate(minDateObj) && modelDateObj.getTime() < minDateObj.getTime() ) ||
					( angular.isDate(maxDateObj) && modelDateObj.getTime() > maxDateObj.getTime() )
				){
					result = false;
				}

				return result;
			}

			if(!nullable){
				ngModelCtrl.$formatters.push(formatter);
				ngModelCtrl.$parsers.push(parser);
				ngModelCtrl.$validators.datepicker = validatorDatepicker;
			}


			attrs.$observe('maxDate', function(val){
				var maxDate = evalDate(val);
				elem.datepicker('option', 'maxDate', maxDate);
				ngModelCtrl.$validate();
				//ngModelCtrl.$setDirty();	// To display invalid status of field
			});

			attrs.$observe('minDate', function(val){
				var minDate = evalDate(val);
				elem.datepicker('option', 'minDate', minDate);
				ngModelCtrl.$validate();
				//ngModelCtrl.$setDirty();	// To display invalid status of field
			});
		}
	}
}
