@extends('cpanel.cpanel')
@section('content')
	<div class="editor-wrap" ng-controller="Cms as cmsCtrl">
		<!-- Controls START -->
		<current-page-title></current-page-title>
		<div class="toolbar">
			<div class="toolbar-item page-controls">
				<button class="btn btn-primary"
						ng-disabled="pageSettings.$invalid || $root.loading || !page"
						ng-click="savePage(page)">
					<span class="glyphicon glyphicon-floppy-disk"></span>
					Сохранить
				</button>
			</div>
			<history-dropdown></history-dropdown>
		</div>
		<!-- Controls END -->

		<editor></editor>

	</div>
@endsection