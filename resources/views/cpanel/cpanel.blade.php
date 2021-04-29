<?php
$title = $page->title ?? $title;
?>

<!doctype html>
<html lang="ru" ng-app="appCpanel" class="cms_editor">
<head>
	<meta charset="UTF-8">
	<title>{{$title ?? 'CMS'}}</title>
	<base href="{{ URL::to('/') }}/" />

	<script
		src="https://code.jquery.com/jquery-2.2.4.js"
		integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI="
		crossorigin="anonymous"></script>

	<script src="/cms-assets/references.js"></script>
	<script src="/cms-assets/cms-lib.js"></script>
	<script src="/cms-assets/cms.js"></script>
	<link rel="stylesheet" href="{{mix('css/app.css')}}">

	<link rel="stylesheet" href="/cms-assets/cms.css" media="all" />
	<script src="/js/ckeditor/ckeditor.js"></script>
</head>
<body>
	<header id="header">
		<div class="container-fluid my-1">
			<a href="/" class="logo">
				<img src="/images/logo.png"
					 alt="Ikeamania"
					 style="width: 150px"
					 title="Ikeamania"
				/>
			</a>
		</div>
	</header>

	<main class="container-fluid cpanel-container">
		@if($errors->any())
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>@{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		@yield('content')
		<flash-message></flash-message>
	</main>
	<loading class="main-loading" title="Загрузка..."></loading>
	<message id="main-message"></message>

	<footer>

	</footer>
</body>
</html>