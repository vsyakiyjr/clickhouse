@extends('cpanel.cpanel')

@section('content')

<div class="redirects container-fluid"
	 ng-controller="redirectsCtrl">


	<div class="clearfix padding">

		<form method="post"
			  name="redirectsForm"
			  class="form-group-sm"
			  ng-submit="redirectsForm.$valid && pageChanged(1)">
			<div class="form-group">
				<input type="text"
					   id="search"
					   name="search"
					   required
					   tabindex="1"
					   autocomplete="off"
					   ng-model="filters.search"
					   placeholder="Поиск по алиасу"
					   class="form-control"
				/>
			</div>
			<button class="btn btn-sm btn-success"
					ng-disabled="redirectsForm.$invalid">
				Найти
			</button>
			<button class="btn btn-sm btn-default"
					ng-disabled="!filters.search"
					ng-click="filters.search = '' && pageChanged(1)"
					type="button">
				 Сбросить
			</button>

			<button class="btn btn-primary"
					type="button"
					ng-click="addRedirect()">
				<span class="fa fa-plus"></span>
				Добавить
			</button>
		</form>
	</div>

	<ui-simple-pagination ng-show="redirects.length > 0"></ui-simple-pagination>

	<div class="table-responsive" ng-show="redirects.length > 0">
		<table class="table table-bordered table-hover table-striped redirects-table form-group-sm">
			<thead>
				<tr>
					<th>Alias</th>
					<th>Страница</th>
					<th>Тип совпадения</th>
					<th>Создан</th>
					<th>Комментарий</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="redirect in redirects track by redirect.id">
					<td>
						<input type="text"
							   class="form-control"
							   required
							   ng-model="redirect.alias"
							   ng-change="update(redirect)"
							   ng-model-options="{updateOn: 'blur'}"
						/>
					</td>
					<td>
						<a ng-href="@{{redirect.page.fullpath}}"
						   target="_blank"
						   rel="nofollow">
							@{{::redirect.page.title}}
						</a>
					</td>
					<td uib-dropdown>
						<button class="btn btn-sm btn-default" uib-dropdown-toggle="">
							@{{redirect.match_type}}
							<span class="fa fa-caret-down"></span>
						</button>
						<ul class="dropdown-menu" uib-dropdown-menu>
							<li ng-repeat="(type, name) in matchTypes"
								ng-class="{'active': redirect.match_type == type}">
								<a href="#"
								   ng-click="redirect.match_type = type && update(redirect)">
									@{{name}}
								</a>
							</li>
						</ul>
					</td>
					<td>@{{::redirect.created_at | formatDate: 'd.m.Y H:i'}}</td>
					<td>
						<input type="text"
							   class="form-control"
							   ng-model="redirect.comment"
							   ng-change="update(redirect)"
							   ng-model-options="{updateOn: 'blur'}"
						/>
					</td>
					<td>
						<a href="" ng-click="delete(redirect)">
							<span class="fa fa-trash text-danger fa-2x"></span>
						</a>

					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<ui-simple-pagination ng-show="redirects.length > 0"></ui-simple-pagination>
</div>

@endsection