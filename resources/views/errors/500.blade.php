@extends('redesign.basic')
@section('content')
	<div class="error-block">
		<div class="error-title">Что-то пошло не так…</div>
		<div class="error-img">
			<img src="/images/errors/500.png"
				 alt="">
		</div>
		<div class="error-message">
			Попробуйте обновить страницу или вернуться назад и выполнить действие заново. <br> Если ошибка повторится, пожалуйста, сообщите нам любым удобным способом.
		</div>
	</div>
@endsection