angular.module('app.cms').directive('pageContent', PageContentDirective);

PageContentDirective.$inject = ['$uibModal', 'CmsConfig', 'Message', 'uiSimpleModal'];

function PageContentDirective($uibModal, CmsConfig, Message, uiSimpleModal) {
	'use strict';

	return {
		restrict: "EA",
		replace: true,
		templateUrl: "/templates/admin/modules/cms/directives/pageContent.html",
		scope:{
			content: "=",
			host: "@"
		},
		link: function (scope, elem, attrs, ctrl) {
			
			scope.availablePageElements = CmsConfig.pageElements;

			scope.editBlock = function(block, $index){
				let blockToEdit = angular.copy(block);

				blockToEdit.config.pages = parseInt(blockToEdit.pages) || 8;
				blockToEdit.config.show_right_block = typeof blockToEdit.show_right_block == "undefined" ? true : !!blockToEdit.show_right_block;
				blockToEdit.config.columns = parseInt(blockToEdit.columns) || 2;
				blockToEdit.config.text_cut_1_col = parseInt(blockToEdit.text_cut_1_col) || 300;
				blockToEdit.config.text_cut_2_col = parseInt(blockToEdit.text_cut_2_col) || 125;
				blockToEdit.config.links_in_right_block = parseInt(blockToEdit.links_in_right_block) || 20;

				uiSimpleModal({
					title:        `Редактирование настроек блока <strong>${block.title}</strong>`,
					windowClass:  'cms-edit-block',
					size: 'lg',
					templateUrl:  '/templates/admin/modules/cms/editBlockModal.html',
					returnData:   true,
					wrapTemplate: false,
					showTitleClose: true,
					data: {
						block: blockToEdit
					}
				}).then(function(result){
					if(result.button == 'save'){

						scope.content.block[$index] = result.data.block;
						Message.pushSuccess('Настройки блока успешно сохранены!')
					}
				});
			};

			/**
			 * Add new block to page
			 * @param {string} blockType
			 */
			scope.addBlock = function (blockType) {
				if (typeof scope.availablePageElements[blockType] === "undefined") return;
				
				let newBlock = clone(scope.availablePageElements[blockType]);
				scope.content.block = Array.isArray(scope.content.block) ? scope.content.block : [];
				
				let lastItemIndex = scope.content.block.length - 1;
				let blockToEdit = scope.content.block[lastItemIndex] || newBlock;

				if (blockType == 'directoryLinks') {
					scope.findDirectoryForLinksBlock(blockToEdit);
				} else {
					scope.content.block.push(newBlock);
				}
			};

			scope.findDirectoryForLinksBlock = function (block) {
				var modalInstance = $uibModal.open({
					animation: true,
					templateUrl: '/templates/admin/modules/cms/directives/findDirectoryDialog.html',
					controller: 'searchDirectoryModalCtrl',
					resolve: {
						Host: function () {
							return scope.host
						}
					}
				});
				
				modalInstance.result.then(function (directory) {
					block.directory = directory;
					block.title = directory.description;
					block.config.directory_id = directory.id;

					scope.content.block.push(block);
				});
			};

			scope.setPosition = function(index, position){
				return scope.content.block[index].position = position;
			};

			scope.getPosition = function(index){
				return scope.content.block[index].position;
			};

			/**
			 * Перемещение элементов в левом и правом контейнерах
			 *
			 * @param oldIndex {int} Текущий индекс элемента
			 * @param newIndex {string} Новый индекс элемента
			 *
			 * @returns {boolean}
			 */
			scope.moveElement = function (oldIndex, newIndex) {
				if (angular.isNumber(oldIndex) === false) return false;
				switch (newIndex) {
					case "up": {
						newIndex = oldIndex - 1;
						break;
					}
					case "down": {
						newIndex = oldIndex + 1;
						break;
					}
					default: {
						console.error("wrong new index submitted");
						return false;
					}
				}
				
				Utilities.arrayMove(scope.content.block, oldIndex, newIndex);
				return true;
			};
			
			
			/**
			 * Удаление элемента со страницы
			 *
			 * @param arrayIndex {int} индекс элемента в массиве блока
			 */
			scope.removeElement = function (arrayIndex) {
				if (window.confirm('Вы уверены?')) {
					scope.content.block.splice(arrayIndex, 1);
				}
			};
			
		}
	}
}