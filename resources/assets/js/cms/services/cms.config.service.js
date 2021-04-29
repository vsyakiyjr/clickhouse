angular.module('app.cms').service('CmsConfig', ConfigService);

function ConfigService() {
	return {
		//шаблоны обычных страниц
		presets:         {
			plainPage:        {
				label:  'Обычная страница',
				blocks: [
					'text'
				]
			},
			popularDirection: {
				label:  'Популярные направления',
				blocks: [
					'text',
					'links'
				]
			}
		},
		//элементы для обычных страниц
		pageElements:    {
			//блок с интерпретируемым html
			html:  {
				type:     'html',
				label:    'HTML блок',
				title:    '',
				icon:     'fa fa-code',
				template: 'textBlock.html',
				content:  'Содержимое',
				visible:  true,
				config:   {}
			},
			// блок с неинтерпретируемым html
			text:  {
				type:     'text',
				label:    'Текстовый блок',
				title:    '',
				content:  'Содержимое',
				icon:     'fa fa-file-text-o',
				template: 'textBlock.html',
				visible:  true,
				config:   {}
			},
			// поисковая форма, ведет на поиск на главную
			form:  {
				type:     'form',
				label:    'Форма поиска',
				title:    '',
				content:  '',
				icon:     'fa fa-search',
				template: 'searchForm.html',
				visible:  true,
				config:   {
					type:          'RT',
					class:         'economy',
					onlyDirect:    false,
					from:          'KBP',
					to:            'PAR',
					departureDate: Utilities.createDate('+7day'),
					returnDate:    Utilities.createDate('+14day'),
					passengers:    {
						ADT: '1',
						CNN: '0',
						INF: '0'
					},
					segments:      []
				}
			},
			//Ссылки на внутренние страницы сайта из определенной директории
			directoryLinks: {
				type:     'directoryLinks',
				label:    'Перелинковка',
				title:    '',
				content:  '',
				icon:     'fa fa-list',
				template: 'directoryLinks.html',
				visible:  true,
				config:   {
					columns: 2,
					pages: 8,
					directory_id: null,
					show_right_block: true,
					text_cut_1_col: 300,
					text_cut_2_col: 125,
					links_in_right_block: 20
				}
			},
			feedback: {
				type:     'feedback',
				label:    'Блок с отправкой формы отзыва',
				title:    '',
				content:  '',
				icon:     'fa fa-question',
				template: 'feedback.html',
				visible:  true,
				config: {
					emails: [],
					title:  'Форма для отправки резюме',
					text:   'Мы ждём Ваше резюме. Прикрепите файл в формате *.pdf, *.doc, *.docx или *.txt',
					email_label: 'Укажите ваш электронный адрес',
					message_label: 'Укажите кратко, на какую вакансию Вы отправляете резюме',
					attachment_label: 'Прикрепите файл Вашего резю в формате *.pdf, *.doc, *.docx или *.txt',
					button_text: 'Отправить нам Ваше резюме',
					successful_submit_text: 'Мы получили Ваше резюме и свяжемся с Вами в ближайшее время',
					submitting_text: 'Идёт отправка документа',
				}
			}
			/*,
			// блок с набором произвольных ссылок
			customLinks:{
				type:     'internalLinks',
				label:    'Внутренние ссылки',
				title:    '',
				content:  '',
				icon:     'fa fa-list-alt',
				template: 'directoryLinks.html',
				visible:  true,
				config: {
					pages: 8,
					columns: 2,
					text_cut_1_col: 300,
					text_cut_2_col: 125,
					pages_ids: []
				}
			}*/
		}
	}
}