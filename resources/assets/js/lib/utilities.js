/*==  assets/shared/js/lib/utilities.js  ======== test compilation ==== */
Utilities = {
	/**
	 * Перемещает элемент в массиве со старого индекса на новый
	 * http://stackoverflow.com/questions/5306680/move-an-array-element-from-one-array-position-to-another
	 * @param array Исходный массив
	 * @param old_index {int} Старый индекс
	 * @param new_index {int} Новый индекс
	 * @returns {Array} Копия изменного массива
	 */
	arrayMove: function(array, old_index, new_index){
		if (new_index >= this.length) {
			var k = new_index - this.length;
			while ((k--) + 1) {
				this.push(undefined);
			}
		}
		array.splice(new_index, 0, array.splice(old_index, 1)[0]);
		return array;
	},
	/**
	 * Returns array - result of intersect arr1 and arr2
	 *
	 * @param {Array}	arr1
	 * @param {Array}	arr2
	 *
	 * @returns {Array}
	 */
	arrayIntersect: function(arr1, arr2){
		var result = [];
		var map = {};
		var l1 = arr1.length;
		var l2 = arr2.length;
		var i = 0;
		
		for(i = 0; i < l2; i++){
			map[arr2[i]] = true;
		}
		
		for(i = 0; i < l1; i++){
			if(arr1[i] in map){
				result.push(arr1[i]);
			}
		}
		
		return result;
	},
	/**
	 * Вырезает теги из строки, если они есть
	 * https://github.com/kvz/phpjs/blob/master/functions/strings/strip_tags.js
	 * @param {string} input
	 * @param {string} allowed Список разрешенных тегов, например: '<i><b><p>'
	 * @returns {string}
	 */
	strip_tags: function strip_tags (input, allowed) {
		allowed = (((allowed || '') + '')
			.toLowerCase()
			.match(/<[a-z][a-z0-9]*>/g) || [])
			.join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
		var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
			commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
		return input.replace(commentsAndPhpTags, '')
			.replace(tags, function ($0, $1) {
				return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
			});
	},
	/**
	 * Очистка строки от мусора
	 * @param {string} valueToClean Строка для очистки
	 * @param {bool} isInt если true, то возвращаемое значение будет приведено к integer
	 * @returns {string | int}
	 */
	cleanese: function (valueToClean, isInt){
		isInt = typeof isInt === "undefined" ? false : isInt;
		valueToClean = ((typeof valueToClean === "undefined") || (valueToClean === null)) ? "" : valueToClean;
		var cleanedValue;
		if (isInt){
			cleanedValue = parseInt(this.strip_tags(valueToClean).trim());
		}else{
			cleanedValue = this.strip_tags(valueToClean).trim();
		}
		return cleanedValue;
	},
	monthes:{
		en:	{
			short:[
				"JAN",
				"FEB",
				"MAR",
				"APR",
				"MAY",
				"JUN",
				"JUL",
				"AUG",
				"SEP",
				"OCT",
				"NOV",
				"DEC"
			],
			full:[
				"January",
				"February",
				"March",
				"April",
				"May",
				"June",
				"July",
				"August",
				"September",
				"October",
				"November",
				"December"
			]
		},
		ru:{
			short:[
				"Янв",
				"Фев",
				"Мар",
				"Апр",
				"Май",
				"Июн",
				"Июл",
				"Авг",
				"Сен",
				"Окт",
				"Ноя",
				"Дек"
			],
			full:[
				"Января",
				"Февраля",
				"Марта",
				"Апреля",
				"Мая",
				"Июня",
				"Июля",
				"Августа",
				"Сентября",
				"Октября",
				"Ноября",
				"Декабря"
			]
		},
		ua:{
			short:[
				"Ciч",
				"Лют",
				"Бер",
				"Квi",
				"Тра",
				"Чер",
				"Лип",
				"Сер",
				"Вер",
				"Жов",
				"Лис",
				"Гру"
			],
			full:[
				"Сiчня",
				"Лютого",
				"Березня",
				"Квiтня",
				"Травня",
				"Червня",
				"Липня",
				"Серпня",
				"Вересня",
				"Жовтня",
				"Листопада",
				"Грудня"
			]
		}
	},
	days:{
		en:{
			short:[
				"Sun",
				"Mon",
				"Tue",
				"Wed",
				"Thu",
				"Fri",
				"Sat"
			],
			full:[
				"Sunday",
				"Monday",
				"Tuesday",
				"Wednesday",
				"Thursday",
				"Friday",
				"Saturday"
			]
		},
		ru:{
			short:[
				"Вс",
				"Пн",
				"Вт",
				"Ср",
				"Чт",
				"Пт",
				"Сб"
			],
			full:[
				"Воскресенье",
				"Понедельник",
				"Вторник",
				"Среда",
				"Четверг",
				"Пятница",
				"Суббота"
			]
		},
		ua:{
			short:[
				"Вс",
				"Пн",
				"Вт",
				"Ср",
				"Чт",
				"Пт",
				"Сб"
			],
			full:[
				"Недiля",
				"Понедiлок",
				"Вiвторок",
				"Середа",
				"Четвер",
				"П'ятница",
				"Субота"
			]
		}
	},
	/**
	 * Возвращает дату отностительно указанного смещения в указанном формате
	 * @param dateString {string}|{object} Смещение даты или объект Date. в случае передачи объекта Date будет возвращена дата в соотвествии с форматом.
	 * Формат смещения: ([0-9]*day|week|month)|(today|tomorrow|yesterday), например: 2day - на два дня вперед, yesterday - вчера.
	 * Регистр значения не имеет.
	 * При неправильно указанном смещении принимается значение today.
	 * При других ошибках возвращается пустая строка.
	 * @param [format] {string} Формат возвращаемой даты:
	 * H - часы в формате 24
	 * i - минуты
	 * d - день месяца, число от 1 до 31
	 * D - день недели, сокращенный вариант
	 * l (L в нижнем регистре) - день недели, полный вариант
	 * m - месяц, число от 1 до 12
	 * M - месяц, сокращенный вариант
	 * F - месяц, полный вариант
	 * y - год в формате ГГ
	 * Y - год в формате ГГГГ
	 * По умолчанию d.m.Y
	 * @param [language] {string} Локаль для вывода строковых значений месяца и дня недели.
	 * Регистр значения не имеет.
	 * По умолчанию en.
	 * @param [returnObject] {boolean} Если установлен в в true,
	 * то вместо форматированной строки вернется сформированный объект Date
	 * @returns {string | Date | boolean}
	 */
	createDate:function(dateString,format,language,returnObject){
		var languageRegexp = /^(en|ru|ua)$/i;
		var now = new Date();
		var createdDate;
		/*По умолчанию возвращаем строку*/
		returnObject = typeof returnObject === "undefined" ? false : returnObject;
		/*Формат по умолчанию - дд.мм.ГГГГ, если входящий формат не определен*/
		format = typeof format === "undefined" ? "d.m.Y" : format;
		/*Язык по умолчанию - en*/
		language = typeof language === "undefined" ? "en" : language;
		language = this.cleanese(language);
		language = languageRegexp.test(language) === false ? "en" : language.toLowerCase();
		/*Формируем объект целевой даты*/
		var dateRegexp = /^([\-+0-9]{0,})*(second|minute|hour|day|week|month|year)$/i;

		if (Object.prototype.toString.call(dateString) === '[object Date]' || dateString instanceof Date){
			createdDate = dateString;
		}else {
			// попробуем распарсить обычную дату из строки
			var parsedDate = this.parseDateString(dateString);
			if(parsedDate !== false){
				createdDate = parsedDate;
			}else{
				var dateComponents = dateRegexp.exec(dateString);
				if (dateComponents){
					/*Если дата указана в формате диапаноза - разбираем ее*/
					var range = typeof dateComponents[1] === "undefined" ? 1 : Number(dateComponents[1]);
					switch (dateComponents[2].toLowerCase()){
						case "second":{
							createdDate = new Date(now.setSeconds(now.getSeconds() + range));
							break;
						}
						case "minute":{
							createdDate = new Date(now.setMinutes(now.getMinutes() + range));
							break;
						}
						case "hour":{
							createdDate = new Date(now.setHours(now.getHours() + range));
							break;
						}
						case "day":{
							createdDate = new Date(now.setDate(now.getDate()+range));
							break;
						}
						case "week":{
							range=range*7;
							createdDate = new Date(now.setDate(now.getDate()+range));
							break;
						}
						case "month":{
							createdDate = new Date(now.setMonth(now.getMonth()+range));
							break;
						}
						case "year":{
							createdDate = new Date(now.setYear(now.getFullYear() + range));
							break;
						}
						default:{
							//console.error("Wrong date range: ", dateString);
							return false;
						}
					}
				}else{
					/*Проверим на текстовое указание диапазона*/
					switch (dateString){
						case "today":{
							createdDate = now;
							break;
						}
						case "yesterday":{
							createdDate = new Date(now.setDate(now.getDate()-1));
							break;
						}
						case "tomorrow":{
							createdDate = new Date(now.setDate(now.getDate()+1));
							break;
						}
						default:{
							//console.error("Wrong day: ", dateString);
							return false;
						}
					}
				}
			}
		}

		if (returnObject){
			return createdDate;
		}else{
			/*Формируем выходную строку в соответствии указанным форматом*/
			/**
			 * Таблица преобразования: (основана на функции date() http://www.php.net/manual/en/function.date.php)
			 * H - часы в формате 24
			 * i - минуты
			 * d - день месяца, число от 1 до 31
			 * D - день недели, сокращенный вариант
			 * l (L в нижнем регистре) - день недели, полный вариант
			 * m - месяц, число от 1 до 12
			 * M - месяц, сокращенный вариант
			 * F - месяц, полный вариант
			 * y - год в формате ГГ
			 * Y - год в формате ГГГГ
			 *
			 * все остальные символы, переданные в строке формата, выводятся как есть
			 * */
			var outputString="";
	        for (var formatIndex=0; formatIndex < format.length; formatIndex++){
			    var convertedValue="";
				switch (format.charAt(formatIndex)){
					case "H" :{
						var hours = createdDate.getHours();
						convertedValue = hours < 10 ? "0" + hours : hours;
						break;
					}
					case "i":{
						var minutes = createdDate.getMinutes();
						convertedValue = minutes < 10 ? "0" + minutes : minutes;
						break;
					}
					case "s":{
						var seconds = createdDate.getSeconds();
						convertedValue = seconds < 10 ? "0" + seconds : seconds;
						break;
					}
					case "d":{
						var dayNumber=createdDate.getDate();
						convertedValue = dayNumber < 10 ? "0" + dayNumber : dayNumber;
						break;
					}
					case "D":{
						var dayOfWeekShort=createdDate.getDay();
						if (this.days.hasOwnProperty(language)){
							convertedValue=this.days[language].short[dayOfWeekShort];
						}else{
							convertedValue=this.days.en.short[dayOfWeekShort];
						}
						break;
					}
					case "l":{
						var dayOfWeekFull=createdDate.getDay();
						if (this.days.hasOwnProperty(language)){
							convertedValue=this.days[language].full[dayOfWeekFull];
						}else{
							convertedValue=this.days.en.full[dayOfWeekFull];
						}
						break;
					}
					case "m":{
						var month=createdDate.getMonth() + 1;
						convertedValue = month < 10 ? "0"+month : month;
						break;
					}
					case "M":{
						var monthShort=createdDate.getMonth();
						if (this.monthes.hasOwnProperty(language)){
							convertedValue=this.monthes[language].short[monthShort];
						}else{
							convertedValue=this.monthes.en.short[monthShort];
						}
						break;
					}
					case "F":{
						var monthFull=createdDate.getMonth();
						if (this.monthes.hasOwnProperty(language)){
							convertedValue=this.monthes[language].full[monthFull];
						}else{
							convertedValue=this.monthes.en.full[monthFull];
						}
						break;
					}
					case "y":{
						var yearShort=createdDate.getFullYear();
						convertedValue=yearShort.toString().substring(2);
						break;
					}
					case "Y":{
						convertedValue=createdDate.getFullYear();
						break;
					}
					default:{
						convertedValue=format.charAt(formatIndex);
						break;
					}
				}
			    outputString=outputString+convertedValue;
			}
			return outputString;
		}
 	},
	
	createDateObj:function(dateString){
		return this.createDate(dateString, undefined, undefined, true);
	},
	/**
	 * Create Date object from dateString.
	 * String format should be Y-m-d H:i:s.
	 * @param {string} dateString
	 */
	parseDateString:function(dateString){
		var dateComponents, year, month, day, hours, seconds, minutes;
		// Formats: "2015-02-03 09:09:09.0000000", "2015-02-03 09:09:09", "2016-05-14T12:01:59", "2016-05-14T12:01:59+02:00", "2016-05-14T12:01:59-02:00"
		var dateRegexp = /^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})([\s|T]([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})(\.[0-9]{1,7}){0,1}){0,1}([+-]([0-9]{1,2}):([0-9]{1,2})){0,1}$/; //2015-02-03 09:09:09.0000000
		// Formats: "09:08:07 14.05.2016", "14.05.2016"
		var dateRegexp2 = /(([0-9]{1,2}):([0-9]{1,2})(:([0-9]{1,2}){0,1}) ){0,1}([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})/;
		if (dateRegexp.test(dateString)){
			dateComponents = dateRegexp.exec(dateString);
			year = parseInt(dateComponents[1]);
			month = parseInt(dateComponents[2]) - 1;
			day = parseInt(dateComponents[3]);
			if (typeof dateComponents[4] !== "undefined"){
				hours = parseInt(dateComponents[5]);
				minutes = parseInt(dateComponents[6]);
				seconds = parseInt(dateComponents[7]);
			}else{
				hours = 0;
				minutes = 0;
				seconds = 0;
			}
		}else if(dateRegexp2.test(dateString)){
			dateComponents = dateRegexp2.exec(dateString);
			hours = typeof dateComponents[2] === "undefined" ? 0 : parseInt(dateComponents[2]);
			minutes = typeof dateComponents[3] === "undefined" ? 0 : parseInt(dateComponents[3]);
			seconds = typeof dateComponents[5] === "undefined" ? 0 : parseInt(dateComponents[5]);
			day = parseInt(dateComponents[6]);
			month = parseInt(dateComponents[7]) - 1;
			year = parseInt(dateComponents[8]);
		}else{
			return false;
		}
		return new Date(year, month, day, hours, minutes, seconds);
	},

	/**
	 * Create Date object from Now() + parsed time from timeString.
	 * String format should be Y-m-d H:i:s.
	 * @param {string} timeString
	 * @returns {Date}
	 */
	parseTimeString:function(timeString){
		var timeRegexp = /([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/i;
		var timeComponens = timeRegexp.exec(timeString);
		var hours = parseInt(timeComponens[1]);
		var minutes = parseInt(timeComponens[2]);
		var secounds = parseInt(timeComponens[3]);
		var today = new Date();
		return new Date(today.getFullYear(), today.getMonth(), today.getDate(), today.getHours() + hours, today.getMinutes() + minutes, today.getSeconds() + secounds);
	},
	validateDate:function(date){
		var dateRegexp = new RegExp(/(([0-9])|([0-2][0-9])|(3[0-1]))([a-z]{3})/i);
		var dateComponents = dateRegexp.exec(date);
		var errorMessage = false;
		if (dateComponents) {
			var day = parseInt(dateComponents[1]);
			var month = dateComponents[5].toUpperCase();
			if (this.monthes.indexOf(month) -1) {
				if (month === "FEB" && day > 29) {
					errorMessage = "Неправильный день месяца";
				}
			}else{
				errorMessage = "Неправильный месяц";
			}
		}else{
			errorMessage = "Неправильный формат даты";
		}
		return errorMessage;
	},
	transliterationTable: {
		"а": {
			code: 1072,
			transliterated: 'a'
		},
		"б": {
			code: 1073,
			transliterated: 'b'
		},
		"в": {
			code: 1074,
			transliterated: 'v'
		},
		"г": {
			code: 1075,
			transliterated: 'g'
		},
		"д": {
			code: 1076,
			transliterated: 'd'
		},
		"е": {
			code: 1077,
			transliterated: 'e'
		},
		"ё": {
			code: 1105,
			transliterated: 'e'
		},
		"ж": {
			code: 1078,
			transliterated: 'zh'
		},
		"з": {
			code: 1079,
			transliterated: 'z'
		},
		"и": {
			code: 1080,
			transliterated: 'i'
		},
		"й": {
			code: 1081,
			transliterated: 'y'
		},
		"к": {
			code: 1082,
			transliterated: 'k'
		},
		"л": {
			code: 1083,
			transliterated: 'l'
		},
		"м": {
			code: 1084,
			transliterated: 'm'
		},
		"н": {
			code: 1085,
			transliterated: 'n'
		},
		"о": {
			code: 1086,
			transliterated: 'o'
		},
		"п": {
			code: 1087,
			transliterated: 'p'
		},
		"р": {
			code: 1088,
			transliterated: 'r'
		},
		"с": {
			code: 1089,
			transliterated: 's'
		},
		"т": {
			code: 1090,
			transliterated: 't'
		},
		"у": {
			code: 1091,
			transliterated: 'u'
		},
		"ф": {
			code: 1092,
			transliterated: 'f'
		},
		"х": {
			code: 1093,
			transliterated: 'h'
		},
		"ц": {
			code: 1094,
			transliterated: 'ts'
		},
		"ч": {
			code: 1095,
			transliterated: 'ch'
		},
		"ш": {
			code: 1096,
			transliterated: 'sh'
		},
		"щ": {
			code: 1097,
			transliterated: 'shch'
		},
		"ъ": {
			code: 1098,
			transliterated: ''
		},
		"ы": {
			code: 1099,
			transliterated: 'y'
		},
		"ь": {
			code: 1100,
			transliterated: ''
		},
		"э": {
			code: 1101,
			transliterated: 'ie'
		},
		"ю": {
			code: 1102,
			transliterated: 'yu'
		},
		"я": {
			code: 1103,
			transliterated: 'ya'
		},
		ї: {
			code: 1111,
			transliterated: 'yi'
		},
		і: {
			code: 1110,
			transliterated: 'i'
		},
		є: {
			code: 1108,
			transliterated: 'ye'
		}
	},
	layoutConversionTable:{
		"a": {
			code:97,
			converted: "ф"
		},
		"b": {
			code:98,
			converted: "и"
		},
		"c": {
			code:99,
			converted: "с"
		},
		"d": {
			code:100,
			converted: "в"
		},
		"e": {
			code:101,
			converted: "у"
		},
		"f": {
			code:102,
			converted: "а"
		},
		"g": {
			code:103,
			converted: "п"
		},
		"h": {
			code:104,
			converted: "р"
		},
		"i": {
			code:105,
			converted: "ш"
		},
		"j": {
			code:106,
			converted: "о"
		},
		"k": {
			code:107,
			converted: "л"
		},
		"l": {
			code:108,
			converted: "д"
		},
		"m": {
			code:109,
			converted: "ь"
		},
		"n": {
			code:110,
			converted: "т"
		},
		"o": {
			code:111,
			converted: "щ"
		},
		"p": {
			code:112,
			converted: "з"
		},
		"q": {
			code:113,
			converted: "й"
		},
		"r": {
			code:114,
			converted: "к"
		},
		"s": {
			code:115,
			converted: "ы"
		},
		"t": {
			code:116,
			converted: "е"
		},
		"u": {
			code:117,
			converted: "г"
		},
		"v": {
			code:118,
			converted: "м"
		},
		"w": {
			code:119,
			converted: "ц"
		},
		"x": {
			code:120,
			converted: "ч"
		},
		"y": {
			code:121,
			converted: "н"
		},
		"z": {
			code:122,
			converted: "я"
		},
		'[':{
			code: 91,
			converted: 'х'
		},
		']':{
			code: 93,
			converted: 'ъ'
		},
		"'":{
			code: 39,
			converted: "э"
		},
		",":{
			code: 44,
			converted: "б"
		},
		".":{
			code: 46,
			converted: "б"
		},
		";":{
			code: 59,
			converted: "ж"
		},
		"`":{
			code: 96,
			converted: "е"
		}
	},
	/**
	 * Транслитерация символа
	 * @param {string | int} char Символ или его код
	 * @param {boolean} isCharCode Флаг, указывающий, был ли передан код символа
	 * @returns {string | boolean} Возвращает транслитерированный символ или false, если исходного символа нет в таблице транслитерации
	 */
	transliterate: function(char,isCharCode){
		isCharCode = typeof isCharCode === 'undefined' ? false : isCharCode;
		if (isCharCode){
			char = Number(char);
			for (var russianSymbol in this.transliterationTable){
				if (this.transliterationTable.hasOwnProperty(russianSymbol)){
					if (this.transliterationTable[russianSymbol]['code'] === char){
						return this.transliterationTable[russianSymbol]['transliterated'];
					}
				}
			}
			return false;
		}else{
			char = char.toLowerCase();
			if (this.transliterationTable.hasOwnProperty(char)){
				return this.transliterationTable[char]['transliterated'];
			}else{
				if (char === ' ') return '-';
				return char;
			}
		}
	},
	/**
	 * Convert one symbol from EN to RU keyboard layout (by symbol or JS keycode code)
	 * @param {string} char
	 * @param {boolean} isCharCode
	 * @returns {string} converted char
	 */
	convertLayoutSymbol: function(char,isCharCode){
		isCharCode = typeof isCharCode === 'undefined' ? false : isCharCode;
		if (isCharCode){
			char = Number(char);
			for (var latinSymbol in this.layoutConversionTable){
				if (this.layoutConversionTable.hasOwnProperty(latinSymbol)){
					if (this.layoutConversionTable[latinSymbol]['code'] === char){
						return this.layoutConversionTable[latinSymbol]['converted'];
					}
				}
			}
			return false;
		}else{
			char = char.toLowerCase();
			if (this.layoutConversionTable.hasOwnProperty(char)){
				return this.layoutConversionTable[char]['converted'];
			}else{
				return char;
			}
		}
	},
	/**
	 * Convert full string from EN to RU keyboard layouts
	 * @param string
	 * @returns {string}
	 */
	convertLayoutFullString:function(string){
		if (typeof string === 'undefined') return '';
		var convertedString = '';
		for (var charsCnt = 0; charsCnt < string.length; charsCnt++){
			convertedString = convertedString + this.convertLayoutSymbol(string.charAt(charsCnt),false);
		}
		return convertedString;
	},
	/**
	 * Транслитерация строки
	 * @param string
	 * @returns {string}
	 */
	transliterateFullString: function(string){
		if (typeof string === 'undefined') return '';
		var trasliteratedString = '';

		for (var charsCnt = 0; charsCnt < string.length; charsCnt++){
			trasliteratedString = trasliteratedString + this.transliterate(string.charAt(charsCnt),false);
		}

		return trasliteratedString.replace(/\W{1,}/g, '-');
	},

	/**
	 * Преобразование текста в html код
	 * https://gist.github.com/CatTail/4174511
	 *
	 * @param str
	 * @returns {XML|string|void|*}
	 */
	decodeHtmlEntity: function(str) {
		return str.replace(/&#(\d+);/g, function(match, dec) {
			return String.fromCharCode(dec);
		});
	},

	/**
	 * Преобразование html кода в текст
	 * https://gist.github.com/CatTail/4174511
	 *
	 * @param str
	 * @returns {string}
	 */
	encodeHtmlEntity: function(str) {
		var buf = [];
		for (var i=str.length-1;i>=0;i--) {
			buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
		}
		return buf.join('');
	},

	/**
	 * Преобразование строки в base64
	 * https://github.com/kvz/phpjs/blob/master/functions/url/base64_encode.js
	 *
	 * @param {string} data
	 * @returns {*}
	 */
	base64_encode: function(data) {
		var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
		var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
			ac = 0,
			enc = '',
			tmp_arr = [];

		if (!data) {
			return data;
		}

		data = unescape(encodeURIComponent(data));

		do {
			// pack three octets into four hexets
			o1 = data.charCodeAt(i++);
			o2 = data.charCodeAt(i++);
			o3 = data.charCodeAt(i++);

			bits = o1 << 16 | o2 << 8 | o3;

			h1 = bits >> 18 & 0x3f;
			h2 = bits >> 12 & 0x3f;
			h3 = bits >> 6 & 0x3f;
			h4 = bits & 0x3f;

			// use hexets to index into b64, and append result to encoded string
			tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
		} while (i < data.length);

		enc = tmp_arr.join('');

		var r = data.length % 3;

		return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
	},

	/**
	 * Декодирование base64 строки
	 * https://github.com/kvz/phpjs/blob/master/functions/url/base64_decode.js
	 *
	 * @param {string} data
	 * @returns {*}
	 */
	base64_decode: function (data) {
		var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
		var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
			ac = 0,
			dec = '',
			tmp_arr = [];

		if (!data) {
			return data;
		}

		data += '';

		do {
			// unpack four hexets into three octets using index points in b64
			h1 = b64.indexOf(data.charAt(i++));
			h2 = b64.indexOf(data.charAt(i++));
			h3 = b64.indexOf(data.charAt(i++));
			h4 = b64.indexOf(data.charAt(i++));

			bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;

			o1 = bits >> 16 & 0xff;
			o2 = bits >> 8 & 0xff;
			o3 = bits & 0xff;

			if (h3 == 64) {
				tmp_arr[ac++] = String.fromCharCode(o1);
			} else if (h4 == 64) {
				tmp_arr[ac++] = String.fromCharCode(o1, o2);
			} else {
				tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
			}
		} while (i < data.length);

		dec = tmp_arr.join('');

		return decodeURIComponent(escape(dec.replace(/\0+$/, '')));
	},
	/**
	 * @source https://github.com/kvz/phpjs/blob/master/functions/strings/rtrim.js
	 * @param str
	 * @param charlist
	 * @returns {string|*}
	 */
	rtrim: function(str, charlist) {
		charlist = !charlist ? ' \\s\u00A0' : (charlist + '')
			.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\\$1');
		var re = new RegExp('[' + charlist + ']+$', 'g');
		return (str + '')
			.replace(re, '');
	},
	/**
	 * @source https://github.com/kvz/phpjs/blob/master/functions/strings/ltrim.js
	 * @param str
	 * @param charlist
	 * @returns {string|*}
	 */
	ltrim: function(str, charlist) {
		charlist = !charlist ? ' \\s\u00A0' : (charlist + '')
			.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
		var re = new RegExp('^[' + charlist + ']+', 'g');
		return (str + '')
			.replace(re, '');
	},
	/**
	 * Range() function implementation
	 * @source http://stackoverflow.com/questions/3895478/does-javascript-have-a-method-like-range-to-generate-an-array-based-on-suppl
	 *
	 * @param start
	 * @param end
	 * @param step
	 * @returns {Array}
	 */
	range: function(start, end, step) {
		var range = [];
		var typeofStart = typeof start;
		var typeofEnd = typeof end;

		if (step === 0) {
			throw TypeError("Step cannot be zero.");
		}

		if (typeofStart == "undefined" || typeofEnd == "undefined") {
			throw TypeError("Must pass start and end arguments.");
		} else if (typeofStart != typeofEnd) {
			throw TypeError("Start and end arguments must be of same type.");
		}

		typeof step == "undefined" && (step = 1);

		if (end < start) {
			step = -step;
		}

		if (typeofStart == "number") {

			while (step > 0 ? end >= start : end <= start) {
				range.push(start);
				start += step;
			}

		} else if (typeofStart == "string") {

			if (start.length != 1 || end.length != 1) {
				throw TypeError("Only strings with one character are supported.");
			}

			start = start.charCodeAt(0);
			end = end.charCodeAt(0);

			while (step > 0 ? end >= start : end <= start) {
				range.push(String.fromCharCode(start));
				start += step;
			}

		} else {
			throw TypeError("Only string and number types are supported");
		}

		return range;

	},
	pluralize: function(number, replacements) {
		// [ день, дня, дней]

		number = parseInt(number);
		var numberString = number.toString(),
			numberLastDigit = parseInt(numberString.charAt(numberString.length - 1)),
			numberFirstDigit = parseInt(numberString.charAt(0));
		if (
			(number === 1 || numberLastDigit == 1) &&
			number != 11
		) {
			return replacements[0];
		} else if (
			(number >= 2 && number <= 4) ||
			(
				numberLastDigit >= 2 &&
				numberLastDigit <= 4 &&
				numberFirstDigit != 1
			)
		) {
			return replacements[1];
		} else {
			return replacements[2];
		}
	}
};